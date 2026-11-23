@extends('layouts.app')

@section('title', 'Edit Komponen')

@section('page-title', 'Edit Komponen')
@section('page-group', 'Komponen')

@section('content')

@php
    $tanggal = old('tanggal', $item->tanggal_pembelian
        ? \Illuminate\Support\Carbon::parse($item->tanggal_pembelian)->format('Y-m-d')
        : '');
@endphp

<div class="card form-card">
    <div class="card-header">
        <h2>Edit Komponen</h2>
        <p>Perbarui data komponen inventaris bus</p>
    </div>

    <div class="form-body">
        <form data-validate action="/inventory/{{ $item->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-upc-scan"></i> Identitas Komponen</h3>
                </div>

                <div class="form-group @error('kode_alat') has-error @enderror">
                    <label for="kode_alat">Kode Alat <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-upc-scan"></i>
                        <input type="text" id="kode_alat" name="kode_alat"
                               value="{{ old('kode_alat', $item->kode_alat) }}"
                               placeholder="012ADI" required maxlength="50">
                    </div>
                    @error('kode_alat')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Kode unik, contoh: 012ADI</div>
                    @enderror
                </div>

                <div class="form-group @error('nama_alat') has-error @enderror">
                    <label for="nama_alat">Nama Alat <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-box-seam"></i>
                        <input type="text" id="nama_alat" name="nama_alat"
                               value="{{ old('nama_alat', $item->nama_alat) }}"
                               placeholder="Filter oli, aki, kampas rem, ..." required maxlength="100">
                    </div>
                    @error('nama_alat')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('kategori') has-error @enderror">
                    <label for="kategori">Kategori</label>
                    <div class="input-wrap">
                        <i class="bi bi-tags"></i>
                        <input type="text" id="kategori" name="kategori"
                               value="{{ old('kategori', $item->kategori) }}"
                               placeholder="Oli, Aki, Ban, Rem, Filter" maxlength="50">
                    </div>
                    @error('kategori')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('merk') has-error @enderror">
                    <label for="merk">Merek</label>
                    <div class="input-wrap">
                        <i class="bi bi-building"></i>
                        <input type="text" id="merk" name="merk"
                               value="{{ old('merk', $item->merk) }}"
                               placeholder="Shell, Bosch, ..." maxlength="50">
                    </div>
                    @error('merk')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('seri') has-error @enderror">
                    <label for="seri">Seri</label>
                    <div class="input-wrap">
                        <i class="bi bi-hash"></i>
                        <input type="text" id="seri" name="seri"
                               value="{{ old('seri', $item->seri) }}"
                               placeholder="Nomor seri pabrikan" maxlength="50">
                    </div>
                    @error('seri')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-receipt"></i> Pengadaan</h3>
                </div>

                <div class="form-group @error('supplier') has-error @enderror">
                    <label for="supplier">Supplier</label>
                    <div class="input-wrap">
                        <i class="bi bi-truck"></i>
                        <input type="text" id="supplier" name="supplier"
                               value="{{ old('supplier', $item->supplier) }}"
                               placeholder="Nama toko / vendor" maxlength="100">
                    </div>
                    @error('supplier')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('tanggal') has-error @enderror">
                    <label for="tanggal">Tanggal</label>
                    <div class="input-wrap">
                        <i class="bi bi-calendar-event"></i>
                        <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal }}">
                    </div>
                    @error('tanggal')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Tanggal pembelian komponen</div>
                    @enderror
                </div>

                <div class="form-group @error('harga_beli') has-error @enderror">
                    <label for="harga_beli">Harga Beli</label>
                    <div class="input-suffix">
                        <input type="number" id="harga_beli" name="harga_beli"
                               value="{{ old('harga_beli', $item->harga_beli) }}"
                               min="0" step="0.01" placeholder="0">
                        <span>Rp</span>
                    </div>
                    @error('harga_beli')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Harga satuan saat pembelian</div>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-clipboard-check"></i> Status &amp; Kondisi</h3>
                </div>

                <div class="form-group @error('status_barang') has-error @enderror">
                    <label for="status_barang">Status Barang <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-activity"></i>
                        <select id="status_barang" name="status_barang" required>
                            <option value="tersedia" {{ old('status_barang', $item->status_barang) === 'tersedia' ? 'selected' : '' }}>Stok</option>
                            <option value="terpasang" {{ old('status_barang', $item->status_barang) === 'terpasang' ? 'selected' : '' }}>Terpasang</option>
                            <option value="rusak" {{ old('status_barang', $item->status_barang) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>
                    @error('status_barang')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Terpasang = sudah dipasang di unit bus</div>
                    @enderror
                </div>

                <div class="form-group full @error('kondisi_awal') has-error @enderror">
                    <label for="kondisi_awal">Kondisi Awal</label>
                    <textarea id="kondisi_awal" name="kondisi_awal" maxlength="500"
                              placeholder="Contoh: baru, segel utuh, dus sedikit penyok">{{ old('kondisi_awal', $item->kondisi_awal) }}</textarea>
                    @error('kondisi_awal')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Catatan singkat kondisi saat diterima. Maks. 500 karakter.</div>
                    @enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Update</button>
                <a href="/inventory" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>

                <button type="submit" class="form-danger-link spacer">
                    <i class="bi bi-trash"></i> Hapus Komponen
                </button>
            </div>
        </form>

        {{-- form hapus terpisah: di luar form utama agar tidak bersarang --}}
        <form id="deleteForm" action="/inventory/{{ $item->id }}" method="POST"
                      data-confirm="Hapus komponen ini? Tindakan ini tidak bisa dibatalkan.">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@endsection
