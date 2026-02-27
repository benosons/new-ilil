@extends('admin.layouts.app')
@section('page_title', $voucher->exists ? 'Edit Voucher' : 'Buat Voucher')

@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-header">
            <h3>{{ $voucher->exists ? 'Edit Voucher' : 'Buat Voucher Baru' }}</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $voucher->exists ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}">
            @csrf
            @if ($voucher->exists) @method('PUT') @endif

            <div class="form-group">
                <label for="code">Kode Voucher</label>
                <input type="text" id="code" name="code" class="form-control"
                       value="{{ old('code', $voucher->code) }}" required
                       placeholder="CONTOH: DISKON10" style="text-transform:uppercase; font-family:monospace; letter-spacing:1px">
                <small class="text-muted">Hanya huruf dan angka, tanpa spasi.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="type">Tipe Diskon</label>
                    <select name="type" id="type" class="form-control">
                        <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                        <option value="percent" {{ old('type', $voucher->type) == 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="value">Nilai Diskon</label>
                    <input type="number" id="value" name="value" class="form-control"
                           value="{{ old('value', $voucher->value) }}" min="0" step="0.01" required>
                </div>
                <div class="form-group" id="max_discount_group" style="display: {{ old('type', $voucher->type) == 'percent' ? 'block' : 'none' }};">
                    <label for="max_discount">Maksimal Potongan</label>
                    <input type="number" id="max_discount" name="max_discount" class="form-control"
                           value="{{ old('max_discount', $voucher->max_discount) }}" min="0" step="0.01" placeholder="Cth: 20000">
                    <small class="text-muted">Opsional. Berlaku jika persentase.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="max_uses">Maksimal Penggunaan</label>
                    <input type="number" id="max_uses" name="max_uses" class="form-control"
                           value="{{ old('max_uses', $voucher->max_uses) }}" min="1">
                    <small class="text-muted">Kosongkan jika tidak terbatas</small>
                </div>
                <div class="form-group">
                    <label for="expires_at">Tanggal Kadaluarsa</label>
                    <input type="date" id="expires_at" name="expires_at" class="form-control"
                           value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d') : '') }}">
                    <small class="text-muted">Kosongkan jika selamanya</small>
                </div>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $voucher->is_active ?? true) ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#39d98a">
                    <span>Aktif (Dapat digunakan)</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $voucher->exists ? 'Simpan Perubahan' : 'Buat Voucher' }}</button>
                <a href="{{ route('admin.vouchers.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var typeSelect = document.getElementById('type');
            var maxDiscountGroup = document.getElementById('max_discount_group');
            
            function toggleMaxDiscount() {
                if(typeSelect.value === 'percent') {
                    maxDiscountGroup.style.display = 'block';
                } else {
                    maxDiscountGroup.style.display = 'none';
                    document.getElementById('max_discount').value = '';
                }
            }
            
            typeSelect.addEventListener('change', toggleMaxDiscount);
        });
    </script>
@endsection
