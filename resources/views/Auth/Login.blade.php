<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - DVNStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('asset/css/auth.css') }}">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card auth-card p-4" style="width: 380px;">
      <div class="text-center mb-3">
        <img src="{{ asset('asset/image/logap.png') }}" alt="DVNStore" width="80">
        <h4 class="brand-title mt-2 fw-bold">DVNStore</h4>
        <p class="text-muted small mb-0">Platform Distribusi Aplikasi & Game</p>
      </div>

      @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
      @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
      @if ($errors->any())
        <div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="pass" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-dvn w-100">Login</button>
      </form>
      <div class="text-center mt-3">
        <small><a href="{{ url('/forgetpass') }}" class="text-decoration-none">Lupa password?</a></small>
      </div>
      <hr>
      <div class="text-center">
        <small>Belum punya akun? <a href="{{ route('register.choice') }}" class="fw-bold text-decoration-none">Daftar</a></small>
      </div>
    </div>
  </div>
</body>
</html>
