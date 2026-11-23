<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('inventories');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status_barang')) {
            $query->where('status_barang', $request->status_barang);
        }

        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $query->where(function ($q) use ($cari) {
                $q->where('kode_alat', 'like', "%{$cari}%")
                    ->orWhere('nama_alat', 'like', "%{$cari}%")
                    ->orWhere('merk', 'like', "%{$cari}%");
            });
        }

        $components = $query->orderBy('id', 'desc')->get();

        // bus tempat komponen terpasang (dari monitoring_assets = sumber kebenaran)
        $dipasangDi = DB::table('monitoring_assets')
            ->select('inventory_kode_alat_ref', 'bus_nomor_plat_ref')
            ->get()
            ->pluck('bus_nomor_plat_ref', 'inventory_kode_alat_ref');

        $components->each(function ($item) use ($dipasangDi) {
            $item->dipasang_di = $dipasangDi[$item->kode_alat] ?? null;
        });

        $kategoriList = DB::table('inventories')
            ->whereNotNull('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // ringkasan stok (selalu total penuh, tidak ikut filter)
        $ringkasan = [
            'total' => DB::table('inventories')->count(),
            'tersedia' => DB::table('inventories')->where('status_barang', 'tersedia')->count(),
            'terpasang' => DB::table('inventories')->where('status_barang', 'terpasang')->count(),
            'rusak' => DB::table('inventories')->where('status_barang', 'rusak')->count(),
        ];

        return view('inventory.index', compact('components', 'kategoriList', 'ringkasan'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_alat' => 'required|string|max:50|unique:inventories,kode_alat',
            'nama_alat' => 'required|string|max:100',
            'kategori' => 'nullable|string|max:50',
            'merk' => 'nullable|string|max:50',
            'seri' => 'nullable|string|max:50',
            'supplier' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'status_barang' => 'required|in:tersedia,terpasang,rusak',
            'kondisi_awal' => 'nullable|string|max:500',
        ]);

        DB::table('inventories')->insert([
            'kode_alat' => strtoupper(trim($request->kode_alat)),
            'nama_alat' => $request->nama_alat,
            'kategori' => $request->kategori,
            'merk' => $request->merk,
            'seri' => $request->seri,
            'supplier' => $request->supplier,
            'tanggal_pembelian' => $request->tanggal,
            'harga_beli' => $request->harga_beli,
            'status_barang' => $request->status_barang,
            'kondisi_awal' => $request->kondisi_awal,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/inventory')->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = DB::table('inventories')->where('id', $id)->first();

        if (!$item) {
            return redirect('/inventory')->with('error', 'Komponen tidak ditemukan.');
        }

        return view('inventory.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('inventories')->where('id', $id)->first();

        if (!$item) {
            return redirect('/inventory')->with('error', 'Komponen tidak ditemukan.');
        }

        $request->validate([
            'kode_alat' => 'required|string|max:50|unique:inventories,kode_alat,' . $id,
            'nama_alat' => 'required|string|max:100',
            'kategori' => 'nullable|string|max:50',
            'merk' => 'nullable|string|max:50',
            'seri' => 'nullable|string|max:50',
            'supplier' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'status_barang' => 'required|in:tersedia,terpasang,rusak',
            'kondisi_awal' => 'nullable|string|max:500',
        ]);

        $kodeBaru = strtoupper(trim($request->kode_alat));

        DB::transaction(function () use ($request, $id, $item, $kodeBaru) {
            DB::table('inventories')->where('id', $id)->update([
                'kode_alat' => $kodeBaru,
                'nama_alat' => $request->nama_alat,
                'kategori' => $request->kategori,
                'merk' => $request->merk,
                'seri' => $request->seri,
                'supplier' => $request->supplier,
                'tanggal_pembelian' => $request->tanggal,
                'harga_beli' => $request->harga_beli,
                'status_barang' => $request->status_barang,
                'kondisi_awal' => $request->kondisi_awal,
                'updated_at' => now(),
            ]);

            if ($kodeBaru !== $item->kode_alat) {
                DB::table('monitoring_assets')
                    ->where('inventory_kode_alat_ref', $item->kode_alat)
                    ->update(['inventory_kode_alat_ref' => $kodeBaru]);
            }
        });

        return redirect('/inventory')->with('success', 'Data komponen berhasil diperbarui.');
    }

    public function show($id)
    {
        $item = DB::table('inventories')->where('id', $id)->first();

        if (!$item) {
            return redirect('/inventory')->with('error', 'Komponen tidak ditemukan.');
        }

        $riwayat = DB::table('maintenance_logs')
            ->where(function ($q) use ($item) {
                $q->where('komponen_baru_kode', $item->kode_alat)
                    ->orWhere('komponen_lama_kode', $item->kode_alat);
            })
            ->orderByDesc('tanggal_ganti')
            ->orderByDesc('id')
            ->get();

        // data pemasangan aktif (monitoring_assets = sumber kebenaran)
        $monitoring = DB::table('monitoring_assets')
            ->where('inventory_kode_alat_ref', $item->kode_alat)
            ->orderByDesc('id')
            ->first();

        // hitung status pakai service yang sama dengan halaman monitoring
        if ($monitoring) {
            $kmPerBus = \App\Services\ComponentStatusService::latestKmPerBus();
            \App\Services\ComponentStatusService::evaluate(
                $monitoring,
                $kmPerBus[$monitoring->bus_nomor_plat_ref] ?? null
            );

            $monitoring->status_label = \App\Services\ComponentStatusService::label($monitoring->status_hasil);
        }

        return view('inventory.show', compact('item', 'riwayat', 'monitoring'));
    }

    public function destroy($id)
    {
        $item = DB::table('inventories')->where('id', $id)->first();

        if (!$item) {
            return redirect('/inventory')->with('error', 'Komponen tidak ditemukan.');
        }

        // Baris monitoring milik komponen ini ikut dihapus, kalau tidak
        // komponen mustahil dihapus: hapus butuh monitoring kosong, sedangkan
        // monitoring hanya bisa dilepas lewat menu Ganti yang butuh stok.
        $terdampak = DB::table('monitoring_assets')
            ->where('inventory_kode_alat_ref', $item->kode_alat)
            ->pluck('bus_nomor_plat_ref')
            ->all();

        DB::transaction(function () use ($item, $id) {
            DB::table('monitoring_assets')
                ->where('inventory_kode_alat_ref', $item->kode_alat)
                ->delete();

            DB::table('inventories')->where('id', $id)->delete();
        });

        $pesan = 'Komponen ' . $item->kode_alat . ' berhasil dihapus.';

        if (!empty($terdampak)) {
            $pesan .= ' Monitoring di ' . implode(', ', array_unique($terdampak)) . ' ikut dilepas.';
        }

        return redirect('/inventory')->with('success', $pesan);
    }
}
