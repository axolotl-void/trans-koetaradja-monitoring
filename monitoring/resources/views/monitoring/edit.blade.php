@extends('layouts.app')

@section('title', 'Edit Monitoring')

@section('page-title', 'Edit Monitoring')
@section('page-group', 'Monitoring')

@section('content')

<div class="card form-card">
    <div class="card-header card-header-row">
        <div>
            <h2>Edit Monitoring Komponen</h2>
            <p>Ubah batas monitoring masa pakai komponen bus</p>
        </div>
        <a href="{{ url('/monitoring/' . $item->id . '/ganti') }}" class="btn-secondary"><i class="bi bi-arrow-repeat"></i> Ganti Komponen</a>
    </div>

    <div class="form-body">
        <form data-validate action="/monitoring/{{ $item->id }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-gear"></i> Komponen &amp; Bus</h3>
                </div>

                <div class="form-group">
                    <label for="kode_alat">Kode Alat</label>
                    <div class="input-wrap">
                        <i class="bi bi-upc-scan"></i>
                        <input type="text" id="kode_alat" value="{{ $item->inventory_kode_alat_ref }}" readonly>
                    </div>
                    <span class="field-hint">Kode komponen tidak diubah di sini. Pakai menu Ganti Komponen.</span>
                </div>

                <div class="form-group {{ $errors->has('bus_nomor_plat_ref') ? 'has-error' : '' }}">
                    <label for="bus_nomor_plat_ref">Bus <span class="req">*</span></label>
                    <select name="bus_nomor_plat_ref" id="bus_nomor_plat_ref" required>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->nomor_plat }}" {{ old('bus_nomor_plat_ref', $item->bus_nomor_plat_ref) == $bus->nomor_plat ? 'selected' : '' }}>
                                {{ $bus->nomor_plat }} - {{ $bus->merk_bus ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('bus_nomor_plat_ref')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-calendar-event"></i> Acuan Monitoring</h3>
                </div>

                <div class="form-group" data-field="tanggal_acuan">
                    <label for="tanggal_acuan">Tanggal Acuan</label>
                    <div class="input-wrap">
                        <i class="bi bi-calendar3"></i>
                        <input type="date" name="tanggal_acuan" id="tanggal_acuan" value="{{ old('tanggal_acuan', $item->tanggal_acuan) }}">
                    </div>
                    @error('tanggal_acuan')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" data-field="km_acuan">
                    <label for="km_acuan">KM Acuan</label>
                    <div class="input-suffix">
                        <input type="number" name="km_acuan" id="km_acuan" min="0" value="{{ old('km_acuan', $item->km_acuan) }}" placeholder="0">
                        <span>KM</span>
                    </div>
                    @error('km_acuan')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Otomatis terisi dari odometer terakhir bila dikosongkan.</span>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-sliders2"></i> Batas &amp; Peringatan</h3>
                </div>

                <div class="form-group {{ $errors->has('jenis_monitoring') ? 'has-error' : '' }}">
                    <label for="jenis_monitoring">Jenis Monitoring <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-activity"></i>
                        <select name="jenis_monitoring" id="jenis_monitoring" required>
                            <option value="waktu" {{ old('jenis_monitoring', $item->jenis_monitoring) == 'waktu' ? 'selected' : '' }}>Waktu</option>
                            <option value="km" {{ old('jenis_monitoring', $item->jenis_monitoring) == 'km' ? 'selected' : '' }}>KM</option>
                            <option value="hybrid" {{ old('jenis_monitoring', $item->jenis_monitoring) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    @error('jenis_monitoring')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Menentukan field mana yang perlu diisi.</span>
                    @enderror
                </div>

                <div class="form-group" data-field="batas_waktu_bulan">
                    <label for="batas_waktu_bulan">Batas Waktu</label>
                    <div class="input-suffix">
                        <input type="number" name="batas_waktu_bulan" id="batas_waktu_bulan" min="1" value="{{ old('batas_waktu_bulan', $item->batas_waktu_bulan) }}" placeholder="0">
                        <span>bulan</span>
                    </div>
                    @error('batas_waktu_bulan')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" data-field="batas_km">
                    <label for="batas_km">Batas KM</label>
                    <div class="input-suffix">
                        <input type="number" name="batas_km" id="batas_km" min="1" value="{{ old('batas_km', $item->batas_km) }}" placeholder="0">
                        <span>KM</span>
                    </div>
                    @error('batas_km')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" data-field="warning_hari">
                    <label for="warning_hari">Peringatan Awal Waktu</label>
                    <div class="input-suffix">
                        <input type="number" name="warning_hari" id="warning_hari" min="0" value="{{ old('warning_hari', $item->warning_hari) }}" placeholder="0">
                        <span>hari</span>
                    </div>
                    @error('warning_hari')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Muncul peringatan sekian hari sebelum batas.</span>
                    @enderror
                </div>

                <div class="form-group" data-field="warning_km">
                    <label for="warning_km">Peringatan Awal KM</label>
                    <div class="input-suffix">
                        <input type="number" name="warning_km" id="warning_km" min="0" value="{{ old('warning_km', $item->warning_km) }}" placeholder="0">
                        <span>KM</span>
                    </div>
                    @error('warning_km')
                        <span class="field-hint">{{ $message }}</span>
                    @else
                        <span class="field-hint">Muncul peringatan sekian KM sebelum batas.</span>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-journal-text"></i> Catatan</h3>
                </div>

                <div class="form-group full {{ $errors->has('catatan') ? 'has-error' : '' }}">
                    <label for="catatan">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" maxlength="500" placeholder="Opsional">{{ old('catatan', $item->catatan) }}</textarea>
                    @error('catatan')
                        <span class="field-hint">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="bi bi-check2"></i> Update</button>
                <a href="{{ url('/monitoring') }}" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                <span class="spacer"></span>
                <a href="{{ url('/maintenance') }}" class="btn-secondary"><i class="bi bi-calendar2-check"></i> Riwayat Maintenance</a>
                <a href="{{ url('/profil') }}" class="btn-secondary"><i class="bi bi-person"></i> Profil</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tampilkan field sesuai jenis_monitoring: waktu -> tanggal + batas bulan,
    // km -> km acuan + batas km, hybrid -> semua. Pakai atribut hidden bawaan browser.
    (function () {
        var select = document.getElementById('jenis_monitoring');

        if (!select) {
            return;
        }

        var peta = {
            waktu: ['tanggal_acuan', 'batas_waktu_bulan', 'warning_hari'],
            km: ['km_acuan', 'batas_km', 'warning_km'],
            hybrid: ['tanggal_acuan', 'km_acuan', 'batas_waktu_bulan', 'batas_km', 'warning_hari', 'warning_km']
        };

        function atur() {
            var tampil = peta[select.value] || peta.hybrid;

            peta.hybrid.forEach(function (nama) {
                var input = document.querySelector('[name="' + nama + '"]');
                var grup = input ? input.closest('.form-group') : null;

                if (grup) {
                    grup.hidden = tampil.indexOf(nama) === -1;
                }
            });
        }

        select.addEventListener('change', atur);
        atur(); // sesuaikan juga saat halaman pertama dimuat
    })();
</script>
@endpush
