<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandaloneOrder;
use Illuminate\Http\Request;

class StandaloneOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = StandaloneOrder::with('items.product')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('wa_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.standalone-orders.index', compact('orders'));
    }

    public function show(StandaloneOrder $standaloneOrder)
    {
        $standaloneOrder->load('items.product');
        return view('admin.standalone-orders.show', compact('standaloneOrder'));
    }

    public function updateStatus(Request $request, StandaloneOrder $standaloneOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processed,completed,cancelled',
        ]);

        if ($validated['status'] === 'cancelled' && $standaloneOrder->status !== 'cancelled') {
            // Restore stock
            foreach ($standaloneOrder->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
            // Restore voucher quota if used
            if ($standaloneOrder->voucher_code) {
                $voucher = \App\Models\Voucher::where('code', $standaloneOrder->voucher_code)->first();
                if ($voucher && $voucher->used_count > 0) {
                    $voucher->decrement('used_count');
                }
            }
        }

        $standaloneOrder->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
