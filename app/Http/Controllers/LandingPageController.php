<?php

namespace App\Http\Controllers;

use App\Models\Product;

class LandingPageController extends Controller
{
    /**
     * Display the landing page with products from database.
     */
    public function index()
    {
        $products = Product::active()->inStock()->sorted()->get();

        // Pass products as JSON for JS rendering
        $productsJson = $products->map(fn($p) => [
            'id' => $p->id,
            'key' => $p->key,
            'name' => $p->name,
            'desc' => $p->description,
            'price' => $p->price,
            'stock' => $p->stock,
            'img' => asset($p->image_path),
            'glow' => $p->glow_color,
        ]);

        return view('landing', compact('productsJson'));
    }
}
