@extends('admin.layouts.app')
@section('page_title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-header">
            <h3>{{ $user->exists ? 'Edit User' : 'Tambah User Baru' }}</h3>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if ($user->exists) @method('PUT') @endif

            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
                <input type="password" id="password" name="password" class="form-control"
                       {{ $user->exists ? '' : 'required' }}>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
                    <input type="checkbox" name="is_admin" value="1"
                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#39d98a">
                    <span>Admin</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $user->exists ? 'Simpan' : 'Buat User' }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>
@endsection
