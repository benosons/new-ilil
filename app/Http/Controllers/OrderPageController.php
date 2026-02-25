<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StandaloneOrder;
use App\Models\Voucher;
use Illuminate\Http\Request;

class OrderPageController extends Controller
{
    public function index()
    {
        // Get all active products for the dropdown
        $products = Product::active()->inStock()->sorted()->get();
        return view('order-page', compact('products'));
    }

    public function checkWa(Request $request)
    {
        $waNumber = $request->query('wa_number');
        if (!$waNumber) {
            return response()->json(['exists' => false]);
        }

        $order = StandaloneOrder::where('wa_number', $waNumber)
                                ->where('status', 'pending')
                                ->first();

        if ($order) {
            return response()->json([
                'exists' => true,
                'name' => $order->name,
                'email' => $order->email,
            ]);
        }

        return response()->json(['exists' => false]);
    }
    public function checkVoucher(Request $request)
    {
        $voucherCode = $request->input('voucher_code');
        $subtotal = $request->input('subtotal', 0);

        if (!$voucherCode) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak valid.']);
        }

        $voucher = Voucher::where('code', $voucherCode)->where('is_active', true)->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Voucher tidak ditemukan atau tidak aktif.']);
        }

        if (!$voucher->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah kadaluarsa atau kuota telah habis.']);
        }

        $discountAmount = 0;
        if ($voucher->type === 'percent') {
            $discountAmount = $subtotal * ($voucher->value / 100);
        } else {
            $discountAmount = $voucher->value;
        }

        // Prevent discount from being larger than subtotal
        $discountAmount = min($discountAmount, $subtotal);

        return response()->json([
            'valid' => true,
            'code' => $voucher->code,
            'discount' => $discountAmount,
            'message' => 'Voucher berhasil digunakan.'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'wa_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'products' => 'required|array',
            'products.*' => 'nullable|integer|min:0',
            'voucher_code' => 'nullable|string'
        ]);

        $selectedProducts = array_filter($validated['products'] ?? [], function($qty) {
            return $qty > 0;
        });

        if (empty($selectedProducts)) {
            return redirect()->back()->withInput()->withErrors(['products' => 'Silakan pilih minimal 1 produk pesanan.']);
        }

        // Validate against actual stock
        foreach ($selectedProducts as $productId => $quantity) {
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->back()->withInput()->withErrors(['products' => 'Produk tidak ditemukan.']);
            }
            if ($product->stock < $quantity) {
                return redirect()->back()->withInput()->withErrors(['products' => "Mohon maaf, stok produk {$product->name} tidak mencukupi. Sisa stok saat ini: {$product->stock} pcs."]);
            }
        }

        // Check for existing pending order
        $order = StandaloneOrder::where('wa_number', $validated['wa_number'])
                                ->where('status', 'pending')
                                ->first();

        $isMerge = false;
        if ($order) {
            // Update name and email if provided
            $order->update([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? $order->email,
            ]);
            $isMerge = true;
        } else {
            // Create new order
            $order = new StandaloneOrder();
            $order->name = $validated['name'];
            $order->wa_number = $validated['wa_number'];
            $order->email = $validated['email'];
            $order->total_price = 0;
            $order->status = 'pending';
            $order->save();
        }

        $totalPrice = $isMerge ? $order->items->sum('subtotal') : 0; // Calculate base raw total from existing items if merged
        
        foreach ($selectedProducts as $productId => $quantity) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
                // Deduct stock for the ordered quantity
                $product->decrement('stock', $quantity);
                // Check if item already exists in the order
                $existingItem = $order->items()->where('product_id', $productId)->first();
                
                if ($existingItem) {
                    // Update quantity
                    $newQuantity = $existingItem->quantity + $quantity;
                    $newSubtotal = $product->price * $newQuantity;
                    
                    // Subtract old subtotal and add new subtotal to total price
                    $totalPrice = $totalPrice - $existingItem->subtotal + $newSubtotal;
                    
                    $existingItem->update([
                        'quantity' => $newQuantity,
                        'subtotal' => $newSubtotal,
                    ]);
                } else {
                    // Create new item
                    $subtotal = $product->price * $quantity;
                    $totalPrice += $subtotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);
                }
            }
        }
        $order->update(['total_price' => $totalPrice]); // Update raw total_price first

        // Voucher Validation
        $voucherCode = $validated['voucher_code'] ?? null;
        $discountAmount = 0;

        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->where('is_active', true)->first();
            if ($voucher && $voucher->isValid()) {
                if ($voucher->type === 'percent') {
                    $discountAmount = $totalPrice * ($voucher->value / 100);
                } else {
                    $discountAmount = $voucher->value;
                }
                
                // Prevent discount from being larger than subtotal
                $discountAmount = min($discountAmount, $totalPrice);
                
                // If it's a new voucher application
                if ($order->voucher_code !== $voucher->code) {
                    $voucher->increment('used_count');
                }
                
                $order->voucher_code = $voucher->code;
                $order->discount_amount = $discountAmount;
            } else {
                return redirect()->back()->withInput()->withErrors(['voucher_code' => 'Voucher tidak valid atau kuota telah habis.']);
            }
        }

        // Apply discount to total_price
        $finalTotal = max(0, $totalPrice - $discountAmount);
        $order->total_price = $finalTotal;
        $order->save();

        if ($isMerge) {
            return redirect()->route('order-page.index')->with('success', 'Pesanan tambahan Anda berhasil digabungkan dengan pesanan sebelumnya! Tim kami akan segera menghubungi Anda via WhatsApp.');
        }

        return redirect()->route('order-page.index')->with('success', 'Pesanan Anda berhasil dikirim! Tim kami akan segera menghubungi Anda via WhatsApp.');
    }
}
