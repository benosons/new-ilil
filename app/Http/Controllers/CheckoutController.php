<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

use App\Models\Voucher; // Added

class CheckoutController extends Controller
{
    public function showForm()
    {
        $products = Product::active()->sorted()->get();
        return view('checkout', [
            'products' => $products,
            'midtransClientKey' => config('midtrans.client_key'),
            'midtransSnapUrl' => config('midtrans.snap_url'),
        ]);
    }

    public function checkVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'items' => 'required|array', 
        ]);

        // Find active voucher
        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode tidak ditemukan.']);
        }

        if (!$voucher->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Kode tidak valid, kadaluarsa, atau habis.']);
        }

        // Calculate current subtotal
        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $subtotal += $product->price * $item['quantity'];
            }
        }

        // Calculate discount
        $discount = 0;
        if ($voucher->type == 'fixed') {
            $discount = $voucher->value;
        } else {
            $discount = $subtotal * ($voucher->value / 100);
        }

        if ($discount > $subtotal) $discount = $subtotal;

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'discount' => $discount,
            'discount_formatted' => 'Rp ' . number_format($discount, 0, ',', '.'),
            'type' => $voucher->type,
            'value' => $voucher->value
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                $subtotal = 0;
                $orderItems = [];

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok produk {$product->name} tidak mencukupi. Sisa stok saat ini: {$product->stock} pcs.");
                    }

                    $itemSubtotal = $product->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $itemSubtotal,
                    ];

                    // Deduct stock
                    $product->decrement('stock', $item['quantity']);
                }

                // Apply Voucher
                $discountAmount = 0;
                $voucherCode = null;

                if ($request->voucher_code) {
                    $voucher = Voucher::where('code', $request->voucher_code)->active()->first();
                    if ($voucher && $voucher->isValid()) {
                        $voucherCode = $voucher->code;
                        if ($voucher->type == 'fixed') {
                            $discountAmount = $voucher->value;
                        } else {
                            $discountAmount = $subtotal * ($voucher->value / 100);
                        }
                        if ($discountAmount > $subtotal) $discountAmount = $subtotal;
                        
                        // Increment usage
                        $voucher->increment('used_count');
                    }
                }

                $total = $subtotal - $discountAmount; // + shipping_cost (0 initially)

                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'customer_address' => $request->customer_address,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'voucher_code' => $voucherCode,
                    'shipping_cost' => 0,
                    'total' => $total,
                    'status' => 'pending',
                    'payment_method' => 'midtrans',
                ]);

                foreach ($orderItems as $item) {
                    $order->items()->create($item);
                }

                return $order;
            });
            
            // ... (rest of the logic: email, midtrans token) ...

            // Send confirmation email
            if ($order->customer_email) {
                try {
                    Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
                } catch (\Exception $e) {
                    Log::error('Failed to send order email: ' . $e->getMessage());
                }
            }

            // Generate Midtrans Snap Token
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $midtransItems = [];
            foreach ($order->items as $item) {
                $midtransItems[] = [
                    'id' => 'PROD-' . $item->product_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product_name,
                ];
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => $order->total,
                ],
                'item_details' => $midtransItems,
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email ?? 'customer@keripikilil.com',
                    'phone' => $order->customer_phone,
                    'shipping_address' => [
                        'first_name' => $order->customer_name,
                        'phone' => $order->customer_phone,
                        'address' => $order->customer_address ?? '-',
                    ],
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['midtrans_snap_token' => $snapToken]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Midtrans Notification Webhook
     */
    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new \Midtrans\Notification();
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? 'accept';

            $order = Order::where('order_number', $orderId)->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                if ($fraudStatus === 'accept') {
                    $order->update([
                        'status' => 'paid',
                        'midtrans_transaction_id' => $notification->transaction_id,
                        'paid_at' => now(),
                    ]);

                    // Send payment success email
                    if ($order->customer_email) {
                        try {
                            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
                        } catch (\Exception $e) {
                            Log::error('Failed to send payment email: ' . $e->getMessage());
                        }
                    }
                }
            } elseif ($transactionStatus === 'pending') {
                $order->update(['status' => 'pending']);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                if ($order->status !== 'cancelled') {
                    // Restore stock
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                    $order->update(['status' => 'cancelled']);
                }
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function success()
    {
        return view('checkout-result', [
            'status' => 'success',
            'title' => 'Pembayaran Berhasil! ✅',
            'message' => 'Terima kasih! Pesanan kamu sedang kami proses.',
        ]);
    }

    public function failed()
    {
        return view('checkout-result', [
            'status' => 'failed',
            'title' => 'Pembayaran Gagal ❌',
            'message' => 'Silakan coba lagi atau hubungi kami via WhatsApp.',
        ]);
    }
}
