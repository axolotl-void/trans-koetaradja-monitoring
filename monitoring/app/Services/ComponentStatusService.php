<?php

namespace App\Services;

use Carbon\Carbon;

class ComponentStatusService
{
    public const AMAN = 'aman';
    public const PERINGATAN = 'peringatan';
    public const WAJIB_GANTI = 'wajib_ganti';

    /**
     * Hitung status satu baris monitoring_assets.
     *
     * $item  objek/array hasil join monitoring_assets (+ kolom bus_nomor_plat_ref,
     *        jenis_monitoring, tanggal_acuan, km_acuan, batas_waktu_bulan,
     *        batas_km, warning_hari, warning_km)
     * $km    kilometer terakhir bus (null = belum ada odometer)
     */
    public static function evaluate(object $item, ?int $km): object
    {
        $jenis = $item->jenis_monitoring ?? 'hybrid';

        $kmSekarang = (int) ($km ?? 0);
        $kmExpired = null;
        $sisaKm = null;
        $tanggalExpired = null;
        $sisaHari = null;

        $statusKm = self::AMAN;
        $statusWaktu = self::AMAN;

        // ---- jalur kilometer ----
        $pakaiKm = in_array($jenis, ['km', 'hybrid'], true);

        if ($pakaiKm && !is_null($item->batas_km)) {
            $kmExpired = (int) ($item->km_acuan ?? 0) + (int) $item->batas_km;
            $sisaKm = $kmExpired - $kmSekarang;

            if ($kmSekarang >= $kmExpired) {
                $statusKm = self::WAJIB_GANTI;
            } elseif (!is_null($item->warning_km) && $sisaKm <= (int) $item->warning_km) {
                $statusKm = self::PERINGATAN;
            }
        }

        // ---- jalur waktu ----
        $pakaiWaktu = in_array($jenis, ['waktu', 'hybrid'], true);

        if ($pakaiWaktu && !empty($item->tanggal_acuan) && !empty($item->batas_waktu_bulan)) {
            $tanggalExpired = Carbon::parse($item->tanggal_acuan)
                ->addMonths((int) $item->batas_waktu_bulan);

            $sisaHari = (int) round(now()->diffInDays($tanggalExpired, false));

            if ($sisaHari <= 0) {
                $statusWaktu = self::WAJIB_GANTI;
            } elseif (!is_null($item->warning_hari) && $sisaHari <= (int) $item->warning_hari) {
                $statusWaktu = self::PERINGATAN;
            }
        }

        $item->km_sekarang = $kmSekarang;
        $item->km_expired_hitung = $kmExpired;
        $item->sisa_km = $sisaKm;
        $item->tanggal_expired_hitung = $tanggalExpired;
        $item->sisa_hari = $sisaHari;
        $item->status_hasil = self::worst($statusKm, $statusWaktu);

        // bar sisa masa pakai: persentase terhadap total batas.
        // dipakai jalur yang paling dekat habis kalau monitoring hybrid.
        $persen = null;

        if (!is_null($sisaKm) && !is_null($item->batas_km) && (int) $item->batas_km > 0) {
            $persen = ($sisaKm / (int) $item->batas_km) * 100;
        }

        if (!is_null($sisaHari) && !is_null($item->batas_waktu_bulan) && (int) $item->batas_waktu_bulan > 0) {
            $totalHari = (int) $item->batas_waktu_bulan * 30;
            $persenWaktu = ($sisaHari / $totalHari) * 100;
            $persen = is_null($persen) ? $persenWaktu : min($persen, $persenWaktu);
        }

        $item->sisa_bar_persen = is_null($persen)
            ? 0
            : (int) max(0, min(100, round($persen)));

        return $item;
    }

    /** Status terberat menang: wajib_ganti > peringatan > aman. */
    public static function worst(string ...$statuses): string
    {
        $rank = [self::AMAN => 0, self::PERINGATAN => 1, self::WAJIB_GANTI => 2];
        $hasil = self::AMAN;

        foreach ($statuses as $status) {
            if (($rank[$status] ?? 0) > $rank[$hasil]) {
                $hasil = $status;
            }
        }

        return $hasil;
    }

    public static function label(string $status): string
    {
        return match ($status) {
            self::WAJIB_GANTI => 'Wajib Ganti',
            self::PERINGATAN => 'Peringatan',
            'kosong' => 'Belum ada data',
            default => 'Aman',
        };
    }

    /**
     * Kilometer terakhir tiap bus dalam satu query.
     * @return array<string,int>
     */
    public static function latestKmPerBus(): array
    {
        return \Illuminate\Support\Facades\DB::table('odometer_logs')
            ->select('bus_nomor_plat_ref', \Illuminate\Support\Facades\DB::raw('MAX(kilometer) as km'))
            ->groupBy('bus_nomor_plat_ref')
            ->pluck('km', 'bus_nomor_plat_ref')
            ->map(fn ($km) => (int) $km)
            ->all();
    }
}
