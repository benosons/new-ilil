<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        if ($request->has('order_number')) {
            $order = Order::where('order_number', $request->order_number)->with('items')->first();
        }

        return view('tracking', compact('order'));
    }
}
