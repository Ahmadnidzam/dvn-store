@extends('Layouts.layout')
@section('title', 'Manajemen Platform - Admin')
@section('content')
<h3 class="page-title"><span class="material-symbols-outlined">apps</span> Manajemen Platform</h3>
<form class="mb-3"><div class="d-flex gap-2 flex-wrap">
    <select name="category" class="form-select" style="max-width:220px" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        <option value="app" @selected(request('category')==='app')>Aplikasi</option>
        <option value="game" @selected(request('category')==='game')>Game</option>
    </select>
</div></form>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle">
<thead><tr><th>#</th><th>Nama</th><th>Developer</th><th>Kategori</th><th>Scan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($platforms as $p)
<tr>
    <td>{{ $p->id }}</td>
    <td><a href="{{ route('admin.platforms.show',$p->id) }}">{{ $p->nama_platform }}</a></td>
    <td>{{ $p->developer->name ?? '-' }}</td>
    <td><span class="badge bg-secondary">{{ $p->category }}</span></td>
    <td>{{ $p->scan_status }}</td>
    <td>
        @if($p->is_taken_down)<span class="badge bg-dark">Taken Down</span>
        @elseif($p->is_published)<span class="badge bg-success">Published</span>
        @else<span class="badge bg-warning text-dark">Pending</span>@endif
    </td>
    <td class="text-end">
        <div class="action-group">
        @if($p->is_taken_down)
            <form action="{{ route('admin.platforms.restore',$p->id) }}" method="POST" class="d-inline">@csrf
                <button class="btn btn-sm btn-outline-success">Restore</button>
            </form>
        @else
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tk{{$p->id}}">Takedown</button>
            <div class="modal fade" id="tk{{$p->id}}" tabindex="-1"><div class="modal-dialog">
                <form action="{{ route('admin.platforms.takedown',$p->id) }}" method="POST" class="modal-content">@csrf
                    <div class="modal-header"><h5 class="modal-title">Takedown {{$p->nama_platform}}</h5></div>
                    <div class="modal-body"><label class="form-label">Alasan</label>
                        <input name="reason" class="form-control" required></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger">Takedown</button>
                    </div>
                </form>
            </div></div>
        @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted">Belum ada platform.</td></tr>
@endforelse
</tbody></table>
{{ $platforms->links() }}
</div></div>
@endsection
