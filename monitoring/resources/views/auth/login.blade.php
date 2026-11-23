<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Monitoring Trans Koetaradja</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}?v={{ filemtime(public_path('assets/css/login.css')) }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
</head>

<body>

<div class="login-wrap">

    {{-- ============ PANEL KIRI: brand + foto bus ============ --}}
    <aside class="login-visual" aria-hidden="true">
        <div class="visual-photo"></div>
        <div class="visual-veil"></div>

        <span class="visual-orb orb-a"></span>
        <span class="visual-orb orb-b"></span>
        <span class="visual-orb orb-c"></span>

        <div class="visual-content">
            <div class="visual-brand">
                <span class="visual-mark">
                    <i class="bi bi-bus-front-fill"></i>
                </span>
                <span class="visual-brand-copy">
                    <strong>TRANS KOETARADJA</strong>
                    <small>Monitoring Asset</small>
                </span>
            </div>

            <h2 class="visual-title">
                Pantau armada<br>
                <span>tanpa kehilangan jejak.</span>
            </h2>

            <p class="visual-sub">
                Kilometer, masa pakai komponen, dan riwayat penggantian
                dalam satu tempat.
            </p>

            <ul class="visual-points">
                <li>
                    <i class="bi bi-check2"></i>
                    59 armada terdaftar
                </li>
                <li>
                    <i class="bi bi-check2"></i>
                    9 jenis komponen dipantau
                </li>
                <li>
                    <i class="bi bi-check2"></i>
                    Riwayat penggantian tersimpan
                </li>
            </ul>
        </div>

        <div class="visual-foot">
            <i class="bi bi-geo-alt-fill"></i>
            Banda Aceh, Indonesia
        </div>
    </aside>

    {{-- ============ PANEL KANAN: form ============ --}}
    <main class="login-panel">

        {{-- status ringkas: mengisi ruang kosong, isinya nyata dari DB --}}
        <div class="login-strip">
            <div class="strip-item">
                <span class="strip-icon strip-icon-bus">
                    <i class="bi bi-bus-front-fill"></i>
                </span>
                <span class="strip-copy">
                    <strong>{{ number_format($ringkas['bus'] ?? 0, 0, ',', '.') }}</strong>
                    <small>Armada</small>
                </span>
            </div>

            <span class="strip-sep" aria-hidden="true"></span>

            <div class="strip-item">
                <span class="strip-icon strip-icon-gear">
                    <i class="bi bi-gear-fill"></i>
                </span>
                <span class="strip-copy">
                    <strong>{{ number_format($ringkas['komponen'] ?? 0, 0, ',', '.') }}</strong>
                    <small>Komponen</small>
                </span>
            </div>

            <span class="strip-sep" aria-hidden="true"></span>

            <div class="strip-item">
                <span class="strip-icon strip-icon-hist">
                    <i class="bi bi-clock-history"></i>
                </span>
                <span class="strip-copy">
                    <strong>{{ number_format($ringkas['riwayat'] ?? 0, 0, ',', '.') }}</strong>
                    <small>Riwayat</small>
                </span>
            </div>
        </div>

        <div class="login-card">

            <div class="card-head">
                <span class="card-icon">
                    <i class="bi bi-bus-front-fill"></i>
                </span>

                <h1>Selamat datang</h1>
                <p>Masuk untuk memantau kondisi armada.</p>
            </div>

            @if(session('error'))
                <div class="login-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="login-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST" id="loginForm">
                @csrf

                <div class="login-field">
                    <label for="email">Email</label>

                    <div class="login-input">
                        <i class="bi bi-envelope"></i>

                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               placeholder="nama@transkoetaradja.test"
                               autocomplete="username"
                               required
                               autofocus>
                    </div>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>

                    <div class="login-input">
                        <i class="bi bi-lock"></i>

                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="Masukkan password"
                               autocomplete="current-password"
                               required>

                        <button type="button" class="login-eye" id="eyeBtn"
                                aria-label="Lihat password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="login-row">
                    <label class="login-check">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya</span>
                    </label>

                    <button type="button" class="login-help" id="helpBtn">
                        Butuh bantuan?
                    </button>
                </div>

                <button type="submit" class="login-submit" id="submitBtn">
                    <span class="submit-label">Masuk</span>
                    <span class="submit-spinner" aria-hidden="true"></span>
                </button>
            </form>

            <p class="login-note">
                <i class="bi bi-shield-lock"></i>
                Akses terbatas untuk petugas monitoring.
            </p>
        </div>

        <div class="login-foot">
            <span class="foot-line" aria-hidden="true"></span>

            <p class="login-copy">
                &copy; {{ date('Y') }} Trans Koetaradja &middot; Monitoring Asset
            </p>

            <p class="login-ver">
                <span class="ver-dot" aria-hidden="true"></span>
                Sistem internal &middot; akses terbatas
            </p>
        </div>
    </main>

</div>

<script>
(function () {
    'use strict';

    var eye = document.getElementById('eyeBtn');
    var pass = document.getElementById('password');

    if (eye && pass) {
        eye.addEventListener('click', function () {
            var lihat = pass.type === 'password';

            pass.type = lihat ? 'text' : 'password';
            eye.innerHTML = lihat
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';
            eye.setAttribute('aria-label', lihat ? 'Sembunyikan password' : 'Lihat password');
            pass.focus();
        });
    }

    // kunci tombol setelah submit supaya tidak dobel
    var form = document.getElementById('loginForm');
    var btn = document.getElementById('submitBtn');

    if (form && btn) {
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) return;

            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    }
})();
</script>

</body>
</html>
