<!DOCTYPE html>
<html lang="id"><head>
<meta charset="UTF-8"><title>Lupa Password - DVNStore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('asset/css/auth.css') }}">
</head><body>
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card auth-card p-4" style="width:400px">
    <h4 class="auth-title text-center fw-bold mb-3">Lupa Password</h4>
    @if(session('error'))<div class="alert alert-danger py-2">{{session('error')}}</div>@endif
    <form method="POST" action="{{ route('forget') }}">@csrf
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Kode Unik</label><input type="text" name="kode_unik" class="form-control" required></div>
      <button class="btn btn-dvn w-100">Verifikasi</button>
    </form>
    <div class="text-center mt-3"><small><a href="{{ url('/login') }}">Login</a></small></div>
  </div>
</div></body></html>
