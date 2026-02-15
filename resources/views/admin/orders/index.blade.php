@extends('admin.layouts.app')
@section('page_title', 'Pesanan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Pesanan</h3>
            <a href="{{ route('admin.orders.export') }}" class="btn btn-primary" target="_blank">
                <span class="icon">📥</span> Export Excel
            </a>
        </div>

        {{-- Status filter tabs --}}
        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px">
            <a href="{{ route('admin.orders.index') }}"
               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-ghost' }}">
                Semua ({{ $statusCounts['all'] }})
            </a>
            @foreach (['pending' => 'Pending', 'paid' => 'Dibayar', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Batal'] as $key => $label)
                <a href="{{ route('admin.orders.index', ['status' => $key]) }}"
                   class="btn btn-sm {{ request('status') === $key ? 'btn-primary' : 'btn-ghost' }}">
                    {{ $label }} ({{ $statusCounts[$key] }})
                </a>
            @endforeach
        </div>

        {{-- Search --}}
        <form method="GET" style="margin-bottom:16px; display:flex; gap:8px">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" class="form-control" placeholder="Cari no. order / nama / telp..."
                   value="{{ request('search') }}" style="max-width:300px">
            <button type="submit" class="btn btn-ghost btn-sm">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Customer</th>
                        <th>Telepon</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td class="text-muted">{{ $order->customer_phone }}</td>
                            <td>{{ $order->items->sum('quantity') }} item</td>
                            <td><strong>{{ $order->formatted_total }}</strong></td>
                            <td>{!! $order->status_badge !!}</td>
                            <td class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted" style="text-align:center;padding:24px">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $orders->links() }}</div>
    </div>
@endsection
