@extends('admin.layouts.app')
@section('page_title', 'Kelola Voucher')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Voucher</h3>
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
                <span class="icon" style="font-size:1rem; margin-right:4px">+</span> Buat Voucher
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Terpakai / Max</th>
                        <th>Exp. Date</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vouchers as $v)
                        <tr>
                            <td>
                                <strong style="font-family:monospace; font-size:1rem; letter-spacing:1px; color:var(--accent)">
                                    {{ $v->code }}
                                </strong>
                            </td>
                            <td>{{ $v->type == 'percent' ? 'Persentase' : 'Nominal Tetap' }}</td>
                            <td>
                                @if($v->type == 'percent')
                                    <strong>{{ $v->value }}%</strong>
                                @else
                                    <strong>Rp {{ number_format($v->value, 0, ',', '.') }}</strong>
                                @endif
                            </td>
                            <td>
                                {{ $v->used_count }}
                                <span class="text-muted">/ {{ $v->max_uses ?? '∞' }}</span>
                            </td>
                            <td>
                                @if($v->expires_at)
                                    <span class="{{ $v->expires_at->isPast() ? 'text-danger' : '' }}">
                                        {{ $v->expires_at->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Selamanya</span>
                                @endif
                            </td>
                            <td>
                                @if($v->is_active)
                                    <span class="badge-status paid">Aktif</span>
                                @else
                                    <span class="badge-status cancelled">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <a href="{{ route('admin.vouchers.edit', $v) }}" class="btn btn-ghost btn-sm">Edit</a>
                                <form action="{{ route('admin.vouchers.destroy', $v) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus voucher ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted" style="text-align:center; padding:30px">
                                Belum ada kode voucher.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    </div>
@endsection
