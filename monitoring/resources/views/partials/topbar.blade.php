<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="btn-menu" aria-label="Buka menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="topbar-page">
            <nav class="topbar-crumb" aria-label="Breadcrumb">
                <a href="{{ url('/dashboard') }}">Beranda</a>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                <span>@yield('page-group', 'Ringkasan')</span>
            </nav>

            <h1>@yield('page-title', 'Dashboard')</h1>
        </div>
    </div>

    <div class="topbar-right">

        {{-- pencarian cepat: kirim ke halaman yang relevan --}}
        <form action="{{ url('/buses') }}" method="GET" class="topbar-search" role="search">
            <i class="bi bi-search" aria-hidden="true"></i>

            <input type="search"
                   name="cari"
                   value="{{ request('cari') }}"
                   placeholder="Cari bus atau komponen…"
                   aria-label="Cari bus atau komponen"
                   autocomplete="off">

            <kbd aria-hidden="true">/</kbd>
        </form>

        {{-- notifikasi: komponen yang perlu ditindak --}}
        <div class="topbar-notif" id="notifWrap">
            <button type="button"
                    class="topbar-icon-btn"
                    id="notifBtn"
                    aria-label="Notifikasi"
                    aria-haspopup="true"
                    aria-expanded="false">
                <i class="bi bi-bell" aria-hidden="true"></i>

                @if(($topbarNotif['total'] ?? 0) > 0)
                    <span class="topbar-badge">{{ $topbarNotif['total'] > 9 ? '9+' : $topbarNotif['total'] }}</span>
                @endif
            </button>

            <div class="topbar-pop" id="notifPop" hidden>
                <div class="topbar-pop-head">
                    <strong>Perlu tindakan</strong>
                    <span>{{ $topbarNotif['total'] ?? 0 }} komponen</span>
                </div>

                @forelse($topbarNotif['items'] ?? [] as $n)
                    <a href="{{ url('/monitoring/' . $n['id'] . '/ganti') }}" class="topbar-pop-item">
                        <span class="pop-dot status-{{ $n['status'] }}"></span>

                        <span class="pop-copy">
                            <strong>{{ $n['nama_alat'] }}</strong>
                            <span>{{ $n['bus'] }}</span>
                        </span>

                        <span class="pop-meta">{{ $n['sisa_label'] }}</span>
                    </a>
                @empty
                    <div class="topbar-pop-empty">
                        <i class="bi bi-check-circle" aria-hidden="true"></i>
                        Semua komponen aman
                    </div>
                @endforelse

                <a href="{{ url('/monitoring') }}" class="topbar-pop-foot">
                    Lihat semua monitoring
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        {{-- profil: gabungan avatar + nama + menu --}}
        <div class="topbar-user" id="userWrap">
            <button type="button"
                    class="topbar-user-btn"
                    id="userBtn"
                    aria-haspopup="true"
                    aria-expanded="false">
                <span class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </span>

                <span class="admin-info">
                    <span class="admin-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <span class="admin-role">Administrator</span>
                </span>

                <i class="bi bi-chevron-down topbar-user-caret" aria-hidden="true"></i>
            </button>

            <div class="topbar-pop topbar-pop-user" id="userPop" hidden>
                <div class="topbar-pop-head topbar-pop-head-user">
                    <span class="topbar-avatar topbar-avatar-lg">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>

                    <span class="pop-copy">
                        <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                        <span>{{ auth()->user()->email ?? 'admin@transkoetaradja.test' }}</span>
                    </span>
                </div>

                <a href="{{ url('/profil') }}" class="topbar-pop-item">
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    <span class="pop-copy"><strong>Profil aplikasi</strong></span>
                </a>

                <a href="{{ url('/profil') }}#keamanan" class="topbar-pop-item">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    <span class="pop-copy"><strong>Ganti password</strong></span>
                </a>

                <form action="{{ url('/logout') }}" method="POST" class="topbar-pop-logout">
                    @csrf

                    <button type="submit">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
