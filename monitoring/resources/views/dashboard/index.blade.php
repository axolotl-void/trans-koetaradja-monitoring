@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-group', 'Ringkasan')

@section('content')

{{-- ============ HERO: skor kesehatan + komposisi ============ --}}
<div class="dash-hero">

    <div class="hero-score">
        <div class="score-ring" style="--skor: {{ $skor }}">
            <svg viewBox="0 0 120 120" aria-hidden="true">
                <circle class="score-track" cx="60" cy="60" r="52"></circle>
                <circle class="score-fill" cx="60" cy="60" r="52"
                        stroke-dasharray="{{ round($skor * 3.267, 1) }} 326.7"></circle>
            </svg>

            <div class="score-text">
                <strong>{{ $skor ?? '—' }}</strong>
                <span>{{ $skor === null ? 'belum ada data' : 'skor' }}</span>
            </div>
        </div>

        <div class="score-copy">
            <span class="eyebrow">Kesehatan armada</span>
            <h2>
                @if($skor === null)
                    Belum ada komponen dipantau
                @elseif($skor >= 85)
                    Armada dalam kondisi baik
                @elseif($skor >= 60)
                    Ada komponen perlu perhatian
                @else
                    Banyak komponen perlu diganti
                @endif
            </h2>
            <p>
                @if($skor === null)
                    {{ $busAktif }} bus aktif terdaftar, tapi belum ada komponen yang dicatat.
                @else
                    {{ $totalMonitoring }} komponen di {{ $busTerpantau }} dari {{ $busAktif }} bus aktif.
                @endif
                @if($skor === null)
                    Tambah komponen supaya kondisi armada bisa dinilai.
                @elseif($totalPeringatan + $totalWajibGanti > 0)
                    {{ $totalPeringatan + $totalWajibGanti }} perlu ditindak.
                @elseif($busTerpantau < $busAktif)
                    {{ $busAktif - $busTerpantau }} bus belum punya data komponen.
                @else
                    Semua dalam batas aman.
                @endif
            </p>

            <div class="hero-actions">
                <a href="{{ url('/monitoring') }}" class="btn-add">
                    <i class="bi bi-activity"></i>
                    Lihat Monitoring
                </a>
                <a href="{{ url('/maintenance') }}" class="btn-secondary">
                    <i class="bi bi-clock-history"></i>
                    Riwayat
                </a>
            </div>
        </div>
    </div>

    <div class="hero-compare">
        <span class="eyebrow">Komposisi status komponen</span>

        <div class="compare-bar">
            @php
                $pctAman = $totalMonitoring ? round($totalAman / $totalMonitoring * 100) : 0;
                $pctPer  = $totalMonitoring ? round($totalPeringatan / $totalMonitoring * 100) : 0;
                $pctWajib= $totalMonitoring ? round($totalWajibGanti / $totalMonitoring * 100) : 0;
            @endphp

            @if($pctAman > 0)
                <span class="compare-seg seg-aman" style="--w: {{ $pctAman }}"
                      title="Aman {{ $totalAman }}"></span>
            @endif
            @if($pctPer > 0)
                <span class="compare-seg seg-peringatan" style="--w: {{ $pctPer }}"
                      title="Peringatan {{ $totalPeringatan }}"></span>
            @endif
            @if($pctWajib > 0)
                <span class="compare-seg seg-wajib" style="--w: {{ $pctWajib }}"
                      title="Wajib ganti {{ $totalWajibGanti }}"></span>
            @endif
        </div>

        <div class="compare-legend">
            <a href="{{ url('/monitoring?status=aman') }}" class="compare-item">
                <span class="compare-dot seg-aman"></span>
                <span class="compare-label">Aman</span>
                <strong>{{ $totalAman }}</strong>
            </a>

            <a href="{{ url('/monitoring?status=peringatan') }}" class="compare-item">
                <span class="compare-dot seg-peringatan"></span>
                <span class="compare-label">Peringatan</span>
                <strong>{{ $totalPeringatan }}</strong>
            </a>

            <a href="{{ url('/monitoring?status=wajib_ganti') }}" class="compare-item">
                <span class="compare-dot seg-wajib"></span>
                <span class="compare-label">Wajib ganti</span>
                <strong>{{ $totalWajibGanti }}</strong>
            </a>
        </div>
    </div>

