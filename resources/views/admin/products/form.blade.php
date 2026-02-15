@extends('admin.layouts.app')
@section('page_title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
    <div class="card" style="max-width:650px">
        <div class="card-header">
            <h3>{{ $product->exists ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($product->exists) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="form-group">
                    <label for="key">Key (slug)</label>
                    <input type="text" id="key" name="key" class="form-control"
                           value="{{ old('key', $product->key) }}" required placeholder="contoh: original">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" id="price" name="price" class="form-control"
                           value="{{ old('price', $product->price) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label for="sort_order">Urutan</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control"
                           value="{{ old('sort_order', $product->sort_order ?? 0) }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="image">Gambar Produk</label>
                @if($product->image_path)
                    <div style="margin-bottom:10px">
                        <img src="{{ asset($product->image_path) }}" alt="Preview" style="max-height:100px; border-radius:8px">
                    </div>
                @endif
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small class="text-muted" style="font-size:.75rem">Upload gambar baru untuk mengganti (jpg, png, webp max 2MB).</small>
            </div>

            <div class="form-group">
                <label for="glow_color">Glow Color (CSS)</label>
                <input type="text" id="glow_color" name="glow_color" class="form-control"
                       value="{{ old('glow_color', $product->glow_color ?? 'rgba(255,213,74,.22)') }}"
                       placeholder="rgba(255,213,74,.22)">
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#39d98a">
                    <span>Aktif (tampil di landing page)</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $product->exists ? 'Simpan' : 'Buat Produk' }}</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
