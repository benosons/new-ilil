<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/products', function () {
    return Product::active()->sorted()->get()->map(fn($p) => [
        'id' => $p->id,
        'key' => $p->key,
        'name' => $p->name,
        'desc' => $p->description,
        'price' => $p->price,
        'img' => asset($p->image_path),
        'glow' => $p->glow_color,
    ]);
});
