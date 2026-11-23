@extends('layouts.app')

@section('title', 'Input Kilometer')

@section('page-title', 'Input Kilometer')
@section('page-group', 'Kilometer')

@section('content')

<div class="card form-card">
    <div class="card-header">
        <h2>Input Kilometer Bus</h2>
        <p>Input kilometer terbaru armada bus Trans Koetaradja</p>
    </div>

    <div class="form-body">
        <form data-validate action="/odometer" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-bus-front"></i> Bus &amp; Waktu</h3>
                </div>

                <div class="form-group full @error('bus_nomor_plat_ref') has-error @enderror">
                    <label for="bus_nomor_plat_ref">Bus <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-bus-front"></i>
                        <select id="bus_nomor_plat_ref" name="bus_nomor_plat_ref" required>
                            <option value="" disabled {{ old('bus_nomor_plat_ref') ? '' : 'selected' }}>Pilih bus</option>
                            @foreach($buses as $bus)
                                <option value="{{ $bus->nomor_plat }}"
                                        data-km="{{ $kmTerakhir[$bus->nomor_plat] ?? '' }}"
                                        {{ old('bus_nomor_plat_ref') === $bus->nomor_plat ? 'selected' : '' }}>
                                    {{ $bus->nomor_plat }} - {{ $bus->merk_bus ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('bus_nomor_plat_ref')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Pilih armada yang kilometer-nya dicatat</div>
                    @enderror
                </div>

                <div class="form-group @error('tanggal_update') has-error @enderror">
                    <label for="tanggal_update">Tanggal Update <span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-calendar-event"></i>
                        <input type="date" id="tanggal_update" name="tanggal_update"
                               value="{{ old('tanggal_update', date('Y-m-d')) }}" required>
                    </div>
                    @error('tanggal_update')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Tanggal pencatatan odometer</div>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-speedometer2"></i> Kilometer</h3>
                </div>

                <div class="form-group @error('kilometer') has-error @enderror">
                    <label for="kilometer">Kilometer <span class="req">*</span></label>
                    <div class="input-suffix">
                        <input type="number" id="kilometer" name="kilometer"
                               value="{{ old('kilometer') }}"
                               min="0" step="1" inputmode="numeric"
                               placeholder="0" required>
                        <span>KM</span>
                    </div>
                    @error('kilometer')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint" id="kmLastHint">Tidak boleh lebih kecil dari catatan terakhir bus ini</div>
                    @enderror
                </div>

                <div class="form-section">
                    <h3 class="form-section-title"><i class="bi bi-person-badge"></i> Petugas &amp; Catatan</h3>
                </div>

                <div class="form-group @error('user_nama') has-error @enderror">
                    <label for="user_nama">Nama Petugas</label>
                    <div class="input-wrap">
                        <i class="bi bi-person"></i>
                        <input type="text" id="user_nama" name="user_nama"
                               value="{{ old('user_nama') }}"
                               maxlength="100" placeholder="Nama pencatat">
                    </div>
                    @error('user_nama')
                        <div class="field-hint">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group full @error('catatan') has-error @enderror">
                    <label for="catatan">Catatan</label>
                    <textarea id="catatan" name="catatan" maxlength="500"
                              placeholder="Kondisi armada, rute, atau keterangan lain">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <div class="field-hint">{{ $message }}</div>
                    @else
                        <div class="field-hint">Opsional, maksimal 500 karakter</div>
                    @enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
                <a href="/odometer" class="btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var sel = document.getElementById('bus_nomor_plat_ref');
        var km = document.getElementById('kilometer');
        var hint = document.getElementById('kmLastHint');

        if (!sel || !km || !hint) {
            return;
        }

        var defaultHint = hint.textContent;

        function sync() {
            var opt = sel.options[sel.selectedIndex];
            var raw = opt ? opt.getAttribute('data-km') : '';
            var last = raw ? parseInt(raw, 10) : null;

            if (last === null || isNaN(last)) {
                km.min = 0;
                hint.textContent = defaultHint;
                return;
            }

            km.min = last;
            hint.textContent = 'Kilometer terakhir: ' + last.toLocaleString('id-ID') + ' KM (tidak boleh lebih kecil)';
        }

        sel.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
