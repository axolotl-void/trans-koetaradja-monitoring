@extends('layouts.app')

@section('title', 'Data Komponen')
@section('page-title', 'Data Komponen')
@section('page-group', 'Komponen')

@section('content')

{{-- ringkasan stok --}}
<div class="fleet-summary">

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-total">
            <i class="bi bi-boxes"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['total'] }}</span>
            <span class="fleet-label">Total komponen</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-aktif">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['tersedia'] }}</span>
            <span class="fleet-label">Stok tersedia</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-service">
            <i class="bi bi-gear-wide-connected"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['terpasang'] }}</span>
            <span class="fleet-label">Terpasang</span>
        </div>
    </div>

    <div class="fleet-card">
        <div class="fleet-icon fleet-icon-off">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="fleet-copy">
            <span class="fleet-value">{{ $ringkasan['rusak'] }}</span>
            <span class="fleet-label">Rusak</span>
        </div>
    </div>

</div>

<div class="card">

    <div class="card-header card-header-row">
        <div>
            <h2>Daftar Komponen</h2>
            <p>Komponen bus dan inventaris monitoring</p>
        </div>

        <a href="/inventory/create" class="btn-add">
            <i class="bi bi-plus-lg"></i>
            Input Komponen
        </a>
    </div>

    <div class="card-body">

        <form method="GET" action="/inventory" class="table-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" name="cari" value="{{ request('cari') }}"
                       placeholder="Cari kode, nama, atau merek...">
            </div>

            <select name="kategori" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                        {{ ucfirst($kategori) }}
                    </option>
                @endforeach
            </select>

            <select name="status_barang" class="toolbar-select" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="tersedia" {{ request('status_barang') == 'tersedia' ? 'selected' : '' }}>Stok</option>
                <option value="terpasang" {{ request('status_barang') == 'terpasang' ? 'selected' : '' }}>Terpasang</option>
                <option value="rusak" {{ request('status_barang') == 'rusak' ? 'selected' : '' }}>Rusak</option>
            </select>

            <button type="submit" class="btn-secondary toolbar-submit">Cari</button>

            @if(request()->hasAny(['cari', 'kategori', 'status_barang']))
                <a href="/inventory" class="toolbar-reset">Reset</a>
            @endif

            <a href="/export/monitoring" class="btn-secondary toolbar-export">
                <i class="bi bi-download"></i>
                Export
            </a>

            <span class="toolbar-count">{{ $components->count() }} komponen</span>
        </form>

        <div class="table-wrapper">
            <table class="bus-table">
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Komponen</th>
                        <th>Kategori</th>
                        <th>Merek / Seri</th>
                        <th class="num">Harga Beli</th>
                        <th>Kondisi</th>
                        <th>Dipasang</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($components as $index => $item)
                        <tr class="row-in" style="--i: {{ $index }}">

                            <td class="col-no" data-label="No">{{ $index + 1 }}</td>

                            <td data-label="Komponen">
                                <div class="plate-cell">
                                    <div class="plate-avatar">
                                        <i class="bi bi-gear-fill"></i>
                                    </div>

                                    <div class="plate-copy">
                                        <div class="plate-line">
                                            <strong>{{ $item->nama_alat }}</strong>
                                        </div>

                                        <small>{{ $item->kode_alat }}</small>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Kategori">
                                @if($item->kategori)
                                    <span class="kategori-chip">{{ ucfirst($item->kategori) }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Merek">
                                @if($item->merk || $item->seri)
                                    <div class="merk-cell">
                                        <strong>{{ $item->merk ?? '—' }}</strong>
                                        @if($item->seri)
                                            <small>{{ $item->seri }}</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Harga" class="num">
                                @if($item->harga_beli)
                                    <span class="harga-cell">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Kondisi">
                                @if($item->status_barang === 'tersedia')
                                    <span class="status-pill status-tersedia">
                                        <span class="status-dot"></span>
                                        Stok
                                    </span>
                                @elseif($item->status_barang === 'rusak')
                                    <span class="status-pill status-rusak">
                                        <span class="status-dot"></span>
                                        Rusak
                                    </span>
                                @elseif($item->status_barang === 'terpasang')
                                    <span class="status-pill status-terpasang">
                                        <span class="status-dot"></span>
                                        Dipakai
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td data-label="Dipasang">
                                @if($item->dipasang_di)
                                    <span class="bus-chip">
                                        <i class="bi bi-bus-front-fill"></i>
                                        {{ $item->dipasang_di }}
                                    </span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>

                            <td class="col-aksi" data-label="Aksi">
                                <div class="aksi-wrap">
                                    <a href="/inventory/{{ $item->id }}" class="btn-icon btn-icon-edit" title="Detail {{ $item->nama_alat }}" aria-label="Detail {{ $item->nama_alat }}">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="/inventory/{{ $item->id }}/edit" class="btn-icon btn-icon-edit" title="Ubah {{ $item->nama_alat }}" aria-label="Ubah {{ $item->nama_alat }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="/inventory/{{ $item->id }}" method="POST"
                                          data-confirm="Hapus komponen {{ $item->nama_alat }} ({{ $item->kode_alat }})?{{ $item->dipasang_di ? ' Monitoring di ' . $item->dipasang_di . ' ikut dilepas.' : '' }} Tindakan ini tidak bisa dibatalkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-delete" title="Hapus {{ $item->nama_alat }}" aria-label="Hapus {{ $item->nama_alat }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-box-seam"></i>
                                    <strong>{{ request()->hasAny(['cari', 'kategori', 'status_barang']) ? 'Tidak ada komponen yang cocok' : 'Belum ada komponen' }}</strong>
                                    <span>
                                        {{ request()->hasAny(['cari', 'kategori', 'status_barang'])
                                            ? 'Coba kata kunci, kategori, atau status lain.'
                                            : 'Tambahkan komponen pertama untuk mulai memantau masa pakainya.' }}
                                    </span>

                                    @if(request()->hasAny(['cari', 'kategori', 'status_barang']))
                                        <a href="/inventory" class="btn-secondary">Reset filter</a>
                                    @else
                                        <a href="/inventory/create" class="btn-add">Input Komponen</a>
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
