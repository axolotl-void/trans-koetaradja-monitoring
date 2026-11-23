@extends('layouts.app')

@section('title', 'Detail Komponen')
@section('page-title', 'Detail Komponen')
@section('page-group', 'Komponen')

@section('content')

@php
    $statusLabel = ['tersedia' => 'Stok', 'terpasang' => 'Dipakai', 'rusak' => 'Rusak'][$item->status_barang] ?? '-';
    $statusClass = ['tersedia' => 'status-tersedia', 'terpasang' => 'status-terpasang', 'rusak' => 'status-rusak'][$item->status_barang] ?? 'status-netral';
    $busAktif = $monitoring->bus_nomor_plat_ref ?? null;
@endphp

<div class="detail-grid">

    {{-- ============ KOLOM KIRI: ringkasan ============ --}}
    <div class="detail-main">

        <div class="card">
            <div class="detail-hero">

                <div class="detail-icon">
                    <i class="bi bi-gear-fill"></i>
                </div>

                <div class="detail-hero-copy">
                    <h2>{{ $item->nama_alat ?? '-' }}</h2>
                    <span class="detail-code">{{ $item->kode_alat ?? '-' }}</span>
                </div>

                <span class="status-pill {{ $statusClass }}">
                    <span class="status-dot"></span>
                    {{ $statusLabel }}
                </span>
            </div>

            <div class="detail-body">

                {{-- pemasangan --}}
                <div class="detail-block">
                    <h3 class="form-section-title">
                        <i class="bi bi-bus-front"></i>
                        Pemasangan
                    </h3>

                    <div class="detail-pairs">
                        <div class="detail-pair">
                            <span>Dipasang di</span>
                            <strong>
                                @if($busAktif)
                                    <span class="bus-chip">
                                        <i class="bi bi-bus-front-fill"></i>
                                        {{ $busAktif }}
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>

                        <div class="detail-pair">
                            <span>Tanggal acuan</span>
                            <strong>
                                @if($monitoring && $monitoring->tanggal_acuan)
                                    {{ \Carbon\Carbon::parse($monitoring->tanggal_acuan)->format('d/m/Y') }}
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>

                        <div class="detail-pair">
                            <span>Jenis monitoring</span>
                            <strong>
                                @if($monitoring)
                                    {{ ucfirst($monitoring->jenis_monitoring) }}
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>

                        <div class="detail-pair">
                            <span>Sisa masa pakai</span>
                            <strong>
                                @if($monitoring && (!is_null($monitoring->sisa_km) || !is_null($monitoring->sisa_hari)))
                                    @if(!is_null($monitoring->sisa_km))
                                        {{ number_format($monitoring->sisa_km, 0, ',', '.') }} KM
                                    @endif
                                    @if(!is_null($monitoring->sisa_hari))
                                        {{ $monitoring->sisa_hari }} hari
                                    @endif
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>
                    </div>

                    @if($monitoring)
                        <div class="detail-monitor-note">
                            Status monitoring:
                            <span class="status-pill status-{{ $monitoring->status_hasil }}">
                                <span class="status-dot"></span>
                                {{ $monitoring->status_label }}
                            </span>
                            @if($monitoring->km_sekarang)
                                <span class="detail-muted">Odometer bus {{ number_format($monitoring->km_sekarang, 0, ',', '.') }} KM</span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- identitas --}}
                <div class="detail-block">
                    <h3 class="form-section-title">
                        <i class="bi bi-upc-scan"></i>
                        Identitas
                    </h3>

                    <div class="detail-pairs">
                        <div class="detail-pair">
                            <span>Kategori</span>
                            <strong>
                                @if($item->kategori)
                                    <span class="kategori-chip">{{ ucfirst($item->kategori) }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>

                        <div class="detail-pair">
                            <span>Merek</span>
                            <strong>{{ $item->merk ?: '—' }}</strong>
                        </div>

                        <div class="detail-pair">
                            <span>Seri</span>
                            <strong>{{ $item->seri ?: '—' }}</strong>
                        </div>

                        <div class="detail-pair">
                            <span>Kondisi awal</span>
                            <strong>{{ $item->kondisi_awal ?: '—' }}</strong>
                        </div>
                    </div>
                </div>

                {{-- pengadaan --}}
                <div class="detail-block">
                    <h3 class="form-section-title">
                        <i class="bi bi-receipt"></i>
                        Pengadaan
                    </h3>

                    <div class="detail-pairs">
                        <div class="detail-pair">
                            <span>Supplier</span>
                            <strong>{{ $item->supplier ?: '—' }}</strong>
                        </div>

                        <div class="detail-pair">
                            <span>Tanggal beli</span>
                            <strong>
                                @if($item->tanggal_pembelian)
                                    {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d/m/Y') }}
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>

                        <div class="detail-pair detail-pair-wide">
                            <span>Harga beli</span>
                            <strong class="detail-price">
                                @if($item->harga_beli !== null && $item->harga_beli !== '')
                                    Rp {{ number_format((float) $item->harga_beli, 0, ',', '.') }}
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- riwayat maintenance --}}
        <div class="card">
            <div class="card-header">
                <h2>Riwayat Maintenance</h2>
                <p>Riwayat pemasangan dan penggantian komponen ini</p>
            </div>

            <div class="card-body">
                <div class="table-wrapper">
                    <table class="bus-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Bus</th>
                                <th>Diganti dengan</th>
                                <th class="num">KM</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($riwayat as $log)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($log->tanggal_ganti)->format('d/m/Y') }}</td>
                                    <td>{{ $log->bus_plat }}</td>
                                    <td>
                                        @if($log->komponen_baru_kode === $item->kode_alat)
                                            <span class="badge badge-aman">Dipasang</span>
                                            {{ $log->komponen_baru_nama ?? $log->komponen_baru_kode }}
                                        @else
                                            <span class="badge badge-nonaktif">Dilepas</span>
                                            diganti {{ $log->komponen_baru_nama ?? $log->komponen_baru_kode }}
                                        @endif
                                    </td>
                                    <td class="num">
                                        {{ $log->km_saat_ganti ? number_format($log->km_saat_ganti, 0, ',', '.') : '—' }}
                                    </td>
                                    <td>{{ $log->user_petugas ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-clock-history"></i>
                                            <strong>Belum ada riwayat</strong>
                                            <span>Komponen ini belum pernah diganti atau dipasang lewat menu Monitoring.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ============ KOLOM KANAN: aksi ============ --}}
    <aside class="detail-aside">

        <div class="card detail-action-card">
            <h3>Tindakan</h3>

            <a href="/inventory/{{ $item->id }}/edit" class="btn-primary detail-action">
                <i class="bi bi-pencil-square"></i>
                Ubah Komponen
            </a>

            @if($monitoring)
                <a href="/monitoring/{{ $monitoring->id }}/ganti" class="btn-secondary detail-action">
                    <i class="bi bi-arrow-repeat"></i>
                    Ganti Komponen
                </a>
            @else
                <a href="/monitoring/create" class="btn-secondary detail-action">
                    <i class="bi bi-plus-lg"></i>
                    Pasang ke Bus
                </a>
            @endif

            <a href="/inventory" class="btn-secondary detail-action">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="card detail-action-card">
            <h3>Zona berbahaya</h3>

            <p class="detail-muted">
                Menghapus komponen menghilangkan datanya secara permanen.
                Komponen yang sedang dipasang dikunci oleh sistem.
            </p>

            @if($busAktif)
                <button type="button" class="form-danger-link detail-action" disabled
                        title="Komponen sedang dipasang di {{ $busAktif }}">
                    <i class="bi bi-lock"></i>
                    Hapus Komponen
                </button>

                <p class="detail-muted" style="margin:0;">
                    Tombol dinonaktifkan karena komponen masih terpasang di
                    <strong>{{ $busAktif }}</strong>. Ganti atau lepas dulu di menu Monitoring.
                </p>
            @else
                <form action="/inventory/{{ $item->id }}" method="POST"
                      data-confirm="Hapus komponen {{ $item->nama_alat }}? Tindakan ini tidak bisa dibatalkan.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="form-danger-link detail-action">
                        <i class="bi bi-trash"></i>
                        Hapus Komponen
                    </button>
                </form>
            @endif
        </div>

        <div class="card detail-action-card detail-meta">
            <h3>Metadata</h3>

            <div class="detail-pair">
                <span>Dibuat</span>
                <strong>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : '—' }}</strong>
            </div>

            <div class="detail-pair">
                <span>Diperbarui</span>
                <strong>{{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') : '—' }}</strong>
            </div>

            <div class="detail-pair">
                <span>ID internal</span>
                <strong>#{{ $item->id }}</strong>
            </div>
        </div>

    </aside>

</div>

@endsection