</div>

{{-- ============ METRIK RINGKAS ============ --}}
<div class="dash-metrics">

    <a href="{{ url('/buses') }}" class="metric-card">
        <div class="metric-top">
            <span class="metric-label">Total armada</span>
            <i class="bi bi-bus-front"></i>
        </div>
        <strong class="metric-value">{{ $totalBus }}</strong>
        <span class="metric-foot">{{ $busAktif }} aktif beroperasi</span>
    </a>

    <a href="{{ url('/inventory') }}" class="metric-card">
        <div class="metric-top">
            <span class="metric-label">Komponen</span>
            <i class="bi bi-gear"></i>
        </div>
        <strong class="metric-value">{{ $totalMonitoring }}</strong>
        <span class="metric-foot">sedang dipantau</span>
    </a>

    <a href="{{ url('/maintenance') }}" class="metric-card">
        <div class="metric-top">
            <span class="metric-label">Penggantian</span>
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <strong class="metric-value">{{ $totalMaintenance }}</strong>
        <span class="metric-foot">total tercatat</span>
    </a>

    <a href="{{ url('/inventory') }}" class="metric-card">
        <div class="metric-top">
            <span class="metric-label">Stok tersedia</span>
            <i class="bi bi-box-seam"></i>
        </div>
        <strong class="metric-value">{{ $komponenTersedia ?? 0 }}</strong>
        <span class="metric-foot">belum dipasang</span>
    </a>

</div>

{{-- ============ STATUS PER BUS ============ --}}
<div class="section-head">
    <div>
        <h2>Status per Armada</h2>
        <p>Kondisi komponen tiap bus</p>
    </div>

    <a href="{{ url('/buses') }}" class="widget-link">
        Lihat semua
        <i class="bi bi-arrow-right"></i>
    </a>
</div>

<div class="armada-grid">
    @foreach($statusArmada->take(4) as $bus)
        <div class="armada-card armada-{{ $bus->terburuk }}" style="--i: {{ $loop->index }}">

            <div class="armada-head">
                <div class="armada-icon">
                    <i class="bi bi-bus-front-fill"></i>
                </div>

                <div class="armada-title">
                    <strong>{{ $bus->nomor_plat }}</strong>
                    <small>
                        @if($bus->nomor_lambung) {{ $bus->nomor_lambung }} &middot; @endif
                        {{ $bus->merk_bus ?: 'Merk belum diisi' }}
                    </small>
                </div>

                <span class="status-pill status-{{ $bus->terburuk }}">
                    <span class="status-dot"></span>
                    {{ \App\Services\ComponentStatusService::label($bus->terburuk) }}
                </span>
            </div>

            <div class="armada-body">
                @if($bus->jumlah_komponen > 0)
                    @foreach($bus->komponen->take(3) as $k)
                        <a href="{{ url('/monitoring/' . $k->id . '/ganti') }}" class="armada-item">
                            <span class="armada-item-dot status-{{ $k->status_hasil }}"></span>
                            <span class="armada-item-name">{{ $k->nama_alat ?? $k->inventory_kode_alat_ref }}</span>
                            <span class="armada-item-sisa">
                                @if(!is_null($k->sisa_km))
                                    {{ number_format($k->sisa_km, 0, ',', '.') }} KM
                                @elseif(!is_null($k->sisa_hari))
                                    {{ $k->sisa_hari }} hari
                                @endif
                            </span>
                        </a>
                    @endforeach

                    @if($bus->jumlah_komponen > 3)
                        <span class="armada-more">+{{ $bus->jumlah_komponen - 3 }} komponen lain</span>
                    @endif
                @else
                    <div class="armada-empty">
                        <i class="bi bi-gear"></i>
                        Belum ada komponen terpasang
                    </div>
                @endif
            </div>

            <a href="{{ url('/monitoring/create') }}" class="armada-add">
                <i class="bi bi-plus-lg"></i>
                Tambah komponen
            </a>
        </div>
    @endforeach
</div>

