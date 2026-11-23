@extends('layouts.app')

@section('title', 'Ganti Komponen')
@section('page-title', 'Ganti Komponen')
@section('page-group', 'Monitoring')

@section('content')

<div class="card form-card">
    <div class="card-header">
        <h2>Ganti Komponen</h2>
        <p>Pilih komponen pengganti dari stok yang tersedia</p>
    </div>

    <div class="form-body">
        <form data-validate action="/monitoring/{{ $item->id }}/proses-ganti" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-box-seam"></i> Komponen Pengganti</h3>
                </div>

                <div class="form-group">
                    <label for="komponen_lama">Komponen Lama</label>
                    <div class="input-wrap">
                        <i class="bi bi-box"></i>
                        <input type="text" id="komponen_lama" value="{{ $item->nama_alat ?? $item->inventory_kode_alat_ref }}" readonly>
                    </div>
                    <span class="field-hint">Komponen lama otomatis kembali ke stok berstatus tersedia.</span>
                </div>

                <div class="form-group">
                    <label for="bus">Bus</label>
                    <div class="input-wrap">
                        <i class="bi bi-bus-front"></i>
                        <input type="text" id="bus" value="{{ $item->bus_nomor_plat_ref }}" readonly>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('komponen_baru_kode') ? 'has-error' : '' }}">
                    <label for="komponen_baru_kode">Komponen Pengganti <span class="req">*</span></label>
                    <select name="komponen_baru_kode" id="komponen_baru_kode" required>
                        @forelse($components as $c)
                            <option value="{{ $c->kode_alat }}" {{ old('komponen_baru_kode') == $c->kode_alat ? 'selected' : '' }}>
                                {{ $c->kode_alat }} — {{ $c->nama_alat }}
                            </option>
                        @empty
                            <option value="">Tidak ada stok komponen tersedia</option>
                        @endforelse
                    </select>
                    @error('komponen_baru_kode')
                        <span class="field-hint">{{ $message }}</span>
                    @elseif($components->isEmpty())
                        <span class="field-hint">Tambah stok dulu di <a href="{{ url('/inventory/create') }}">Data Komponen</a>.</span>
                    @else
                        <span class="field-hint">Hanya komponen berstatus tersedia yang muncul.</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="km_sekarang">KM Sekarang</label>
                    <div class="input-suffix">
                        <input type="number" id="km_sekarang" value="{{ $kmSekarang }}" readonly>
                        <span>KM</span>
                    </div>
                    <span class="field-hint">Diambil dari odometer terakhir bus ini, jadi KM acuan baru.</span>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-sliders2"></i> Batas Baru</h3>
                </div>

                <div class="form-group {{ $errors->has('batas_km') ? 'has-error' : '' }}">
                    <label for="batas_km">Batas KM</label>
                    <div class="input-suffix">
                        <input type="number" name="batas_km" id="batas_km" min="1" value="{{ old('batas_km', $item->batas_km) }}" placeholder="0">
                        <span>KM</span>
                    </div>
                    @error('batas_km')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Opsional. Dihitung dari KM sekarang.</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('batas_waktu_bulan') ? 'has-error' : '' }}">
                    <label for="batas_waktu_bulan">Batas Waktu</label>
                    <div class="input-suffix">
                        <input type="number" name="batas_waktu_bulan" id="batas_waktu_bulan" min="1" value="{{ old('batas_waktu_bulan', $item->batas_waktu_bulan) }}" placeholder="0">
                        <span>bulan</span>
                    </div>
                    @error('batas_waktu_bulan')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Opsional. Dihitung dari tanggal penggantian hari ini.</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('warning_km') ? 'has-error' : '' }}">
                    <label for="warning_km">Peringatan Awal KM</label>
                    <div class="input-suffix">
                        <input type="number" name="warning_km" id="warning_km" min="0" value="{{ old('warning_km', $item->warning_km) }}" placeholder="0">
                        <span>KM</span>
                    </div>
                    @error('warning_km')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('warning_hari') ? 'has-error' : '' }}">
                    <label for="warning_hari">Peringatan Awal Waktu</label>
                    <div class="input-suffix">
                        <input type="number" name="warning_hari" id="warning_hari" min="0" value="{{ old('warning_hari', $item->warning_hari) }}" placeholder="0">
                        <span>hari</span>
                    </div>
                    @error('warning_hari')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-journal-text"></i> Catatan</h3>
                </div>

                <div class="form-group full {{ $errors->has('catatan') ? 'has-error' : '' }}">
                    <label for="catatan">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" maxlength="500" placeholder="Alasan penggantian, kondisi komponen lama, dll.">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="bi bi-check2"></i> Simpan Penggantian</button>
                <a href="{{ url('/monitoring') }}" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

@endsection
