<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OdometerController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('odometer_logs');

        if ($request->filled('bus')) {
            $query->where('bus_nomor_plat_ref', $request->bus);
        }

        $odometerLogs = $query->orderByDesc('tanggal_update')
            ->orderByDesc('id')
            ->get();

        $buses = DB::table('buses')->orderBy('nomor_plat')->pluck('nomor_plat');

        // kilometer terakhir tiap bus
        $kmTerakhir = DB::table('odometer_logs')
            ->select('bus_nomor_plat_ref', DB::raw('MAX(kilometer) as km'))
            ->groupBy('bus_nomor_plat_ref')
            ->pluck('km', 'bus_nomor_plat_ref');

        // selisih antar catatan: hitung per bus dari urutan tanggal naik
        $perBus = [];

        $urutNaik = $odometerLogs->sortBy([
            ['bus_nomor_plat_ref', 'asc'],
            ['tanggal_update', 'asc'],
            ['id', 'asc'],
        ]);

        foreach ($urutNaik as $log) {
            $plat = $log->bus_nomor_plat_ref;

            $log->selisih = isset($perBus[$plat])
                ? (int) $log->kilometer - $perBus[$plat]
                : null;

            $perBus[$plat] = (int) $log->kilometer;
        }

        foreach ($odometerLogs as $log) {
            $log->terakhir = (int) ($kmTerakhir[$log->bus_nomor_plat_ref] ?? 0) === (int) $log->kilometer;
        }

        // bus aktif yang belum punya catatan odometer sama sekali
        $busTanpaCatatan = $buses->diff($kmTerakhir->keys())->values();

        // ringkasan: total log, bus tercatat, km tertinggi, log bulan ini
        $ringkasan = [
            'total' => DB::table('odometer_logs')->count(),
            'bus' => DB::table('odometer_logs')->distinct()->count('bus_nomor_plat_ref'),
            'tertinggi' => (int) (DB::table('odometer_logs')->max('kilometer') ?? 0),
            'bulan_ini' => DB::table('odometer_logs')
                ->whereMonth('tanggal_update', now()->month)
                ->whereYear('tanggal_update', now()->year)
                ->count(),
        ];

        return view('odometer.index', compact('odometerLogs', 'buses', 'kmTerakhir', 'ringkasan', 'busTanpaCatatan'));
    }

    public function create()
    {
        $buses = DB::table('buses')->orderBy('nomor_plat')->get();

        $kmTerakhir = DB::table('odometer_logs')
            ->select('bus_nomor_plat_ref', DB::raw('MAX(kilometer) as km'))
            ->groupBy('bus_nomor_plat_ref')
            ->pluck('km', 'bus_nomor_plat_ref');

        return view('odometer.create', compact('buses', 'kmTerakhir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bus_nomor_plat_ref' => 'required|string|exists:buses,nomor_plat',
            'tanggal_update' => 'required|date|before_or_equal:today',
            'kilometer' => 'required|integer|min:0',
            'user_nama' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ], [
            'tanggal_update.before_or_equal' => 'Tanggal tidak boleh melewati hari ini.',
        ]);

        $busPlat = trim($request->bus_nomor_plat_ref);
        $kilometerBaru = (int) $request->kilometer;

        $lastOdometer = DB::table('odometer_logs')
            ->where('bus_nomor_plat_ref', $busPlat)
            ->max('kilometer');

        if (!is_null($lastOdometer) && $kilometerBaru < (int) $lastOdometer) {
            return back()->withInput()->with(
                'error',
                'Kilometer tidak boleh lebih kecil dari data terakhir (' . number_format($lastOdometer, 0, ',', '.') . ' KM).'
            );
        }

        DB::table('odometer_logs')->insert([
            'bus_nomor_plat_ref' => $busPlat,
            'tanggal_update' => $request->tanggal_update,
            'kilometer' => $kilometerBaru,
            'user_nama' => $request->user_nama,
            'catatan' => $request->catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/odometer')->with('success', 'Data kilometer berhasil disimpan.');
    }

    public function edit($id)
    {
        $item = DB::table('odometer_logs')->where('id', $id)->first();

        if (!$item) {
            return redirect('/odometer')->with('error', 'Data kilometer tidak ditemukan.');
        }

        $buses = DB::table('buses')->orderBy('nomor_plat')->get();

        // batas bawah = KM terakhir bus tsb, tidak termasuk baris ini (sama dgn aturan update())
        $kmTerakhir = DB::table('odometer_logs')
            ->where('id', '!=', $id)
            ->select('bus_nomor_plat_ref', DB::raw('MAX(kilometer) as km'))
            ->groupBy('bus_nomor_plat_ref')
            ->pluck('km', 'bus_nomor_plat_ref');

        return view('odometer.edit', compact('item', 'buses', 'kmTerakhir'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('odometer_logs')->where('id', $id)->first();

        if (!$item) {
            return redirect('/odometer')->with('error', 'Data kilometer tidak ditemukan.');
        }

        $request->validate([
            'bus_nomor_plat_ref' => 'required|string|exists:buses,nomor_plat',
            'tanggal_update' => 'required|date',
            'kilometer' => 'required|integer|min:0',
            'user_nama' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);

        // batas atas dari catatan bus yang sama, tidak termasuk baris ini
        $maxKm = DB::table('odometer_logs')
            ->where('bus_nomor_plat_ref', $request->bus_nomor_plat_ref)
            ->where('id', '!=', $id)
            ->max('kilometer');

        if (!is_null($maxKm) && (int) $request->kilometer < (int) $maxKm) {
            return back()->withInput()->with(
                'error',
                'KM tidak boleh lebih kecil dari data sebelumnya (' . number_format($maxKm, 0, ',', '.') . ' KM).'
            );
        }

        DB::table('odometer_logs')->where('id', $id)->update([
            'bus_nomor_plat_ref' => $request->bus_nomor_plat_ref,
            'tanggal_update' => $request->tanggal_update,
            'kilometer' => (int) $request->kilometer,
            'user_nama' => $request->user_nama,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        return redirect('/odometer')->with('success', 'Data kilometer berhasil diperbarui.');
    }
}
