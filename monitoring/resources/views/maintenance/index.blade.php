@extends('layouts.app')

@section('title', 'Riwayat Maintenance')
@section('page-title', 'Riwayat Maintenance')
@section('page-group', 'Maintenance')

@section('content')

{{-- ringkasan --}}
<div class="fleet-summary">

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-total">
            <i class="bi bi-clipboard2-check"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ number_format($ringkasan['total'], 0, ',', '.') }}</span>
            <span class="fleet-label">Total penggantian</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-aktif">
            <i class="bi bi-calendar3"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['bulan_ini'] }}</span>
            <span class="fleet-label">Bulan ini</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-service">
            <i class="bi bi-bus-front"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['bus'] }}</span>
            <span class="fleet-label">Bus tersentuh</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-off">
            <i class="bi bi-person-badge"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['petugas'] }}</span>
            <span class="fleet-label">Petugas terlibat</span>
        </div>
    </div>

</div>

<div class="card">

    <div class="card-header card-header-row">
        <div>
            <h2>Penggantian Komponen</h2>
            <p>Riwayat diurutkan dari penggantian terbaru</p>
        </div>

        <a href="/export/maintenance" class="btn-secondary">
            <i class="bi bi-download"></i>
            Export CSV
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="/maintenance" class="table-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="cari" value="{{ request('cari') }}"
                       placeholder="Plat bus, komponen, atau petugas...">
            </div>

            <input type="date" name="tanggal_mulai" class="toolbar-select"
                   value="{{ request('tanggal_mulai') }}" aria-label="Dari tanggal">

            <span class="toolbar-sep">s/d</span>

            <input type="date" name="tanggal_akhir" class="toolbar-select"
                   value="{{ request('tanggal_akhir') }}" aria-label="Sampai tanggal">

            <button type="submit" class="btn-secondary toolbar-submit">Cari</button>

            @if(request()->hasAny(['cari', 'tanggal_mulai', 'tanggal_akhir']))
                <a href="/maintenance" class="toolbar-reset">Reset</a>
            @endif

            <span class="toolbar-count">
                @if(request()->hasAny(['cari', 'tanggal_mulai', 'tanggal_akhir']))
                    {{ $maintenanceLogs->total() }} dari {{ $ringkasan['total'] }} catatan
                @else
                    {{ $maintenanceLogs->total() }} catatan
                @endif
            </span>
        </form>

        <div class="table-wrapper">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Bus</th>
                        <th>Penggantian</th>
                        <th class="num">KM Saat Ganti</th>
                        <th>Petugas</th>
                        <th>Catatan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($maintenanceLogs as $index => $item)
                        <tr class="row-in" style="--i: {{ $index }}">

                            <td class="col-no" data-label="No">
                                {{ $maintenanceLogs->firstItem() + $loop->index }}
                            </td>

                            <td data-label="Tanggal">
                                <div class="merk-cell">
                                    <strong>{{ \Carbon\Carbon::parse($item->tanggal_ganti)->format('d/m/Y') }}</strong>
                                    <small>{{ \Carbon\Carbon::parse($item->tanggal_ganti)->format('H:i') }} WIB</small>
                                </div>
                            </td>

                            <td data-label="Bus">
                                <span class="bus-chip">
                                    <i class="bi bi-bus-front-fill"></i>
                                    {{ $item->bus_plat }}
                                </span>
                            </td>

                            <td data-label="Penggantian">
                                @if($item->komponen_baru_kode === 'LAPORAN')
                                    {{-- laporan kerusakan: bukan penggantian, jangan pakai panah --}}
                                    <div class="lapor-cell">
                                        <span class="lapor-part">{{ $item->komponen_lama_nama }}</span>
                                        <span class="lapor-desc">{{ $item->komponen_baru_nama }}</span>
                                    </div>
                                @else
                                    <div class="swap-cell">
                                        <div class="swap-item swap-out">
                                            <span class="swap-label">Dilepas</span>
                                            <strong>{{ $item->komponen_lama_nama ?: '—' }}</strong>
                                            <small>{{ $item->komponen_lama_kode }}</small>
                                        </div>

                                        <i class="bi bi-arrow-right swap-arrow"></i>

                                        <div class="swap-item swap-in">
                                            <span class="swap-label">Dipasang</span>
                                            <strong>{{ $item->komponen_baru_nama ?: '—' }}</strong>
                                            <small>{{ $item->komponen_baru_kode }}</small>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <td class="num" data-label="KM">
                                @if($item->km_saat_ganti !== null)
                                    <span class="odo-value">{{ number_format($item->km_saat_ganti, 0, ',', '.') }}</span>
                                    <span class="odo-unit">KM</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Petugas">
                                @if($item->user_petugas)
                                    <span class="petugas-cell">
                                        <i class="bi bi-person"></i>
                                        {{ $item->user_petugas }}
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Catatan" class="catatan-cell">
                                @if(!$item->catatan)
                                    <span class="dash">—</span>
                                @elseif($item->komponen_baru_kode === 'LAPORAN')
                                    <span class="catatan-default" title="{{ $item->catatan }}">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        laporan kerusakan
                                    </span>
                                @elseif($item->catatan === 'Penggantian komponen melalui halaman monitoring.')
                                    <span class="catatan-default" title="{{ $item->catatan }}">
                                        <i class="bi bi-arrow-repeat"></i>
                                        via monitoring
                                    </span>
                                @else
                                    {{ $item->catatan }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-clipboard2-x"></i>
                                    <strong>{{ request()->hasAny(['cari', 'tanggal_mulai', 'tanggal_akhir']) ? 'Tidak ada catatan yang cocok' : 'Belum ada riwayat maintenance' }}</strong>
                                    <span>
                                        {{ request()->hasAny(['cari', 'tanggal_mulai', 'tanggal_akhir'])
                                            ? 'Coba kata kunci atau rentang tanggal lain.'
                                            : 'Riwayat muncul otomatis setelah penggantian komponen dicatat lewat menu Monitoring.' }}
                                    </span>

                                    @if(request()->hasAny(['cari', 'tanggal_mulai', 'tanggal_akhir']))
                                        <a href="/maintenance" class="btn-secondary">Reset filter</a>
                                    @else
                                        <a href="/monitoring" class="btn-add">Ke Monitoring</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($maintenanceLogs->hasPages())
            <div class="table-pagination">
                {{ $maintenanceLogs->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>
</div>

@endsection
