<?php

namespace App\Http\Controllers;

use App\Services\ComponentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    // =========================
    // INDEX
    // =========================
    public function index(Request $request)
    {
        $query = DB::table('monitoring_assets')
            ->leftJoin('inventories', 'monitoring_assets.inventory_kode_alat_ref', '=', 'inventories.kode_alat')
            ->select('monitoring_assets.*', 'inventories.nama_alat', 'inventories.kategori');

        if ($request->filled('kategori')) {
            $query->where('inventories.kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            // filter status dihitung setelah evaluasi, lihat di bawah
        }

        $monitoringAssets = $query->orderBy('monitoring_assets.id', 'desc')->get();

        $kmPerBus = ComponentStatusService::latestKmPerBus();

        foreach ($monitoringAssets as $item) {
            ComponentStatusService::evaluate($item, $kmPerBus[$item->bus_nomor_plat_ref] ?? null);
        }

        if ($request->filled('status')) {
            $monitoringAssets = $monitoringAssets
                ->where('status_hasil', $request->status)
                ->values();
        }

        $kategoriList = DB::table('inventories')
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // ringkasan status memakai hasil evaluasi, bukan kolom status_monitoring
        // (kolom itu tidak pernah diperbarui, jadi angkanya basi)
        $semua = DB::table('monitoring_assets')
            ->leftJoin('inventories', 'monitoring_assets.inventory_kode_alat_ref', '=', 'inventories.kode_alat')
            ->select('monitoring_assets.*', 'inventories.nama_alat', 'inventories.kategori')
            ->get()
            ->map(fn ($item) => ComponentStatusService::evaluate(
                $item,
                $kmPerBus[$item->bus_nomor_plat_ref] ?? null
            ));

        $ringkasan = [
            'total' => $semua->count(),
            'aman' => $semua->where('status_hasil', ComponentStatusService::AMAN)->count(),
            'peringatan' => $semua->where('status_hasil', ComponentStatusService::PERINGATAN)->count(),
            'wajib_ganti' => $semua->where('status_hasil', ComponentStatusService::WAJIB_GANTI)->count(),
        ];

        return view('monitoring.index', compact('monitoringAssets', 'kategoriList', 'ringkasan'));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        $buses = DB::table('buses')->orderBy('nomor_plat')->get();

        $components = DB::table('inventories')
            ->where('status_barang', 'tersedia')
            ->orderBy('kode_alat')
            ->get();

        return view('monitoring.create', compact('buses', 'components'));
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'inventory_kode_alat_ref' => 'required|string|exists:inventories,kode_alat',
            'bus_nomor_plat_ref' => 'required|string|exists:buses,nomor_plat',
            'jenis_monitoring' => 'required|in:waktu,km,hybrid',
            'tanggal_acuan' => 'nullable|date',
            'km_acuan' => 'nullable|integer|min:0',
            'batas_waktu_bulan' => 'nullable|integer|min:1',
            'batas_km' => 'nullable|integer|min:1',
            'warning_hari' => 'nullable|integer|min:0',
            'warning_km' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        if (DB::table('monitoring_assets')
            ->where('inventory_kode_alat_ref', $request->inventory_kode_alat_ref)
            ->exists()) {
            return back()->withInput()
                ->with('error', 'Komponen ini sudah dipasang dan sedang dimonitor. Ganti komponennya lewat menu Monitoring.');
        }

        DB::transaction(function () use ($request) {
            DB::table('monitoring_assets')->insert([
                'inventory_kode_alat_ref' => $request->inventory_kode_alat_ref,
                'bus_nomor_plat_ref' => $request->bus_nomor_plat_ref,
                'tanggal_acuan' => $request->tanggal_acuan ?? now()->toDateString(),
                'km_acuan' => $request->km_acuan,
                'batas_waktu_bulan' => $request->batas_waktu_bulan,
                'batas_km' => $request->batas_km,
                'jenis_monitoring' => $request->jenis_monitoring,
                'warning_hari' => $request->warning_hari,
                'warning_km' => $request->warning_km,
                'status_monitoring' => 'aman',
                'catatan' => $request->catatan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('inventories')
                ->where('kode_alat', $request->inventory_kode_alat_ref)
                ->update([
                    'status_barang' => 'terpasang',
                    'bus_nomor_plat_ref' => $request->bus_nomor_plat_ref,
                    'updated_at' => now(),
                ]);
        });

        return redirect('/monitoring')->with('success', 'Monitoring komponen berhasil ditambahkan.');
    }

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $item = DB::table('monitoring_assets')->where('id', $id)->first();

        if (!$item) {
            return redirect('/monitoring')->with('error', 'Data monitoring tidak ditemukan.');
        }

        $buses = DB::table('buses')->orderBy('nomor_plat')->get();
        $components = DB::table('inventories')
            ->where('status_barang', 'tersedia')
            ->orderBy('kode_alat')
            ->get();

        return view('monitoring.edit', compact('item', 'buses', 'components'));
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'bus_nomor_plat_ref' => 'required|string|exists:buses,nomor_plat',
            'jenis_monitoring' => 'required|in:waktu,km,hybrid',
            'tanggal_acuan' => 'nullable|date',
            'km_acuan' => 'nullable|integer|min:0',
            'batas_waktu_bulan' => 'nullable|integer|min:1',
            'batas_km' => 'nullable|integer|min:1',
            'warning_hari' => 'nullable|integer|min:0',
            'warning_km' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        $item = DB::table('monitoring_assets')->where('id', $id)->first();

        if (!$item) {
            return redirect('/monitoring')->with('error', 'Data monitoring tidak ditemukan.');
        }

        DB::table('monitoring_assets')->where('id', $id)->update([
            'bus_nomor_plat_ref' => $request->bus_nomor_plat_ref,
            'tanggal_acuan' => $request->tanggal_acuan,
            'km_acuan' => $request->km_acuan,
            'batas_waktu_bulan' => $request->batas_waktu_bulan,
            'batas_km' => $request->batas_km,
            'jenis_monitoring' => $request->jenis_monitoring,
            'warning_hari' => $request->warning_hari,
            'warning_km' => $request->warning_km,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return redirect('/monitoring')->with('success', 'Data monitoring berhasil diperbarui.');
    }

    // =========================
    // GANTI FORM
    // =========================
    public function ganti($id)
    {
        $item = DB::table('monitoring_assets')
            ->leftJoin('inventories', 'monitoring_assets.inventory_kode_alat_ref', '=', 'inventories.kode_alat')
            ->select('monitoring_assets.*', 'inventories.nama_alat')
            ->where('monitoring_assets.id', $id)
            ->first();

        if (!$item) {
            return redirect('/monitoring')->with('error', 'Data monitoring tidak ditemukan.');
        }

        $components = DB::table('inventories')
            ->where('status_barang', 'tersedia')
            ->orderBy('kode_alat')
            ->get();

        $kmSekarang = (int) (DB::table('odometer_logs')
            ->where('bus_nomor_plat_ref', $item->bus_nomor_plat_ref)
            ->max('kilometer') ?? 0);

        return view('monitoring.ganti', compact('item', 'components', 'kmSekarang'));
    }

    // =========================
    // PROSES GANTI
    // =========================
    public function prosesGanti(Request $request, $id)
    {
        $request->validate([
            'komponen_baru_kode' => 'required|string|exists:inventories,kode_alat',
            'batas_km' => 'nullable|integer|min:1',
            'batas_waktu_bulan' => 'nullable|integer|min:1',
            'warning_km' => 'nullable|integer|min:0',
            'warning_hari' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        $item = DB::table('monitoring_assets')->where('id', $id)->first();

        if (!$item) {
            return redirect('/monitoring')->with('error', 'Data monitoring tidak ditemukan.');
        }

        $komponenLama = DB::table('inventories')
            ->where('kode_alat', $item->inventory_kode_alat_ref)
            ->first();

        $komponenBaru = DB::table('inventories')
            ->where('kode_alat', $request->komponen_baru_kode)
            ->first();

        if (!$komponenBaru) {
            return back()->withInput()->with('error', 'Komponen baru tidak ditemukan.');
        }

        $kmSekarang = (int) (DB::table('odometer_logs')
            ->where('bus_nomor_plat_ref', $item->bus_nomor_plat_ref)
            ->max('kilometer') ?? 0);

        try {
            DB::transaction(function () use ($request, $id, $item, $komponenLama, $komponenBaru, $kmSekarang) {
                DB::table('maintenance_logs')->insert([
                    'bus_plat' => $item->bus_nomor_plat_ref,
                    'komponen_lama_kode' => $komponenLama->kode_alat ?? $item->inventory_kode_alat_ref,
                    'komponen_lama_nama' => $komponenLama->nama_alat ?? 'Komponen lama',
                    'komponen_baru_kode' => $komponenBaru->kode_alat,
                    'komponen_baru_nama' => $komponenBaru->nama_alat ?? 'Komponen baru',
                    'catatan' => $request->catatan ?? 'Penggantian komponen melalui halaman monitoring.',
                    'km_saat_ganti' => $kmSekarang,
                    'user_petugas' => auth()->user()?->name ?? 'Admin',
                    'tanggal_ganti' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('monitoring_assets')->where('id', $id)->update([
                    'inventory_kode_alat_ref' => $komponenBaru->kode_alat,
                    'batas_km' => $request->batas_km ?? $item->batas_km,
                    'batas_waktu_bulan' => $request->batas_waktu_bulan ?? $item->batas_waktu_bulan,
                    'warning_km' => $request->warning_km ?? $item->warning_km,
                    'warning_hari' => $request->warning_hari ?? $item->warning_hari,
                    'km_acuan' => $kmSekarang,
                    'tanggal_acuan' => now()->toDateString(),
                    'status_monitoring' => 'aman',
                    'tanggal_expired' => null,
                    'km_expired' => null,
                    'updated_at' => now(),
                ]);

                // komponen lama tidak otomatis dibuang: kembali ke stok,
                // petugas yang memutuskan rusak/baik lewat edit komponen
                DB::table('inventories')
                    ->where('kode_alat', $item->inventory_kode_alat_ref)
                    ->update([
                        'status_barang' => 'tersedia',
                        'bus_nomor_plat_ref' => null,
                        'updated_at' => now(),
                    ]);

                DB::table('inventories')
                    ->where('kode_alat', $komponenBaru->kode_alat)
                    ->update([
                        'status_barang' => 'terpasang',
                        'bus_nomor_plat_ref' => $item->bus_nomor_plat_ref,
                        'updated_at' => now(),
                    ]);
            });

            return redirect('/monitoring')->with('success', 'Komponen berhasil diganti dan riwayat maintenance tersimpan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Penggantian komponen gagal disimpan.');
        }
    }

    // =========================
    // RESET PENGINGAT (setelah komponen dirawat, bukan diganti)
    // =========================
    public function reset($id)
    {
        $item = DB::table('monitoring_assets')->where('id', $id)->first();

        if (!$item) {
            return redirect('/monitoring')->with('error', 'Data monitoring tidak ditemukan.');
        }

        $kmSekarang = (int) (DB::table('odometer_logs')
            ->where('bus_nomor_plat_ref', $item->bus_nomor_plat_ref)
            ->max('kilometer') ?? 0);

        DB::table('monitoring_assets')->where('id', $id)->update([
            'tanggal_acuan' => now()->toDateString(),
            'km_acuan' => $kmSekarang,
            'status_monitoring' => 'aman',
            'last_checked_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/monitoring')->with('success', 'Pengingat komponen berhasil direset dari hari ini.');
    }
}