{{-- ============ GRAFIK + TIMELINE ============ --}}
<div class="dash-split">

    <div class="dash-panel">
        <div class="panel-head">
            <div>
                <h2>Penggantian Bulanan</h2>
                <p>12 bulan terakhir</p>
            </div>
        </div>

        <div class="chart-shell">
            <canvas id="maintenanceChart"></canvas>
        </div>
    </div>

    <div class="dash-panel">
        <div class="panel-head">
            <div>
                <h2>Aktivitas Terakhir</h2>
                <p>Penggantian komponen terbaru</p>
            </div>

            <a href="{{ url('/maintenance') }}" class="widget-link">Semua</a>
        </div>

        <div class="timeline">
            @forelse($maintenanceTerakhir as $log)
                <div class="timeline-item" style="--i: {{ $loop->index }}">
                    <span class="timeline-dot"></span>

                    <div class="timeline-body">
                        <div class="timeline-top">
                            <strong>{{ $log->bus_plat }}</strong>
                            <span>{{ \Carbon\Carbon::parse($log->tanggal_ganti)->translatedFormat('d M Y') }}</span>
                        </div>

                        <div class="timeline-swap">
                            <span class="swap-old">{{ $log->komponen_lama_nama ?? $log->komponen_lama_kode }}</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="swap-new">{{ $log->komponen_baru_nama ?? $log->komponen_baru_kode }}</span>
                        </div>

                        @if($log->km_saat_ganti)
                            <span class="timeline-km">{{ number_format($log->km_saat_ganti, 0, ',', '.') }} KM</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="widget-empty">
                    <i class="bi bi-clock-history"></i>
                    <strong>Belum ada aktivitas</strong>
                    <span>Penggantian komponen akan muncul di sini.</span>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ============ PERLU TINDAKAN ============ --}}
@if($perluPerhatian->isNotEmpty())
<div class="section-head">
    <div>
        <h2>Perlu Tindakan</h2>
        <p>Komponen paling mendesak untuk ditangani</p>
    </div>

    <a href="{{ url('/monitoring') }}" class="widget-link">Lihat semua</a>
</div>

<div class="action-grid">
    @foreach($perluPerhatian as $item)
        <a href="{{ url('/monitoring/' . $item->id . '/ganti') }}"
           class="action-card action-{{ $item->status_hasil }}" style="--i: {{ $loop->index }}">

            <div class="action-top">
                <span class="status-pill status-{{ $item->status_hasil }}">
                    <span class="status-dot"></span>
                    {{ \App\Services\ComponentStatusService::label($item->status_hasil) }}
                </span>

                <i class="bi bi-arrow-right-short"></i>
            </div>

            <strong class="action-name">{{ $item->nama_alat ?? $item->inventory_kode_alat_ref }}</strong>

            <span class="action-bus">
                <i class="bi bi-bus-front-fill"></i>
                {{ $item->bus_nomor_plat_ref }}
            </span>

            @if(!is_null($item->sisa_km) || !is_null($item->sisa_hari))
                <div class="action-sisa">
                    @if(!is_null($item->sisa_km))
                        <span class="sisa-big">{{ number_format($item->sisa_km, 0, ',', '.') }}</span>
                        <span class="sisa-unit">KM tersisa</span>
                    @else
                        <span class="sisa-big">{{ $item->sisa_hari }}</span>
                        <span class="sisa-unit">hari tersisa</span>
                    @endif
                </div>

                <div class="sisa-track">
                    <div class="sisa-fill" style="--w: {{ $item->sisa_bar_persen ?? 0 }}%"></div>
                </div>
            @endif
        </a>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('maintenanceChart');
    if (!el || !window.Chart) return;

    const labels = @json($maintenanceChart['labels']);
    const data = @json($maintenanceChart['data']);

    const ctx = el.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 220);
    grad.addColorStop(0, 'rgba(37, 99, 235, .28)');
    grad.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(el, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penggantian',
                data: data,
                backgroundColor: data.map((v, i) => i === data.length - 1 ? '#1d4ed8' : '#93b4f5'),
                hoverBackgroundColor: '#1d4ed8',
                borderRadius: 6,
                borderSkipped: false,
                barMaxBarThickness: 26
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 900,
                easing: 'easeOutQuart',
                delay: (c) => c.dataIndex * 55
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    displayColors: false,
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: (c) => c.parsed.y + ' penggantian'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 3,
                    grid: { color: '#f1f5f9' },
                    border: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 10 }, precision: 0, stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endpush
