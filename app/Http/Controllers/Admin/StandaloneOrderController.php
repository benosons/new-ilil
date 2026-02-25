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

        $standaloneOrder->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
