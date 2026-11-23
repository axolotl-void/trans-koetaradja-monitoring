<?php

namespace App\Http\Controllers;

use App\Services\ComponentStatusService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Export CSV. Tanpa library: fputcsv ke php://output.
     */
    public function csv(string $jenis): StreamedResponse
    {
        [$nama, $header, $rows] = match ($jenis) {
            'maintenance' => $this->maintenance(),
            'monitoring' => $this->monitoring(),
            'odometer' => $this->odometer(),
            default => abort(404),
        };

        $filename = 'trans-koetaradja-' . $nama . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');

            // BOM supaya Excel baca UTF-8 dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, $header, ';');

            foreach ($rows as $row) {
                fputcsv($out, $row, ';');
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function maintenance(): array
    {
        $rows = DB::table('maintenance_logs')
            ->orderByDesc('tanggal_ganti')
            ->get()
            ->map(fn ($r) => [
                Carbon::parse($r->tanggal_ganti)->format('d/m/Y H:i'),
                $r->bus_plat,
                $r->komponen_lama_kode,
                $r->komponen_lama_nama,
                $r->komponen_baru_kode,
                $r->komponen_baru_nama,
                $r->km_saat_ganti,
                $r->user_petugas,
                $r->catatan,
            ]);

        return [
            'maintenance',
            ['Tanggal Ganti', 'Bus', 'Kode Lama', 'Nama Lama', 'Kode Baru', 'Nama Baru', 'KM Saat Ganti', 'Petugas', 'Catatan'],
            $rows,
        ];
    }

    private function monitoring(): array
    {
        $kmPerBus = ComponentStatusService::latestKmPerBus();

        $rows = DB::table('monitoring_assets')
            ->leftJoin('inventories', 'monitoring_assets.inventory_kode_alat_ref', '=', 'inventories.kode_alat')
            ->select('monitoring_assets.*', 'inventories.nama_alat', 'inventories.kategori')
            ->get()
            ->map(function ($item) use ($kmPerBus) {
                ComponentStatusService::evaluate($item, $kmPerBus[$item->bus_nomor_plat_ref] ?? null);

                return [
                    $item->bus_nomor_plat_ref,
                    $item->inventory_kode_alat_ref,
                    $item->nama_alat,
                    $item->kategori,
                    $item->jenis_monitoring,
                    $item->tanggal_acuan,
                    $item->km_acuan,
                    $item->km_expired_hitung,
                    $item->tanggal_expired_hitung?->format('Y-m-d'),
                    $item->km_sekarang,
                    $item->sisa_km,
                    $item->sisa_hari,
                    ComponentStatusService::label($item->status_hasil),
                ];
            });

        return [
            'monitoring',
            ['Bus', 'Kode Komponen', 'Nama Komponen', 'Kategori', 'Jenis', 'Tanggal Acuan', 'KM Acuan', 'Batas KM', 'Batas Tanggal', 'KM Sekarang', 'Sisa KM', 'Sisa Hari', 'Status'],
            $rows,
        ];
    }

    private function odometer(): array
    {
        $rows = DB::table('odometer_logs')
            ->orderByDesc('tanggal_update')
            ->get()
            ->map(fn ($r) => [
                $r->bus_nomor_plat_ref,
                Carbon::parse($r->tanggal_update)->format('d/m/Y'),
                $r->kilometer,
                $r->user_nama,
                $r->catatan,
            ]);

        return [
            'odometer',
            ['Bus', 'Tanggal', 'Kilometer', 'Petugas', 'Catatan'],
            $rows,
        ];
    }
}
