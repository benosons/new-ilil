<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::sorted()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product()]);
    }

    public function store(Request $request)
    {
        $rules = [
            'key' => 'required|string|max:50|unique:products,key',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'glow_color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];

        $messages = [
            'image.max' => 'Ukuran gambar maksimal adalah 2MB.',
            'image.uploaded' => 'Gagal mengupload gambar. Kemungkinan ukuran gambar lebih besar dari batas maksimal server (PHP upload_max_filesize). Cobalah kompres gambar Anda.'
        ];

        $data = $request->validate($rules, $messages);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = 'storage/' . $path;
        }

        $data['is_active'] = $request->boolean('is_active');
        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $rules = [
            'key' => 'required|string|max:50|unique:products,key,' . $product->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'glow_color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];

        $messages = [
            'image.max' => 'Ukuran gambar maksimal adalah 2MB.',
            'image.uploaded' => 'Gagal mengupload gambar. Kemungkinan ukuran gambar lebih besar dari batas maksimal server (PHP upload_max_filesize). Cobalah kompres gambar Anda.'
        ];

        $data = $request->validate($rules, $messages);

        if ($request->hasFile('image')) {
            // Delete old image if exists and is in storage (not assets)
            if ($product->image_path && str_starts_with($product->image_path, 'storage/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $product->image_path));
            }
            
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = 'storage/' . $path;
        }

        $data['is_active'] = $request->boolean('is_active');
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
