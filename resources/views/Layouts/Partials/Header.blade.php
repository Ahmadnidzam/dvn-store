@if (Session::has('login'))
<nav class="navbar navbar-expand-lg shadow-sm py-2 sticky-top">
  <div class="container-fluid px-3 px-lg-4">
    <a class="navbar-brand d-flex align-items-center gap-2 me-3" href="{{ url('/') }}">
      <img src="{{ asset('asset/image/logap.png') }}" alt="logo" width="35" height="40">
      <span class="fw-bold fs-5">DVNStore</span>
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      {{-- Search hanya untuk user/dev (admin tidak transaksi) --}}
      @if (in_array(Session::get('role'), ['user', 'developer']))
        <form action="{{ url('search') }}" method="GET" class="navbar-search d-flex align-items-center gap-2 mx-auto my-3 my-lg-0">
          <input type="text" name="q" class="form-control form-control-sm shadow-sm" placeholder="Telusuri game, aplikasi, genre..." required>
          <button type="submit" class="btn btn-sm text-white shadow-sm px-3">Cari</button>
        </form>
      @endif

      <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 gap-lg-3">
        @if (Session::get('role') === 'admin')
          <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
          <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a></li>
          <li class="nav-item"><a href="{{ route('admin.developers.index') }}" class="nav-link {{ request()->routeIs('admin.developers.*') ? 'active' : '' }}">Developers</a></li>
          <li class="nav-item"><a href="{{ route('admin.platforms.index') }}" class="nav-link {{ request()->routeIs('admin.platforms.*') ? 'active' : '' }}">Platforms</a></li>
          <li class="nav-item"><a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">Transaksi</a></li>
          <li class="nav-item"><a href="{{ route('admin.withdraws.index') }}" class="nav-link {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}">Withdraw</a></li>
          <li class="nav-item"><a href="{{ route('admin.forum.index') }}" class="nav-link {{ request()->routeIs('admin.forum.*') ? 'active' : '' }}">Forum</a></li>
        @elseif (Session::get('role') === 'developer')
          <li class="nav-item"><a href="{{ route('developer.dashboard') }}" class="nav-link {{ request()->routeIs('developer.dashboard') ? 'active' : '' }}">Dashboard</a></li>
          <li class="nav-item"><a href="{{ route('developer.platforms.index') }}" class="nav-link {{ request()->routeIs('developer.platforms.*') ? 'active' : '' }}">Produk Saya</a></li>
          <li class="nav-item"><a href="{{ route('developer.wallet.index') }}" class="nav-link {{ request()->routeIs('developer.wallet.*') ? 'active' : '' }}">Wallet</a></li>
          <li class="nav-item"><a href="{{ route('developer.withdraws.index') }}" class="nav-link {{ request()->routeIs('developer.withdraws.*') ? 'active' : '' }}">Withdraw</a></li>
          <li class="nav-item"><a href="{{ route('forum.index') }}" class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}">Forum</a></li>
        @else
          <li class="nav-item"><a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">Home</a></li>
          <li class="nav-item"><a href="{{ url('/unduhan') }}" class="nav-link {{ request()->is('unduhan*') ? 'active' : '' }}">Library</a></li>
          <li class="nav-item"><a href="{{ route('forum.index') }}" class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}">Forum</a></li>
        @endif

        <li class="nav-item">
          <a href="{{ route('profile') }}" class="nav-link material-symbols-outlined fs-4 p-2" title="Profil">account_circle</a>
        </li>
        <li class="nav-item">
          <form action="{{ url('/logout') }}" method="POST" class="d-inline m-0">@csrf
            <button type="submit" class="btn btn-link nav-link p-2 border-0 bg-transparent">
              <span class="material-symbols-outlined fs-4" title="Logout">logout</span>
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
@endif
