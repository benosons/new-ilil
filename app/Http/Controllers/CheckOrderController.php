<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StandaloneOrder;

class CheckOrderController extends Controller
{
    public function index()
    {
        return view('cek-pesanan');
    }

    public function search(Request $request)
    {
        $request->validate([
            'wa_number' => 'required|string',
        ]);

        $waNumber = $request->wa_number;

        // Fetch the newest active or pending order first, or just the latest order for this WA
        $order = StandaloneOrder::with('items.product')
                                ->where('wa_number', $waNumber)
                                ->latest()
                                ->first();

        if (!$order) {
            return redirect()->back()->withInput()->with('error', 'Pesanan dengan Nomor WhatsApp tersebut tidak ditemukan.');
        }

        return view('cek-pesanan', compact('order', 'waNumber'));
    }
}
