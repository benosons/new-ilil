@extends('admin.layouts.app')
@section('page_title', 'Produk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Produk ({{ $products->total() }})</h3>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Key</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                @if ($product->image_path)
                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}"
                                         style="width:40px; height:40px; object-fit:cover; border-radius:8px; border:1px solid var(--stroke)">
                                @else
                                    <div style="width:40px;height:40px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;font-size:.7rem;color:var(--muted)">N/A</div>
                                @endif
                            </td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td class="text-muted">{{ $product->key }}</td>
                            <td>{{ $product->formatted_price }}</td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge-status paid">Aktif</span>
                                @else
                                    <span class="badge-status cancelled">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $product->sort_order }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                          onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $products->links() }}</div>
    </div>
@endsection
