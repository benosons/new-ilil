<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StandaloneOrder;
use Illuminate\Http\Request;

class OrderPageController extends Controller
{
    public function index()
    {
        // Get all active products for the dropdown
        $products = Product::active()->sorted()->get();
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'wa_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'products' => 'required|array',
            'products.*' => 'nullable|integer|min:0',
        ]);

        $selectedProducts = array_filter($validated['products'] ?? [], function($qty) {
            return $qty > 0;
        });

        if (empty($selectedProducts)) {
            return redirect()->back()->withInput()->withErrors(['products' => 'Silakan pilih minimal 1 produk pesanan.']);
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

        $totalPrice = $order->total_price;
        
        foreach ($selectedProducts as $productId => $quantity) {
            $product = \App\Models\Product::find($productId);
            if ($product) {
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

        $order->update(['total_price' => $totalPrice]);

        if ($isMerge) {
            return redirect()->route('order-page.index')->with('success', 'Pesanan tambahan Anda berhasil digabungkan dengan pesanan sebelumnya! Tim kami akan segera menghubungi Anda via WhatsApp.');
        }

        return redirect()->route('order-page.index')->with('success', 'Pesanan Anda berhasil dikirim! Tim kami akan segera menghubungi Anda via WhatsApp.');
    }
}
