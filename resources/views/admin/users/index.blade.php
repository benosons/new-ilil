@extends('admin.layouts.app')
@section('page_title', 'Users')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar User ({{ $users->total() }})</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Tambah User</a>
        </div>

        <form method="GET" style="margin-bottom: 16px; display:flex; gap:8px">
            <input type="text" name="search" class="form-control" placeholder="Cari nama / email..."
                   value="{{ request('search') }}" style="max-width:300px">
            <button type="submit" class="btn btn-ghost btn-sm">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td class="text-muted">{{ $user->email }}</td>
                            <td>
                                @if ($user->is_admin)
                                    <span class="badge-status paid">Admin</span>
                                @else
                                    <span class="badge-status" style="background:rgba(255,255,255,.06);color:var(--muted)">User</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $users->links() }}</div>
    </div>
@endsection
