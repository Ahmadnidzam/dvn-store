@extends('Layouts.layout')
@section('title','Komunitas - DVNStore')
@section('content')
<h3 class="page-title">
    <span class="material-symbols-outlined align-middle">forum</span> Komunitas DVNStore
</h3>

@if(Session::get('role') !== 'admin')
<div class="card shadow-sm mb-4"><div class="card-body">
<form method="POST" action="{{ route('forum.store') }}">@csrf
    <label class="form-label">Bagikan sesuatu ke komunitas</label>
    <textarea name="content" class="form-control mb-2" rows="3" placeholder="Tulis pesan Anda..." required maxlength="2000"></textarea>
    <button class="btn btn-primary">Kirim Pesan</button>
</form>
</div></div>
@else
<div class="alert alert-info">Anda login sebagai admin, hanya bisa melihat dan memoderasi.</div>
@endif

@forelse($posts as $p)
<div class="card shadow-sm mb-3"><div class="card-body">
    <div class="d-flex justify-content-between gap-3 flex-wrap">
        <div>
            <strong>{{ $p->user->name }}</strong>
            <span class="badge bg-{{ $p->user->role==='developer'?'info':'secondary' }} ms-1">{{ $p->user->role }}</span>
            <small class="text-muted ms-2">{{ $p->created_at->diffForHumans() }}</small>
        </div>
        @if(Session::get('role') !== 'admin')
        <form method="POST" action="{{ route('forum.helpful',$p->id) }}">@csrf
            <button class="btn btn-sm btn-outline-success"><span class="material-symbols-outlined">thumb_up</span> Helpful ({{ $p->helpful_count }})</button>
        </form>
        @else
        <span class="helpful-inline"><span class="material-symbols-outlined">thumb_up</span> {{ $p->helpful_count }}</span>
        @endif
    </div>
    <p class="mt-2 mb-0">{{ $p->content }}</p>
</div></div>
@empty
<div class="text-center text-muted py-5">
    <span class="material-symbols-outlined" style="font-size:4rem">forum</span>
    <p class="mt-2">Belum ada pesan. Jadilah yang pertama!</p>
</div>
@endforelse

{{ $posts->links() }}
@endsection
