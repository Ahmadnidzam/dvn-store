<div class="store-nav-wrap">
    <ul class="nav nav-pills justify-content-center gap-3">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" 
               href="{{ url('/dashboard') }}">
                <span class="material-symbols-outlined align-middle me-1">home</span>
                Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/game') ? 'active' : '' }}" 
               href="{{ url('/dashboard/game') }}">
                <span class="material-symbols-outlined align-middle me-1">stadia_controller</span>
                Game
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard/app') ? 'active' : '' }}"
               href="{{ url('/dashboard/app') }}">
                <span class="material-symbols-outlined align-middle me-1">apps</span>
                Aplikasi
            </a>
        </li>
    </ul>
</div>
