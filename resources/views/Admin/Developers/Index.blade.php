@extends('Layouts.layout')
@section('title', 'Manajemen Developer - Admin')
@section('content')
<h3 class="page-title"><span class="material-symbols-outlined">code</span> Manajemen Developer</h3>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle">
<thead><tr><th>#</th><th>Nama / Studio</th><th>Email</th><th>Produk</th><th>Saldo</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
<tbody>
@forelse($developers as $d)
<tr>
    <td>{{ $d->id }}</td>
    <td><strong>{{ $d->name }}</strong><br><small class="text-muted">{{ optional($d->developerProfile)->nama_studio }}</small></td>
    <td>{{ $d->email }}</td>
    <td>{{ $d->platforms_count }}</td>
    <td>Rp {{ number_format(optional($d->wallet)->saldo ?? 0, 0, ',', '.') }}</td>
    <td>
        @if($d->status==='blocked')
            <span class="badge bg-danger" title="{{ $d->blocked_reason }}">Blocked</span>
        @else
            <span class="badge bg-success">Active</span>
        @endif
    </td>
    <td class="text-end">
        <div class="action-group">
        <a href="{{ route('admin.developers.show',$d->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
        @if($d->status==='blocked')
            <form action="{{ route('admin.developers.unblock',$d->id) }}" method="POST" class="d-inline">@csrf
                <button class="btn btn-sm btn-outline-success">Unblock</button>
            </form>
        @else
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#blockDev{{$d->id}}">Block</button>
            <div class="modal fade" id="blockDev{{$d->id}}" tabindex="-1"><div class="modal-dialog">
                <form action="{{ route('admin.developers.block',$d->id) }}" method="POST" class="modal-content">@csrf
                    <div class="modal-header"><h5 class="modal-title">Block Developer {{$d->name}}</h5></div>
                    <div class="modal-body"><label class="form-label">Alasan</label>
                        <input name="reason" class="form-control" required></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger">Block Developer</button>
                    </div>
                </form>
            </div></div>
        @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted">Belum ada developer.</td></tr>
@endforelse
</tbody></table>
{{ $developers->links() }}
</div></div>
@endsection
