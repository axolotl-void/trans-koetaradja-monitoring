@extends('layouts.app')

@section('title', 'Data Bus')
@section('page-title', 'Data Bus')
@section('page-group', 'Armada')

@section('content')

{{-- ringkasan armada --}}
<div class="fleet-summary">

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-total">
            <i class="bi bi-bus-front"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['total'] }}</span>
            <span class="fleet-label">Total armada</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-aktif">
            <i class="bi bi-check2-circle"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['aktif'] }}</span>
            <span class="fleet-label">Aktif</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-service">
            <i class="bi bi-tools"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['service'] }}</span>
            <span class="fleet-label">Diperbaiki</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-off">
            <i class="bi bi-pause-circle"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['nonaktif'] }}</span>
            <span class="fleet-label">Tidak aktif</span>
        </div>
    </div>

</div>

<div class="card">

    <div class="card-header card-header-row">
        <div>
            <h2>Daftar Armada</h2>
            <p>Daftar armada bus Trans Koetaradja</p>
        </div>

        <a href="/buses/create" class="btn-add">
            <i class="bi bi-plus-lg"></i>
            Tambah Bus
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="/buses" class="table-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="cari" value="{{ request('cari') }}"
                       placeholder="Cari plat, lambung, atau merk...">
            </div>

            <select name="status" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="service" {{ request('status') == 'service' ? 'selected' : '' }}>Service</option>
                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="btn-primary toolbar-submit">Cari</button>

            @if(request()->hasAny(['cari', 'status']))
                <a href="/buses" class="toolbar-reset">Reset</a>
            @endif

            <span class="toolbar-count">{{ $buses->count() }} bus</span>
        </form>

        <div class="table-wrapper">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Armada</th>
                        <th class="col-kode">Kode Bus</th>
                        <th class="col-komponen">Komponen</th>
                        <th>Status</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($buses as $index => $bus)
                        <tr class="row-in" style="--i: {{ $index }}">

                            <td class="col-no" data-label="No">{{ $index + 1 }}</td>

                            <td data-label="Armada">
                                <div class="plate-cell">
                                    <div class="plate-avatar">
                                        <i class="bi bi-bus-front-fill"></i>
                                    </div>

                                    <div class="plate-copy">
                                        <div class="plate-line">
                                            <strong>{{ $bus->nomor_plat }}</strong>
                                        </div>

                                        @if($bus->merk_bus || $bus->tipe_bus)
                                            <small>
                                                @if($bus->merk_bus && $bus->tipe_bus)
                                                    {{ $bus->merk_bus }} &middot; {{ $bus->tipe_bus }}
                                                @elseif($bus->merk_bus)
                                                    {{ $bus->merk_bus }}
                                                @else
                                                    {{ $bus->tipe_bus }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="col-kode" data-label="Kode Bus">
                                @if($bus->nomor_lambung)
                                    <span class="lambung-chip">{{ $bus->nomor_lambung }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td class="col-komponen" data-label="Komponen">
                                @if($bus->jumlah_komponen > 0)
                                    <span class="komponen-chip">
                                        <i class="bi bi-gear-fill"></i>
                                        {{ $bus->jumlah_komponen }}
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Status">
                                <span class="status-pill status-{{ $bus->status_bus }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($bus->status_bus) }}
                                </span>
                            </td>

                            <td class="col-aksi" data-label="Aksi">
                                <div class="aksi-wrap">
                                    <a href="/buses/{{ $bus->id }}/edit" class="btn-icon btn-icon-edit" title="Ubah bus {{ $bus->nomor_plat }}" aria-label="Ubah bus {{ $bus->nomor_plat }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="/buses/{{ $bus->id }}" method="POST"
                                          data-confirm="Hapus bus {{ $bus->nomor_plat }}? Data odometer bus ini juga akan terhapus.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-delete" title="Hapus bus {{ $bus->nomor_plat }}" aria-label="Hapus bus {{ $bus->nomor_plat }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-bus-front"></i>
                                    <strong>{{ request()->hasAny(['cari', 'status']) ? 'Tidak ada bus yang cocok' : 'Belum ada data bus' }}</strong>
                                    <span>
                                        {{ request()->hasAny(['cari', 'status'])
                                            ? 'Coba kata kunci atau status lain.'
                                            : 'Tambahkan armada pertama untuk mulai memantau komponen.' }}
                                    </span>

                                    @if(request()->hasAny(['cari', 'status']))
                                        <a href="/buses" class="btn-secondary">Reset filter</a>
                                    @else
                                        <a href="/buses/create" class="btn-add">Tambah Bus</a>
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
