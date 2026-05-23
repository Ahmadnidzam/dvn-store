@extends('Layouts.layout')
@section('title','Edit Profil - DVNStore')
@section('content')
<a href="{{ route('profile') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm"><div class="card-body p-4">
            <h4 class="fw-bold mb-3">Edit Profil</h4>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3 text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" class="rounded-circle" style="width:100px;height:100px;object-fit:cover">
                    @endif
                </div>
                <div class="mb-3"><label class="form-label">Avatar (opsional)</label>
                    <input type="file" name="avatar" accept="image/*" class="form-control"></div>
                <div class="mb-3"><label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$user->name) }}" required></div>
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
