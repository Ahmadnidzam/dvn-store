@extends('Layouts.layout')
@section('title','Profil - DVNStore')
@section('content')
<a href="javascript:history.back()" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4 text-center">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover">
                @else
                    <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:120px;height:120px;font-size:2rem;font-weight:bold">
                        {{ strtoupper(substr($user->name,0,2)) }}
                    </div>
                @endif
                <h3 class="fw-bold mb-0">{{ $user->name }}</h3>
                <p class="text-muted">{{ $user->email }}</p>
                <p>
                    <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                    @if($user->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Blocked</span>
                    @endif
                </p>
                <hr>
                <div class="row text-center">
                    <div class="col"><p class="text-muted mb-0 small">Tergabung</p><strong>{{ $user->created_at->format('d M Y') }}</strong></div>
                    @if($user->role === 'user')
                    <div class="col"><p class="text-muted mb-0 small">Item di Library</p><strong>{{ $user->downloads_count ?? 0 }}</strong></div>
                    @endif
                </div>
                <hr>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profil</a>
            </div>
        </div>
    </div>
</div>
@endsection
