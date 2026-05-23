<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - DVNStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('asset/css/auth.css') }}">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div style="max-width: 800px; width: 100%;">
      <div class="text-center mb-4">
        <img src="{{ asset('asset/image/logap.png') }}" alt="DVNStore" width="80">
        <h2 class="auth-title mt-2 fw-bold">Pilih Jenis Akun</h2>
        <p class="text-muted">Pilih sesuai kebutuhan Anda</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <a href="{{ route('register.user') }}" class="text-decoration-none text-dark">
            <div class="choice-card card p-4 h-100 text-center">
              <span class="material-symbols-outlined icon-big mb-3">person</span>
              <h4 class="fw-bold">Daftar sebagai User</h4>
              <p class="text-muted small">Customer biasa yang ingin mencari, membeli, mengunduh, dan memberi review aplikasi atau game.</p>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <a href="{{ route('register.developer') }}" class="text-decoration-none text-dark">
            <div class="choice-card card p-4 h-100 text-center">
              <span class="material-symbols-outlined icon-big mb-3">code</span>
              <h4 class="fw-bold">Daftar sebagai Developer</h4>
              <p class="text-muted small">Indie developer yang ingin mengunggah, menjual, dan menerima withdraw hasil penjualan aplikasi/game.</p>
            </div>
          </a>
        </div>
      </div>
      <div class="text-center mt-4">
        <small>Sudah punya akun? <a href="{{ url('/login') }}" class="fw-bold">Login</a></small>
      </div>
    </div>
  </div>
</body>
</html>
