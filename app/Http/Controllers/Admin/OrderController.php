<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with('items');

        if ($request->search) {
             $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_phone', 'like', "%{$request->search}%");
             });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Status counts for tabs
        $statuses = ['all', 'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
        $statusCounts = [];
        foreach ($statuses as $s) {
            if ($s == 'all') {
                $statusCounts[$s] = Order::count();
            } else {
                $statusCounts[$s] = Order::where('status', $s)->count();
            }
        }

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function export()
    {
        $filename = "laporan-penjualan-" . date('Y-m-d-His') . ".csv";
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            
            // Header Row
            fputcsv($handle, [
                'No Order', 'Tanggal', 'Nama Customer', 'No HP', 'Status', 
                'Total Produk', 'Ongkir', 'Diskon', 'Total Bayar', 'Metode Bayar', 'Kurir', 'Resi'
            ]);

            // Data Rows
            $orders = Order::with('items')->latest()->chunk(100, function($orders) use ($handle) {
                foreach ($orders as $order) {
                    $itemNames = $order->items->map(fn($i) => "{$i->quantity}x {$i->product_name}")->join('; ');
                    
                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->customer_name,
                        $order->customer_phone,
                        ucfirst($order->status),
                        $itemNames,
                        $order->shipping_cost,
                        $order->discount_amount,
                        $order->total,
                        $order->payment_method,
                        $order->shipping_courier,
                        $order->tracking_number
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        $updateRel = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'paid' && !$order->paid_at) {
            $updateRel['paid_at'] = now();
        }
        
        if ($data['status'] === 'shipped' && !$order->shipped_at) {
            $updateRel['shipped_at'] = now();
        }

        if ($data['status'] === 'cancelled' && $order->status !== 'cancelled') {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        $order->update($updateRel);

        return back()->with('success', "Status pesanan #{$order->order_number} diperbarui ke {$data['status']}.");
    }

    public function updateTracking(Request $request, Order $order)
    {
        $data = $request->validate([
            'shipping_courier' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->update([
            'shipping_courier' => $data['shipping_courier'],
            'tracking_number' => $data['tracking_number'],
            'status' => 'shipped',
            'shipped_at' => $order->shipped_at ?? now(),
        ]);

        return back()->with('success', 'Resi berhasil diinput. Status pesanan berubah menjadi Shipped.');
    }

    public function updateShipping(Request $request, Order $order)
    {
        $data = $request->validate([
            'shipping_cost' => 'required|integer|min:0',
        ]);

        $order->shipping_cost = $data['shipping_cost'];
        $order->total = $order->subtotal + $order->shipping_cost;
        $order->save();

        return back()->with('success', 'Ongkir diperbarui. Total pesanan dikalkulasi ulang.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
