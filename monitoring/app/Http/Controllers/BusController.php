<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{
    public function index(Request $request)
    {
        // jumlah komponen yang benar-benar dimonitor per bus
        // (monitoring_assets = sumber kebenaran pemasangan)
        $terpasang = DB::table('monitoring_assets')
            ->select('bus_nomor_plat_ref', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('bus_nomor_plat_ref')
            ->pluck('jumlah', 'bus_nomor_plat_ref');

        $query = DB::table('buses');

        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $query->where(function ($q) use ($cari) {
                $q->where('nomor_plat', 'like', "%{$cari}%")
                    ->orWhere('nomor_lambung', 'like', "%{$cari}%")
                    ->orWhere('merk_bus', 'like', "%{$cari}%")
                    ->orWhere('tipe_bus', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_bus', $request->status);
        }

        $buses = $query->orderBy('nomor_plat')->get()
            ->each(function ($bus) use ($terpasang) {
                $bus->jumlah_komponen = (int) ($terpasang[$bus->nomor_plat] ?? 0);
            });

        // ringkasan armada (selalu total penuh, tidak ikut filter)
        $ringkasan = [
            'total' => DB::table('buses')->count(),
            'aktif' => DB::table('buses')->where('status_bus', 'aktif')->count(),
            'service' => DB::table('buses')->where('status_bus', 'service')->count(),
            'nonaktif' => DB::table('buses')->where('status_bus', 'nonaktif')->count(),
        ];

        return view('buses.index', compact('buses', 'ringkasan'));
    }

    public function create()
    {
        return view('buses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_plat' => 'required|string|max:20|unique:buses,nomor_plat',
            'nomor_lambung' => 'nullable|string|max:30',
            'merk_bus' => 'nullable|string|max:50',
            'tipe_bus' => 'nullable|string|max:50',
            'tahun_pengadaan' => 'nullable|integer|min:1980|max:' . (date('Y') + 1),
            'status_bus' => 'required|in:aktif,nonaktif,service',
        ]);

        DB::table('buses')->insert([
            'nomor_plat' => strtoupper(trim($request->nomor_plat)),
            'nomor_lambung' => $request->nomor_lambung,
            'merk_bus' => $request->merk_bus,
            'tipe_bus' => $request->tipe_bus,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'status_bus' => $request->status_bus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/buses')->with('success', 'Bus berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $bus = DB::table('buses')->where('id', $id)->first();

        if (!$bus) {
            return redirect('/buses')->with('error', 'Bus tidak ditemukan.');
        }

        return view('buses.edit', compact('bus'));
    }

    public function update(Request $request, $id)
    {
        $bus = DB::table('buses')->where('id', $id)->first();

        if (!$bus) {
            return redirect('/buses')->with('error', 'Bus tidak ditemukan.');
        }

        $request->validate([
            'nomor_plat' => 'required|string|max:20|unique:buses,nomor_plat,' . $id,
            'nomor_lambung' => 'nullable|string|max:30',
            'merk_bus' => 'nullable|string|max:50',
            'tipe_bus' => 'nullable|string|max:50',
            'tahun_pengadaan' => 'nullable|integer|min:1980|max:' . (date('Y') + 1),
            'status_bus' => 'required|in:aktif,nonaktif,service',
        ]);

        $platBaru = strtoupper(trim($request->nomor_plat));

        DB::transaction(function () use ($request, $id, $bus, $platBaru) {
            DB::table('buses')->where('id', $id)->update([
                'nomor_plat' => $platBaru,
                'nomor_lambung' => $request->nomor_lambung,
                'merk_bus' => $request->merk_bus,
                'tipe_bus' => $request->tipe_bus,
                'tahun_pengadaan' => $request->tahun_pengadaan,
                'status_bus' => $request->status_bus,
                'updated_at' => now(),
            ]);

            if ($platBaru !== $bus->nomor_plat) {
                foreach (['monitoring_assets', 'odometer_logs', 'inventories'] as $tabel) {
                    DB::table($tabel)->where('bus_nomor_plat_ref', $bus->nomor_plat)
                        ->update(['bus_nomor_plat_ref' => $platBaru]);
                }

                DB::table('maintenance_logs')->where('bus_plat', $bus->nomor_plat)
                    ->update(['bus_plat' => $platBaru]);
            }
        });

        return redirect('/buses')->with('success', 'Data bus berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bus = DB::table('buses')->where('id', $id)->first();

        if (!$bus) {
            return redirect('/buses')->with('error', 'Bus tidak ditemukan.');
        }

        $dipakai = DB::table('monitoring_assets')
            ->where('bus_nomor_plat_ref', $bus->nomor_plat)
            ->exists()
            || DB::table('inventories')
                ->where('bus_nomor_plat_ref', $bus->nomor_plat)
                ->exists();

        if ($dipakai) {
            return redirect('/buses')->with(
                'error',
                'Bus ' . $bus->nomor_plat . ' masih punya komponen terpasang. Lepas atau ganti komponennya dulu.'
            );
        }

        DB::transaction(function () use ($bus) {
            DB::table('odometer_logs')->where('bus_nomor_plat_ref', $bus->nomor_plat)->delete();
            DB::table('buses')->where('id', $bus->id)->delete();
        });

        return redirect('/buses')->with('success', 'Bus berhasil dihapus.');
    }
}
