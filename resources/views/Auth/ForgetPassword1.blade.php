<!DOCTYPE html>
<html lang="id"><head>
<meta charset="UTF-8"><title>Reset Password - DVNStore</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('asset/css/auth.css') }}">
</head><body>
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card auth-card p-4" style="width:400px">
    <h4 class="auth-title text-center fw-bold mb-3">Reset Password</h4>
    @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('forgetpassword') }}">@csrf
      <div class="mb-3"><label class="form-label">Password Baru</label><input type="password" name="pass" class="form-control" required minlength="6"></div>
      <div class="mb-3"><label class="form-label">Konfirmasi Password</label><input type="password" name="konf_pass" class="form-control" required></div>
      <button class="btn btn-dvn w-100">Simpan Password Baru</button>
    </form>
  </div>
</div></body></html>
