@extends('Layouts.layout')
@section('title','Edit Produk - Developer')
@section('content')
<a href="{{ route('developer.platforms.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<h3 class="fw-bold mb-3">Edit Produk: {{ $platform->nama_platform }}</h3>

<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('developer.platforms.update',$platform->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Nama Produk</label>
        <input type="text" name="nama_platform" class="form-control" value="{{ old('nama_platform',$platform->nama_platform) }}" required></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Genre</label>
            <select name="genre" class="form-select" required>
                @foreach($genres as $group => $items)
                    <optgroup label="{{ $group }}">
                        @foreach($items as $genre)
                            <option value="{{ $genre }}" {{ old('genre', $platform->genre) === $genre ? 'selected' : '' }}>{{ $genre }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select></div>
        <div class="col-md-6 mb-3"><label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" value="{{ old('harga',$platform->harga) }}" min="0" required></div>
    </div>
    <div class="mb-3"><label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi',$platform->deskripsi) }}</textarea></div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Icon baru (opsional)</label>
            <input type="file" name="icon" accept="image/*" class="form-control"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Cuplikan video baru (opsional)</label>
            <input type="file" name="cuplikan" accept="video/*" class="form-control"></div>
    </div>
    <button class="btn btn-primary">Simpan Perubahan</button>
</form>
</div></div>
@endsection
