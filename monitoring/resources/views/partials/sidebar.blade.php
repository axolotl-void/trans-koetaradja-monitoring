<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-bus-front-fill"></i>
        </div>

        <div class="brand-copy">
            <h2>TRANS KOETARADJA</h2>
            <p>MONITORING ASSET</p>
        </div>

        <button type="button" class="sidebar-close" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="menu">

        <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('/buses') }}" class="{{ request()->is('buses*') ? 'active' : '' }}">
            <i class="bi bi-bus-front"></i>
            <span>Data Bus</span>
        </a>

        <a href="{{ url('/inventory') }}" class="{{ request()->is('inventory*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Data Komponen</span>
        </a>

        <a href="{{ url('/monitoring') }}" class="{{ request()->is('monitoring*') ? 'active' : '' }}">
            <i class="bi bi-activity"></i>
            <span>Monitoring</span>
        </a>

        <a href="{{ url('/odometer') }}" class="{{ request()->is('odometer*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Riwayat Kilometer</span>
        </a>

        <a href="{{ url('/maintenance') }}" class="{{ request()->is('maintenance*') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check"></i>
            <span>Riwayat Maintenance</span>
        </a>

        <a href="{{ url('/profil') }}" class="{{ request()->is('profil*') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>Profil</span>
        </a>

    </nav>

    {{-- ringkasan operasional: satu sistem rail dengan menu --}}
    <div class="sidebar-panel">
        <span class="sidebar-panel-title">Ringkasan hari ini</span>

        <a href="{{ url('/buses') }}" class="sidebar-stat">
            <span class="sidebar-stat-icon">
                <i class="bi bi-bus-front-fill"></i>
            </span>

            <span class="sidebar-stat-copy">
                <span class="sidebar-stat-value">{{ number_format($sidebarStat['bus'] ?? 0) }}</span>
                <span class="sidebar-stat-label">Bus aktif</span>
            </span>
        </a>

        <a href="{{ url('/monitoring') }}" class="sidebar-stat">
            <span class="sidebar-stat-icon">
                <i class="bi bi-gear-fill"></i>
            </span>

            <span class="sidebar-stat-copy">
                <span class="sidebar-stat-value">{{ number_format($sidebarStat['komponen'] ?? 0) }}</span>
                <span class="sidebar-stat-label">Komponen dipantau</span>
            </span>
        </a>
    </div>

    {{-- foto armada di dasar sidebar --}}
    <a href="{{ url('/buses') }}" class="sidebar-photo" title="Lihat data armada">
        <img src="{{ asset('assets/images/bus-sidebar-opt.jpg') }}"
             alt="Bus Trans Koetaradja di jalan"
             width="500" height="360"
             loading="lazy" decoding="async">

        <span class="sidebar-photo-shade" aria-hidden="true"></span>

        <span class="sidebar-photo-copy">
            <strong>Armada Trans Koetaradja</strong>
            <span>Melayani dengan hati</span>
        </span>

        <span class="sidebar-photo-arrow" aria-hidden="true">
            <i class="bi bi-arrow-right"></i>
        </span>
    </a>

</aside>
