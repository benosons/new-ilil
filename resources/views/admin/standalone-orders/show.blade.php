@extends('admin.layouts.app')

@section('page_title', 'Detail Pesanan Langsung')

@section('content')
    <div class="layout-grid">
        {{-- Order Items --}}
        <div class="card">
            <div class="card-header">
                <h3>Item Pesanan ({{ $standaloneOrder->items->count() }} Jenis)</h3>
                <span class="text-muted" style="font-size:.82rem">{{ $standaloneOrder->created_at->format('d M Y H:i') }}</span>
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
                        @foreach ($standaloneOrder->items as $item)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px">
                                        @if($item->product && $item->product->image_path)
                                            <img src="{{ str_starts_with($item->product->image_path, 'assets/') ? asset($item->product->image_path) : asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid var(--border)">
                                        @else
                                            <div style="width:40px; height:40px; background:var(--bg-lighter); border-radius:6px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--muted)">
                                                <svg style="width:20px; height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                        <strong>{{ $item->product->name ?? 'Produk Terhapus' }}</strong>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right"><span style="color:var(--muted)">Subtotal</span></td>
                            <td><span style="color:var(--muted)">Rp {{ number_format($standaloneOrder->total_price + $standaloneOrder->discount_amount, 0, ',', '.') }}</span></td>
                        </tr>
                        @if($standaloneOrder->voucher_code)
                        <tr>
                            <td colspan="3" style="text-align:right"><span style="color:var(--accent)">Diskon ({{ $standaloneOrder->voucher_code }})</span></td>
                            <td><span style="color:var(--accent)">-Rp {{ number_format($standaloneOrder->discount_amount, 0, ',', '.') }}</span></td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" style="text-align:right"><strong style="color:#fff">Total Pembayaran</strong></td>
                            <td><strong style="color:var(--accent); font-size:1.05rem">Rp {{ number_format($standaloneOrder->total_price, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Sidebar: Customer + Status --}}
        <div>
            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Info Pelanggan</h3>
                <div style="font-size:.85rem; line-height:1.8">
                    <div><strong>{{ $standaloneOrder->name }}</strong></div>
                    <div class="text-muted">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $standaloneOrder->wa_number) }}" target="_blank" style="color:var(--accent); text-decoration:none;">
                            📞 {{ $standaloneOrder->wa_number }} ↗
                        </a>
                    </div>
                    @if ($standaloneOrder->email)
                        <div class="text-muted">✉️ {{ $standaloneOrder->email }}</div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom:16px">
                <h3 style="font-size:.9rem; margin-bottom:14px">Status Pesanan</h3>
                <div style="margin-bottom:14px">{!! $standaloneOrder->status_badge !!}</div>

                <form method="POST" action="{{ route('admin.standalone-orders.updateStatus', $standaloneOrder) }}">
                    @csrf @method('PATCH')
                    <div class="form-group mb-0">
                        <label for="status">Ubah Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" {{ $standaloneOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processed" {{ $standaloneOrder->status == 'processed' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $standaloneOrder->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $standaloneOrder->status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mt-2" style="width:100%">Update Status</button>
                </form>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.standalone-orders.index') }}" class="btn btn-ghost btn-sm">← Kembali</a>
            </div>
        </div>
    </div>
@endsection
