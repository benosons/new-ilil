@extends('admin.layouts.app')

@section('title', 'Daftar Pesanan Langsung')

@section('page_title', 'Pesanan Langsung')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Pesanan Langsung</h3>
        </div>

        {{-- Filters & Search --}}
        <form action="{{ route('admin.standalone-orders.index') }}" method="GET" style="margin-bottom:16px; display:flex; gap:12px; align-items:flex-end;">
            <div style="flex: 1; max-width: 300px;">
                <label for="search" style="display:block; font-size:.85rem; margin-bottom:6px; color:var(--muted)">Cari Pesanan</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Nama, WhatsApp, atau Email..." value="{{ request('search') }}">
            </div>
            <div>
                <label for="status" style="display:block; font-size:.85rem; margin-bottom:6px; color:var(--muted)">Status</label>
                <select name="status" id="status" class="form-control" style="min-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 11px 16px;">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.standalone-orders.index') }}" class="btn btn-ghost" style="padding: 11px 16px;">Reset</a>
                @endif
            </div>
        </form>

        {{-- Table Section --}}
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div><strong>{{ $order->name }}</strong></div>
                                <div class="text-muted" style="font-size: .8rem;">{{ $order->wa_number }}</div>
                            </td>
                            <td>
                                <div>{{ Str::words($order->items->pluck('product.name')->join(', ')?:'-', 5) }}</div>
                                <div class="text-muted" style="font-size: .8rem;">{{ $order->items->sum('quantity') }} items</div>
                            </td>
                            <td><strong>{{ $order->formatted_total }}</strong></td>
                            <td>{!! $order->status_badge !!}</td>
                            <td>
                                <a href="{{ route('admin.standalone-orders.show', $order) }}" class="btn btn-ghost btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted" style="text-align:center;padding:24px">Belum ada pesanan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination">{{ $orders->links() }}</div>
    </div>
@endsection
