@extends('layouts.app')

@section('title', 'Riwayat Kilometer')
@section('page-title', 'Riwayat Kilometer')
@section('page-group', 'Kilometer')

@section('content')

{{-- ringkasan --}}
<div class="fleet-summary">

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-total">
            <i class="bi bi-speedometer2"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['total'] }}</span>
            <span class="fleet-label">Catatan odometer</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-aktif">
            <i class="bi bi-bus-front"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['bus'] }}</span>
            <span class="fleet-label">Bus tercatat</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-service">
            <i class="bi bi-arrow-up-right-circle"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ number_format($ringkasan['tertinggi'], 0, ',', '.') }}</span>
            <span class="fleet-label">KM tertinggi</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-off">
            <i class="bi bi-calendar3"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['bulan_ini'] }}</span>
            <span class="fleet-label">Input bulan ini</span>
        </div>
    </div>

</div>

<div class="card">

    <div class="card-header card-header-row">
        <div>
            <h2>Riwayat Kilometer Bus</h2>
            <p>Catatan odometer armada Trans Koetaradja</p>
        </div>

        <div class="aksi-wrap">
            <a href="/export/odometer" class="btn-secondary">
                <i class="bi bi-download"></i>
                Export CSV
            </a>

            <a href="/odometer/create" class="btn-add">
                <i class="bi bi-plus-lg"></i>
                Input Kilometer
            </a>
        </div>
    </div>

    <div class="card-body">

        <form method="GET" action="/odometer" class="table-toolbar">
            <select name="bus" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua bus</option>
                @foreach($buses as $plat)
                    <option value="{{ $plat }}" {{ request('bus') == $plat ? 'selected' : '' }}>
                        {{ $plat }}
                    </option>
                @endforeach
            </select>

            @if(request('bus'))
                <a href="/odometer" class="toolbar-reset">Reset</a>
            @endif

            <span class="toolbar-count">
                @if(request('bus'))
                    {{ $odometerLogs->count() }} dari {{ $ringkasan['total'] }} catatan
                @else
                    {{ $odometerLogs->count() }} catatan
                @endif
            </span>
        </form>

        @if($busTanpaCatatan->isNotEmpty() && !request('bus'))
            <div class="alert alert-info-soft">
                <i class="bi bi-info-circle"></i>
                {{ $busTanpaCatatan->count() }} bus aktif belum punya catatan odometer:
                <strong>{{ $busTanpaCatatan->implode(', ') }}</strong>
            </div>
        @endif

        <div class="table-wrapper">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Bus</th>
                        <th>Tanggal</th>
                        <th class="num">Kilometer</th>
                        @php $adaSelisih = $odometerLogs->contains(fn ($l) => !is_null($l->selisih)); @endphp
                        @if($adaSelisih)
                            <th class="num">Selisih</th>
                        @endif
                        <th>Petugas</th>
                        <th>Catatan</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($odometerLogs as $i => $log)
                        <tr class="row-in" style="--i: {{ $i }}">

                            <td class="col-no" data-label="No">{{ $i + 1 }}</td>

                            <td data-label="Bus">
                                <div class="plate-cell">
                                    <div class="plate-avatar">
                                        <i class="bi bi-bus-front-fill"></i>
                                    </div>

                                    <div class="plate-copy">
                                        <div class="plate-line">
                                            <strong>{{ $log->bus_nomor_plat_ref }}</strong>
                                        </div>

                                        @if($log->terakhir)
                                            <small class="text-terakhir">
                                                <i class="bi bi-check2"></i> kilometer terakhir
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td data-label="Tanggal">
                                {{ \Carbon\Carbon::parse($log->tanggal_update)->format('d/m/Y') }}
                            </td>

                            <td class="num" data-label="Kilometer">
                                <strong class="odo-value">{{ number_format($log->kilometer, 0, ',', '.') }}</strong>
                                <span class="odo-unit">KM</span>
                            </td>

                            @if($adaSelisih)
                                <td class="num" data-label="Selisih">
                                    @if(!is_null($log->selisih))
                                        @if($log->selisih > 0)
                                            <span class="delta-chip delta-up">+{{ number_format($log->selisih, 0, ',', '.') }} KM</span>
                                        @elseif($log->selisih === 0)
                                            <span class="delta-chip delta-same">0 KM</span>
                                        @else
                                            <span class="delta-chip delta-down">{{ number_format($log->selisih, 0, ',', '.') }} KM</span>
                                        @endif
                                    @else
                                        <span class="dash" title="Catatan pertama untuk bus ini">—</span>
                                    @endif
                                </td>
                            @endif

                            <td data-label="Petugas">
                                @if($log->user_nama)
                                    <span class="petugas-cell">
                                        <i class="bi bi-person"></i>
                                        {{ $log->user_nama }}
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Catatan" class="catatan-cell">
                                {{ $log->catatan ?: '—' }}
                            </td>

                            <td class="col-aksi" data-label="Aksi">
                                <div class="aksi-wrap">
                                    <a href="/odometer/{{ $log->id }}/edit" class="btn-icon btn-icon-edit"
                                       title="Ubah catatan {{ $log->bus_nomor_plat_ref }}" aria-label="Ubah catatan {{ $log->bus_nomor_plat_ref }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $adaSelisih ? 8 : 7 }}">
                                <div class="empty-state">
                                    <i class="bi bi-speedometer2"></i>
                                    <strong>{{ request('bus') ? 'Tidak ada catatan untuk bus ini' : 'Belum ada riwayat kilometer' }}</strong>
                                    <span>
                                        {{ request('bus')
                                            ? 'Pilih bus lain atau reset filter.'
                                            : 'Input kilometer pertama untuk mulai melacak pemakaian armada.' }}
                                    </span>

                                    @if(request('bus'))
                                        <a href="/odometer" class="btn-secondary">Reset filter</a>
                                    @else
                                        <a href="/odometer/create" class="btn-add">Input Kilometer</a>
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
