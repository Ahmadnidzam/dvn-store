@extends('Layouts.layout')
@section('title', 'Manajemen User - Admin')
@section('content')
<h3 class="page-title"><span class="material-symbols-outlined">group</span> Manajemen User</h3>
<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table align-middle">
            <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Status</th><th>Bergabung</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @if($u->status==='blocked')
                            <span class="badge bg-danger" title="{{ $u->blocked_reason }}">Blocked</span>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </td>
                    <td>{{ $u->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <div class="action-group">
                        @if($u->status==='blocked')
                            <form action="{{ route('admin.users.unblock',$u->id) }}" method="POST" class="d-inline">@csrf
                                <button class="btn btn-sm btn-outline-success">Unblock</button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#blockUser{{$u->id}}">Block</button>
                            <div class="modal fade" id="blockUser{{$u->id}}" tabindex="-1"><div class="modal-dialog">
                                <form action="{{ route('admin.users.block',$u->id) }}" method="POST" class="modal-content">@csrf
                                    <div class="modal-header"><h5 class="modal-title">Block {{$u->name}}</h5></div>
                                    <div class="modal-body"><label class="form-label">Alasan</label>
                                        <input name="reason" class="form-control" required></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button class="btn btn-danger">Block User</button>
                                    </div>
                                </form>
                            </div></div>
                        @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>
@endsection
