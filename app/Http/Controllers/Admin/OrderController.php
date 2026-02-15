<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('order_number', 'like', "%{$s}%")
                ->orWhere('customer_name', 'like', "%{$s}%")
                ->orWhere('customer_phone', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
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
