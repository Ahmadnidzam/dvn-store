@extends('Layouts.layout')
@section('title','Upload Produk - Developer')
@section('content')
<a href="{{ route('developer.platforms.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<h3 class="page-title"><span class="material-symbols-outlined">upload_file</span> Upload Produk Baru</h3>

<div class="alert alert-warning">
    <strong>Catatan:</strong> Setelah submit, Anda diminta membayar <strong>Rp {{ number_format(config('dvnstore.upload_fee'),0,',','.') }}</strong> sebagai upload fee. Setelah pembayaran sukses, file akan di-scan VirusTotal sebelum dipublikasikan.
</div>

<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('developer.platforms.store') }}" enctype="multipart/form-data">@csrf
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Kategori</label>
            <select name="category" class="form-select" required>
                <option value="game">Game</option>
                <option value="app">Aplikasi</option>
            </select>
        </div>
        <div class="col-md-8 mb-3"><label class="form-label">Nama Produk</label>
            <input type="text" name="nama_platform" class="form-control" value="{{ old('nama_platform') }}" required></div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Genre</label>
            <select name="genre" class="form-select" required>
                <option value="" disabled {{ old('genre') ? '' : 'selected' }}>Pilih genre</option>
                @foreach($genres as $group => $items)
                    <optgroup label="{{ $group }}">
                        @foreach($items as $genre)
                            <option value="{{ $genre }}" {{ old('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select></div>
        <div class="col-md-6 mb-3"><label class="form-label">Harga (Rupiah, 0 = gratis)</label>
            <input type="number" name="harga" class="form-control" value="{{ old('harga',0) }}" min="0" required></div>
    </div>
    <div class="mb-3"><label class="form-label">Deskripsi (min. 50 karakter)</label>
        <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi') }}</textarea></div>
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Icon (JPG/PNG/WEBP, max 2MB)</label>
            <input type="file" name="icon" accept="image/*" class="form-control" required></div>
        <div class="col-md-4 mb-3"><label class="form-label">Cuplikan Video (opsional, max 20MB)</label>
            <input type="file" name="cuplikan" accept="video/*" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">File Installer (ZIP/APK/EXE, max 200MB)</label>
            <input type="file" name="file" class="form-control" required></div>
    </div>
    <button class="btn btn-success">Lanjut ke Pembayaran Upload Fee</button>
</form>
</div></div>
@endsection
