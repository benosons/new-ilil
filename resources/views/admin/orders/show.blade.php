@extends('admin.layouts.app')
@section('page_title', 'Detail Pesanan #' . $order->order_number)

@section('content')
    <div class="layout-grid">
        {{-- Order Items --}}
        <div class="card">
            <div class="card-header">
                <h3>Item Pesanan</h3>
                <span class="text-muted" style="font-size:.82rem">{{ $order->created_at->format('d M Y H:i') }}</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right"><strong>Subtotal</strong></td>
                            <td><strong>{{ $order->formatted_subtotal }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right" class="text-muted">Ongkir</td>
                            <td class="text-muted">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right"><strong style="color:var(--accent)">Total</strong></td>
                            <td><strong style="color:var(--accent); font-size:1.05rem">{{ $order->formatted_total }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Sidebar: Customer + Status --}}
        <div>
            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Info Customer</h3>
                <div style="font-size:.85rem; line-height:1.8">
                    <div><strong>{{ $order->customer_name }}</strong></div>
                    <div class="text-muted">📞 {{ $order->customer_phone }}</div>
                    @if ($order->customer_email)
                        <div class="text-muted">✉️ {{ $order->customer_email }}</div>
                    @endif
                    @if ($order->customer_address)
                        <div class="text-muted" style="margin-top:8px">📍 {{ $order->customer_address }}</div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Status Pesanan</h3>
                <div style="margin-bottom:14px">{!! $order->status_badge !!}</div>

                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group mb-0">
                        <label for="status">Ubah Status</label>
                        <select name="status" id="status" class="form-control">
                            @foreach (['pending','paid','processing','shipped','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-2">Update Status</button>
                </form>
            </div>

            {{-- Ongkir --}}
            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Ongkos Kirim</h3>
                <form method="POST" action="{{ route('admin.orders.update-shipping', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group mb-0">
                        <label for="shipping_cost">Biaya Ongkir (Rp)</label>
                        <input type="number" name="shipping_cost" id="shipping_cost" class="form-control"
                               value="{{ $order->shipping_cost }}" min="0">
                    </div>
                    <button type="submit" class="btn btn-ghost btn-sm mt-2" style="width:100%">Update Ongkir</button>
                </form>
            </div>

            {{-- Resi --}}
            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Resi Pengiriman</h3>
                @if($order->tracking_number)
                    <div style="margin-bottom:10px; font-size:.85rem">
                        <div class="text-muted">{{ $order->shipping_courier }}</div>
                        <div style="font-family:monospace; font-size:1rem; letter-spacing:1px; font-weight:700; color:var(--accent)">
                            {{ $order->tracking_number }}
                        </div>
                        <div class="text-muted" style="font-size:.75rem">
                            Shipped: {{ $order->shipped_at ? $order->shipped_at->format('d M H:i') : '-' }}
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.orders.update-tracking', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group">
                        <label for="shipping_courier">Kurir</label>
                        <input type="text" name="shipping_courier" id="shipping_courier" class="form-control"
                               value="{{ $order->shipping_courier }}" placeholder="JNE / J&T / Sicepat">
                    </div>
                    <div class="form-group mb-0">
                        <label for="tracking_number">No. Resi</label>
                        <input type="text" name="tracking_number" id="tracking_number" class="form-control"
                               value="{{ $order->tracking_number }}" placeholder="Input nomor resi">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-2" style="width:100%">
                        {{ $order->tracking_number ? 'Update Resi' : 'Input Resi' }}
                    </button>
                </form>
            </div>

            <div class="card">
                <h3 style="font-size:.9rem; margin-bottom:14px">Payment</h3>
                <div style="font-size:.85rem; line-height:1.8">
                    <div>Metode: <strong>{{ ucfirst($order->payment_method) }}</strong></div>
                    @if ($order->midtrans_transaction_id)
                        <div class="text-muted">Transaction ID: {{ $order->midtrans_transaction_id }}</div>
                    @endif
                    @if ($order->paid_at)
                        <div class="text-muted">Dibayar: {{ $order->paid_at->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" style="display:inline"
                      onsubmit="return confirm('Hapus pesanan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Hapus Pesanan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
