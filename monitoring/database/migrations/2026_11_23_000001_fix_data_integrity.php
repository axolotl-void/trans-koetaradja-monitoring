<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan integritas data:
 * 1. kode_alat duplikat diberi akhiran, referensi monitoring ikut dipindah
 * 2. bus yang dipakai monitoring/odometer tapi tidak ada di tabel buses didaftarkan ulang
 * 3. kode_alat dibuat unik supaya join monitoring -> inventaris tidak menggandakan baris
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---- 1. duplikat kode_alat ----
        $duplikat = DB::table('inventories')
            ->select('kode_alat', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('kode_alat')
            ->groupBy('kode_alat')
            ->having('jumlah', '>', 1)
            ->pluck('kode_alat');

        foreach ($duplikat as $kode) {
            $baris = DB::table('inventories')
                ->where('kode_alat', $kode)
                ->orderBy('id')
                ->get();

            // baris pertama dipertahankan, sisanya dapat kode baru
            $pertama = $baris->shift();

            foreach ($baris as $i => $lama) {
                $kodeBaru = $kode . '-DUP' . ($i + 2);

                DB::table('inventories')->where('id', $lama->id)
                    ->update(['kode_alat' => $kodeBaru, 'updated_at' => now()]);

                // referensi di monitoring ikut pindah hanya kalau menunjuk baris ini
                DB::table('monitoring_assets')
                    ->where('inventory_kode_alat_ref', $kode)
                    ->where('bus_nomor_plat_ref', $lama->bus_nomor_plat_ref ?? '')
                    ->update(['inventory_kode_alat_ref' => $kodeBaru]);
            }
        }

        // ---- 2. bus yatim ----
        $platDipakai = collect()
            ->merge(DB::table('monitoring_assets')->pluck('bus_nomor_plat_ref'))
            ->merge(DB::table('odometer_logs')->pluck('bus_nomor_plat_ref'))
            ->merge(DB::table('maintenance_logs')->pluck('bus_plat'))
            ->filter()
            ->unique();

        $platTerdaftar = DB::table('buses')->pluck('nomor_plat');

        foreach ($platDipakai->diff($platTerdaftar) as $plat) {
            DB::table('buses')->insert([
                'nomor_plat' => $plat,
                'nomor_lambung' => null,
                'merk_bus' => null,
                'tipe_bus' => null,
                'tahun_pengadaan' => null,
                'status_bus' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---- 3. unique index ----
        if (!$this->adaIndexUnik('inventories', 'inventories_kode_alat_unique')) {
            Schema::table('inventories', function ($table) {
                $table->unique('kode_alat');
            });
        }
    }

    public function down(): void
    {
        Schema::table('inventories', function ($table) {
            $table->dropUnique(['kode_alat']);
        });
    }

    private function adaIndexUnik(string $tabel, string $nama): bool
    {
        return collect(Schema::getIndexes($tabel))
            ->contains(fn ($index) => $index['name'] === $nama);
    }
};
