@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Profil')
@section('page-group', 'Pengaturan')

@section('content')

<div class="detail-grid">

    {{-- ============ KOLOM KIRI ============ --}}
    <div class="detail-main">

        {{-- kartu akun --}}
        <div class="card">
            <div class="detail-hero">
                <div class="detail-icon detail-icon-user">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>

                <div class="detail-hero-copy">
                    <h2>{{ $user->name ?? 'Admin' }}</h2>
                    <span class="detail-code">{{ $user->email ?? '—' }}</span>
                </div>

                <span class="status-pill status-tersedia">
                    <span class="status-dot"></span>
                    Administrator
                </span>
            </div>

            <div class="detail-body">
                <div class="detail-pairs">
                    <div class="detail-pair">
                        <span>Nama</span>
                        <strong>{{ $user->name ?? '—' }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Email</span>
                        <strong>{{ $user->email ?? '—' }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Terdaftar</span>
                        <strong>
                            {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') : '—' }}
                        </strong>
                    </div>

                    <div class="detail-pair">
                        <span>Terakhir diperbarui</span>
                        <strong>
                            {{ $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y H:i') : '—' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- statistik data --}}
        <div class="card">
            <div class="card-header">
                <h2>Statistik Data</h2>
                <p>Isi database saat ini</p>
            </div>

            <div class="card-body">
                <div class="stat-grid">
                    <div class="stat-box">
                        <span class="stat-box-value">{{ number_format($statistik['bus'], 0, ',', '.') }}</span>
                        <span class="stat-box-label">Total bus</span>
                        <small>{{ $statistik['bus_aktif'] }} aktif</small>
                    </div>

                    <div class="stat-box">
                        <span class="stat-box-value">{{ number_format($statistik['komponen'], 0, ',', '.') }}</span>
                        <span class="stat-box-label">Komponen</span>
                        <small>{{ $statistik['dipantau'] }} dipantau</small>
                    </div>

                    <div class="stat-box">
                        <span class="stat-box-value">{{ number_format($statistik['maintenance'], 0, ',', '.') }}</span>
                        <span class="stat-box-label">Penggantian</span>
                        <small>riwayat maintenance</small>
                    </div>

                    <div class="stat-box">
                        <span class="stat-box-value">{{ number_format($statistik['odometer'], 0, ',', '.') }}</span>
                        <span class="stat-box-label">Catatan odometer</span>
                        <small>kilometer</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- kelola akun --}}
        <div class="card">
            <div class="card-header">
                <h2>Kelola Akun</h2>
                <p>{{ $akun->count() }} akun punya akses ke aplikasi ini</p>
            </div>

            <div class="card-body">
                <div class="akun-list">
                    @foreach($akun as $a)
                        <div class="akun-row">
                            <span class="akun-avatar">{{ strtoupper(substr($a->name, 0, 1)) }}</span>

                            <div class="akun-copy">
                                <strong>
                                    {{ $a->name }}
                                    @if($a->id === $user->id)
                                        <span class="akun-you">Anda</span>
                                    @endif
                                </strong>
                                <span>{{ $a->email }}</span>
                            </div>

                            <span class="akun-date">
                                {{ $a->created_at ? \Carbon\Carbon::parse($a->created_at)->format('d/m/Y') : '—' }}
                            </span>

                            @if($a->id !== $user->id)
                                <form action="/profil/akun/{{ $a->id }}" method="POST"
                                      data-confirm="Hapus akun {{ $a->email }}? Akun ini tidak akan bisa masuk lagi.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-icon-delete" title="Hapus {{ $a->email }}"
                                            aria-label="Hapus {{ $a->email }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            @else
                                <span class="akun-lock" title="Akun yang sedang dipakai">
                                    <i class="bi bi-lock"></i>
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <details class="akun-add">
                    <summary>
                        <i class="bi bi-person-plus"></i>
                        <span>Tambah akun baru</span>
                        <i class="bi bi-chevron-down akun-add-caret"></i>
                    </summary>

                    <form action="/profil/akun" method="POST" data-validate class="akun-form">
                        @csrf

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="akun_nama">Nama</label>
                                <div class="input-wrap">
                                    <i class="bi bi-person"></i>
                                    <input type="text" name="name" id="akun_nama" value="{{ old('name') }}"
                                           placeholder="Nama pengguna" maxlength="100" required>
                                </div>
                                @error('name')
                                    <div class="field-hint hint-bad">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="akun_email">Email</label>
                                <div class="input-wrap">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" name="email" id="akun_email" value="{{ old('email') }}"
                                           placeholder="nama@transkoetaradja.test" maxlength="150" required>
                                </div>
                                @error('email')
                                    <div class="field-hint hint-bad">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="akun_password">Password</label>
                                <div class="input-wrap">
                                    <i class="bi bi-key"></i>
                                    <input type="password" name="password" id="akun_password"
                                           minlength="8" autocomplete="new-password" required>
                                </div>
                                <div class="field-hint">Minimal 8 karakter.</div>
                            </div>

                            <div class="form-group">
                                <label for="akun_password2">Ulangi password</label>
                                <div class="input-wrap">
                                    <i class="bi bi-key-fill"></i>
                                    <input type="password" name="password_confirmation" id="akun_password2"
                                           minlength="8" autocomplete="new-password" required>
                                </div>
                                <div class="field-hint" id="akunMatch"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="bi bi-person-check"></i>
                            Simpan Akun
                        </button>
                    </form>
                </details>
            </div>
        </div>

        {{-- informasi sistem --}}
        <div class="card">
            <div class="card-header">
                <h2>Informasi Sistem</h2>
                <p>Versi dan konfigurasi aplikasi</p>
            </div>

            <div class="card-body">
                <div class="detail-pairs">
                    <div class="detail-pair">
                        <span>Nama aplikasi</span>
                        <strong>{{ $sistem['aplikasi'] }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Laravel</span>
                        <strong>{{ $sistem['laravel'] }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>PHP</span>
                        <strong>{{ $sistem['php'] }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Database</span>
                        <strong>{{ $sistem['database'] === 'mysql' ? 'MySQL' : ucfirst($sistem['database']) }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Zona waktu</span>
                        <strong>{{ $sistem['zona'] }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Bahasa</span>
                        <strong>{{ $sistem['locale'] }}</strong>
                    </div>

                    <div class="detail-pair">
                        <span>Lingkungan</span>
                        <strong>
                            <span class="env-chip env-{{ $sistem['lingkungan'] }}">
                                {{ $sistem['lingkungan'] }}
                            </span>
                        </strong>
                    </div>

                    <div class="detail-pair">
                        <span>Mode debug</span>
                        <strong>
                            @if($sistem['debug'])
                                <span class="env-chip env-danger">aktif</span>
                            @else
                                <span class="env-chip env-safe">nonaktif</span>
                            @endif
                        </strong>
                    </div>

                    <div class="detail-pair detail-pair-wide">
                        <span>Waktu server</span>
                        <strong>{{ $sistem['waktu']->translatedFormat('d F Y, H:i') }} WIB</strong>
                    </div>
                </div>

                @if($sistem['debug'])
                    <div class="alert alert-danger" style="margin-top:18px; margin-bottom:0;">
                        <strong>Mode debug aktif.</strong>
                        Pesan error dan detail internal akan terlihat oleh siapa pun yang membuka aplikasi.
                        Matikan sebelum dipakai di server produksi.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ============ KOLOM KANAN ============ --}}
    <aside class="detail-aside">

        <div class="card detail-action-card">
            <h3>Keamanan</h3>

            <p class="detail-muted">
                Ganti password akun Anda. Sesi lain akan otomatis keluar setelah password berubah.
            </p>

            <form action="/profil/password" method="POST" data-validate>
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom:12px;">
                    <label for="password_lama">Password saat ini</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password_lama" id="password_lama" required
                               autocomplete="current-password">
                    </div>
                    @error('password_lama')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label for="password_baru">Password baru</label>
                    <div class="input-wrap">
                        <i class="bi bi-key"></i>
                        <input type="password" name="password" id="password_baru" required
                               minlength="8" autocomplete="new-password">
                    </div>
                    <div class="field-hint" id="pwHint">Minimal 8 karakter.</div>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label for="password_konfirmasi">Ulangi password baru</label>
                    <div class="input-wrap">
                        <i class="bi bi-key-fill"></i>
                        <input type="password" name="password_confirmation" id="password_konfirmasi" required
                               minlength="8" autocomplete="new-password">
                    </div>
                    <div class="field-hint" id="pwMatch"></div>
                </div>

                <button type="button" class="btn-secondary detail-action" id="pwToggle" style="margin-bottom:8px;">
                    <i class="bi bi-eye"></i>
                    Lihat password
                </button>

                <button type="submit" class="btn-primary detail-action">
                    <i class="bi bi-shield-lock"></i>
                    Simpan Password
                </button>
            </form>
        </div>

        <div class="card detail-action-card">
            <h3>Sesi</h3>

            <p class="detail-muted">
                Anda masuk sebagai <strong>{{ $user->email ?? '—' }}</strong>.
            </p>

            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="btn-secondary detail-action">
                    <i class="bi bi-box-arrow-right"></i>
                    Keluar
                </button>
            </form>
        </div>

    </aside>

</div>

@endsection

@push('scripts')
<script>
(function () {
    var baru = document.getElementById('password_baru');
    var ulang = document.getElementById('password_konfirmasi');
    var match = document.getElementById('pwMatch');
    var toggle = document.getElementById('pwToggle');

    if (baru && ulang && match) {
        var submit = document.querySelector('form[action="/profil/password"] button[type=submit]');

        function cek() {
            var panjangCukup = baru.value.length >= 8;
            var sama = ulang.value === baru.value;

            // kosong: jangan tampilkan error apa pun
            if (!baru.value && !ulang.value) {
                match.textContent = '';
                match.className = 'field-hint';
            } else if (!panjangCukup) {
                match.textContent = 'Minimal 8 karakter.';
                match.className = 'field-hint hint-bad';
            } else if (!ulang.value) {
                match.textContent = '';
                match.className = 'field-hint';
            } else if (sama) {
                match.textContent = 'Password cocok.';
                match.className = 'field-hint hint-ok';
            } else {
                match.textContent = 'Password belum sama.';
                match.className = 'field-hint hint-bad';
            }

            // tanda merah pada field yang bermasalah
            ulang.classList.toggle('is-invalid', !!ulang.value && !sama);

            // tombol hanya aktif kalau semua syarat terpenuhi
            if (submit) {
                var lama = document.getElementById('password_lama');
                submit.disabled = !(panjangCukup && sama && lama && lama.value.length > 0);
            }
        }

        baru.addEventListener('input', cek);
        ulang.addEventListener('input', cek);

        var lamaEl = document.getElementById('password_lama');
        if (lamaEl) {
            lamaEl.addEventListener('input', cek);
        }

        cek();
    }

    // form tambah akun: cek kecocokan password
    var akunPw = document.getElementById('akun_password');
    var akunPw2 = document.getElementById('akun_password2');
    var akunMatch = document.getElementById('akunMatch');

    if (akunPw && akunPw2 && akunMatch) {
        function cekAkun() {
            if (!akunPw.value && !akunPw2.value) {
                akunMatch.textContent = '';
                akunMatch.className = 'field-hint';
            } else if (akunPw.value.length < 8) {
                akunMatch.textContent = 'Minimal 8 karakter.';
                akunMatch.className = 'field-hint hint-bad';
            } else if (!akunPw2.value) {
                akunMatch.textContent = '';
                akunMatch.className = 'field-hint';
            } else if (akunPw.value === akunPw2.value) {
                akunMatch.textContent = 'Password cocok.';
                akunMatch.className = 'field-hint hint-ok';
            } else {
                akunMatch.textContent = 'Password belum sama.';
                akunMatch.className = 'field-hint hint-bad';
            }

            akunPw2.classList.toggle('is-invalid', !!akunPw2.value && akunPw.value !== akunPw2.value);
        }

        akunPw.addEventListener('input', cekAkun);
        akunPw2.addEventListener('input', cekAkun);
        cekAkun();
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            var fields = document.querySelectorAll('form[action="/profil/password"] input[type=password], form[action="/profil/password"] input[type=text]');
            var lihat = toggle.textContent.indexOf('Lihat') !== -1;

            fields.forEach(function (f) { f.type = lihat ? 'text' : 'password'; });

            toggle.innerHTML = lihat
                ? '<i class="bi bi-eye-slash"></i> Sembunyikan password'
                : '<i class="bi bi-eye"></i> Lihat password';
        });
    }
})();
</script>
@endpush
