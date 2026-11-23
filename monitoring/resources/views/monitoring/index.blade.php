@extends('layouts.app')

@section('title', 'Monitoring')
@section('page-title', 'Monitoring Komponen')
@section('page-group', 'Monitoring')

@section('content')

{{-- ringkasan status --}}
<div class="fleet-summary">

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-total">
            <i class="bi bi-activity"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['total'] }}</span>
            <span class="fleet-label">Komponen dipantau</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-aktif">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['aman'] }}</span>
            <span class="fleet-label">Aman</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-service">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['peringatan'] }}</span>
            <span class="fleet-label">Peringatan</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-off">
            <i class="bi bi-arrow-repeat"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['wajib_ganti'] }}</span>
            <span class="fleet-label">Wajib ganti</span>
        </div>
    </div>

</div>

<div class="card">

    <div class="card-header card-header-row">
        <div>
            <h2>Status Komponen</h2>
            <p>Masa pakai komponen yang terpasang di armada</p>
        </div>

        <div class="aksi-wrap">
            <a href="/export/monitoring" class="btn-secondary">
                <i class="bi bi-download"></i>
                Export CSV
            </a>

            <a href="/monitoring/create" class="btn-add">
                <i class="bi bi-plus-lg"></i>
                Tambah Monitoring
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="/monitoring" class="table-toolbar">
            <select name="kategori" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                        {{ ucfirst($kategori) }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman</option>
                <option value="peringatan" {{ request('status') == 'peringatan' ? 'selected' : '' }}>Peringatan</option>
                <option value="wajib_ganti" {{ request('status') == 'wajib_ganti' ? 'selected' : '' }}>Wajib Ganti</option>
            </select>

            @if(request()->hasAny(['kategori', 'status']))
                <a href="/monitoring" class="toolbar-reset">Reset</a>
            @endif

            <span class="toolbar-count">
                @if(request()->hasAny(['kategori', 'status']))
                    {{ $monitoringAssets->count() }} dari {{ $ringkasan['total'] }} komponen
                @else
                    {{ $monitoringAssets->count() }} komponen
                @endif
            </span>
        </form>

        <div class="table-wrapper">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Komponen</th>
                        <th>Bus</th>
                        <th>Jenis</th>
                        <th>Acuan</th>
                        <th>Sisa</th>
                        <th class="col-komponen">Status</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($monitoringAssets as $i => $item)
                        <tr class="row-in" style="--i: {{ $i }}">

                            <td class="col-no" data-label="No">{{ $i + 1 }}</td>

                            <td data-label="Komponen">
                                <div class="plate-cell">
                                    <div class="plate-avatar">
                                        <i class="bi bi-gear-fill"></i>
                                    </div>

                                    <div class="plate-copy">
                                        <div class="plate-line">
                                            <strong>{{ $item->nama_alat ?? $item->inventory_kode_alat_ref }}</strong>
                                        </div>

                                        <small>{{ $item->inventory_kode_alat_ref }}</small>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Bus">
                                <span class="bus-chip">
                                    <i class="bi bi-bus-front-fill"></i>
                                    {{ $item->bus_nomor_plat_ref }}
                                </span>
                            </td>

                            <td data-label="Jenis">
                                <span class="jenis-chip">{{ ucfirst($item->jenis_monitoring) }}</span>
                            </td>

                            <td data-label="Acuan">
                                <div class="merk-cell">
                                    @if($item->tanggal_acuan)
                                        <strong>{{ \Carbon\Carbon::parse($item->tanggal_acuan)->format('d/m/Y') }}</strong>
                                    @endif
                                    @if($item->km_acuan)
                                        <small>{{ number_format($item->km_acuan, 0, ',', '.') }} KM</small>
                                    @endif
                                    @if(!$item->tanggal_acuan && !$item->km_acuan)
                                        <span class="dash">—</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Sisa">
                                @php
                                    $sisaKm = $item->sisa_km;
                                    $sisaHari = $item->sisa_hari;
                                @endphp

                                @if(!is_null($sisaKm) || !is_null($sisaHari))
                                    <div class="sisa-cell">
                                        @if(!is_null($sisaKm))
                                            <strong class="sisa-num">{{ number_format($sisaKm, 0, ',', '.') }} <span>KM</span></strong>
                                        @endif
                                        @if(!is_null($sisaHari))
                                            <strong class="sisa-num">{{ $sisaHari }} <span>hari</span></strong>
                                        @endif

                                        @php
                                            $persen = $item->sisa_bar_persen ?? 0;
                                            $lebar = $persen > 0 ? max(4, $persen) : 4;
                                        @endphp

                                        <div class="sisa-bar"
                                             title="Sisa {{ $persen }}% dari batas masa pakai">
                                            <div class="sisa-bar-fill sisa-bar-{{ $item->status_hasil }}"
                                                 style="width: {{ $lebar }}%"></div>
                                        </div>

                                        @if(!is_null($sisaKm) && $item->km_sekarang)
                                            <small class="text-secondary">odo {{ number_format($item->km_sekarang, 0, ',', '.') }} KM</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td class="col-komponen" data-label="Status">
                                <span class="status-pill status-{{ $item->status_hasil }}">
                                    <span class="status-dot"></span>
                                    {{ \App\Services\ComponentStatusService::label($item->status_hasil) }}
                                </span>
                            </td>

                            <td class="col-aksi" data-label="Aksi">
                                <div class="aksi-wrap">
                                    <a href="/monitoring/{{ $item->id }}/ganti" class="btn-icon btn-icon-edit"
                                       title="Ganti komponen {{ $item->nama_alat }}" aria-label="Ganti komponen {{ $item->nama_alat }}">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </a>

                                    <a href="/monitoring/{{ $item->id }}/edit" class="btn-icon btn-icon-edit"
                                       title="Ubah monitoring {{ $item->nama_alat }}" aria-label="Ubah monitoring {{ $item->nama_alat }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="/monitoring/{{ $item->id }}/reset" method="POST"
                                          data-confirm="Reset pengingat komponen ini dari hari ini? Tanggal acuan dan KM akan diperbarui, riwayat maintenance tidak terpengaruh.">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-icon-reset"
                                                title="Reset pengingat {{ $item->nama_alat }}" aria-label="Reset pengingat {{ $item->nama_alat }}">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-activity"></i>
                                    <strong>{{ request()->hasAny(['kategori', 'status']) ? 'Tidak ada komponen yang cocok' : 'Belum ada komponen dipantau' }}</strong>
                                    <span>
                                        {{ request()->hasAny(['kategori', 'status'])
                                            ? 'Coba kategori atau status lain.'
                                            : 'Pasang komponen ke bus lewat menu Tambah Monitoring untuk mulai memantau masa pakainya.' }}
                                    </span>

                                    @if(request()->hasAny(['kategori', 'status']))
                                        <a href="/monitoring" class="btn-secondary">Reset filter</a>
                                    @else
                                        <a href="/monitoring/create" class="btn-add">Tambah Monitoring</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
