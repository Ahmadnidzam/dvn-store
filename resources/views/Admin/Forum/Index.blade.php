@extends('Layouts.layout')
@section('title','Moderasi Forum - Admin')
@section('content')
<h3 class="fw-bold mb-3" style="color: var(--dark-blue);">Moderasi Forum</h3>
<div class="card shadow-sm"><div class="card-body">
@forelse($posts as $p)
    <div class="border rounded p-3 mb-3 {{ $p->is_hidden ? 'bg-light text-muted' : '' }}">
        <div class="d-flex justify-content-between">
            <div>
                <strong>{{ $p->user->name }}</strong> <span class="badge bg-secondary">{{ $p->user->role }}</span>
                <small class="text-muted ms-2">{{ $p->created_at->diffForHumans() }}</small>
                @if($p->is_hidden)<span class="badge bg-warning text-dark ms-1">Hidden</span>@endif
            </div>
            <div class="helpful-inline"><span class="material-symbols-outlined">thumb_up</span> {{ $p->helpful_count }}</div>
        </div>
        <p class="mt-2 mb-2">{{ $p->content }}</p>
        <div class="d-flex gap-2">
            @if($p->is_hidden)
                <form action="{{ route('admin.forum.unhide',$p->id) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-success">Tampilkan</button></form>
            @else
                <form action="{{ route('admin.forum.hide',$p->id) }}" method="POST">@csrf<button class="btn btn-sm btn-outline-warning">Sembunyikan</button></form>
            @endif
            <form action="{{ route('admin.forum.destroy',$p->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button>
            </form>
        </div>
    </div>
@empty
<p class="text-center text-muted">Belum ada post.</p>
@endforelse
{{ $posts->links() }}
</div></div>
@endsection
