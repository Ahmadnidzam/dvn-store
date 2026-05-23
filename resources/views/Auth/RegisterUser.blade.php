<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar User - DVNStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('asset/css/auth.css') }}">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="card auth-card p-4" style="width: 480px;">
      <div class="text-center mb-3">
        <img src="{{ asset('asset/image/logap.png') }}" alt="DVNStore" width="70">
        <h4 class="auth-title mt-2 fw-bold">Daftar User</h4>
        <p class="text-muted small mb-0">Akun customer DVNStore</p>
      </div>

      @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
      @if ($errors->any())
        <div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
      @endif

      <form method="POST" action="{{ route('register.user') }}">
        @csrf
        <div class="mb-3"><label class="form-label">Nama Lengkap</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div class="mb-3"><label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
        <div class="row">
          <div class="col-6 mb-3"><label class="form-label">Password</label>
            <input type="password" name="pass" class="form-control" required></div>
          <div class="col-6 mb-3"><label class="form-label">Konfirmasi</label>
            <input type="password" name="konf_pass" class="form-control" required></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Kode Unik <small class="text-muted">(untuk reset password)</small></label>
          <input type="text" name="kode_unik" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-dvn w-100">Buat Akun</button>
      </form>
      <div class="text-center mt-3">
        <small><a href="{{ route('register.choice') }}">Pilih jenis akun lain</a></small>
      </div>
    </div>
  </div>
</body>
</html>
