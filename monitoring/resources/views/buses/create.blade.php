@extends('layouts.app')

@section('title', 'Tambah Data Bus')

@section('page-title', 'Tambah Data Bus')
@section('page-group', 'Armada')

@section('content')

<div class="card form-card">
    <div class="card-header">
        <h2>Tambah Data Bus</h2>
        <p>Input armada bus Trans Koetaradja</p>
    </div>

    <div class="form-body">
        <form data-validate action="/buses" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-bus-front"></i> Identitas Armada</h3>
                </div>

                <div class="form-group @error('nomor_plat') has-error @enderror">
                    <label for="nomor_plat">Nomor Plat <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-credit-card-2-front"></i>
                        <input type="text" id="nomor_plat" name="nomor_plat"
                               value="{{ old('nomor_plat') }}"
                               placeholder="BL 1234 AA" required>
                    </div>
                    @error('nomor_plat')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Format: kode wilayah, angka, huruf. Contoh: BL 1234 AA</div>
                    @enderror
                </div>

                <div class="form-group @error('nomor_lambung') has-error @enderror">
                    <label for="nomor_lambung">Nomor Lambung</label>
                    <div class="input-wrap">
                        <i class="bi bi-hash"></i>
                        <input type="text" id="nomor_lambung" name="nomor_lambung"
                               value="{{ old('nomor_lambung') }}"
                               placeholder="TR01">
                    </div>
                    @error('nomor_lambung')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Kode unit armada. Contoh: TR01</div>
                    @enderror
                </div>

                <div class="form-group @error('merk_bus') has-error @enderror">
                    <label for="merk_bus">Merek Bus</label>
                    <div class="input-wrap">
                        <i class="bi bi-building"></i>
                        <input type="text" id="merk_bus" name="merk_bus"
                               value="{{ old('merk_bus') }}"
                               placeholder="Hino, Mercedes-Benz, ...">
                    </div>
                    @error('merk_bus')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group @error('tipe_bus') has-error @enderror">
                    <label for="tipe_bus">Tipe Bus</label>
                    <div class="input-wrap">
                        <i class="bi bi-tag"></i>
                        <input type="text" id="tipe_bus" name="tipe_bus"
                               value="{{ old('tipe_bus') }}"
                               placeholder="Big bus, medium bus, ...">
                    </div>
                    @error('tipe_bus')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-clipboard-data"></i> Data Operasional</h3>
                </div>

                <div class="form-group @error('tahun_pengadaan') has-error @enderror">
                    <label for="tahun_pengadaan">Tahun Pengadaan</label>
                    <div class="input-suffix">
                        <input type="number" id="tahun_pengadaan" name="tahun_pengadaan"
                               value="{{ old('tahun_pengadaan') }}"
                               min="1980" max="{{ date('Y') + 1 }}"
                               placeholder="{{ date('Y') }}">
                        <span>TH</span>
                    </div>
                    @error('tahun_pengadaan')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Rentang {{ 1980 }} &ndash; {{ date('Y') + 1 }}</div>
                    @enderror
                </div>

                <div class="form-group @error('status_bus') has-error @enderror">
                    <label for="status_bus">Status Bus <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-activity"></i>
                        <select id="status_bus" name="status_bus" required>
                            <option value="" disabled {{ old('status_bus') ? '' : 'selected' }}>Pilih status</option>
                            <option value="aktif" {{ old('status_bus') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status_bus') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="service" {{ old('status_bus') == 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>
                    @error('status_bus')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Service = sedang perawatan, tidak beroperasi</div>
                    @enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="/buses" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

@endsection
