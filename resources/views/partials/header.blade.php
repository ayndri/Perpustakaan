<header class="navbar-clean">
    <div class="header-container">
        <a href="{{ route('home') }}" class="brand-logo">
            <i class="fas fa-book" style="color: #AEDEFC;"></i>
            <span>Perpus<span style="font-weight: 300;">Kampus</span></span>
        </a>

        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.*') ? 'active' : '' }}">Koleksi Buku</a></li>
            </ul>

            <div class="separator"></div>

            <div class="auth-buttons">
                @if(Auth::guard('student')->check())
                <a href="{{ route('profile') }}" class="user-name" style="text-decoration: none;">
                    Hi, {{ strtok(Auth::guard('student')->user()->name, " ") }}
                </a>

                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="btn-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn-register">Daftar</a>
                @endif
            </div>
        </div>

        <div class="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>