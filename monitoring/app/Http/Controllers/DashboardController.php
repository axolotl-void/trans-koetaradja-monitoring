<?php

namespace App\Http\Controllers;

use App\Services\ComponentStatusService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBus = DB::table('buses')->count();
        $busAktif = DB::table('buses')->where('status_bus', 'aktif')->count();

        $kmPerBus = ComponentStatusService::latestKmPerBus();

        $allMonitoringAssets = DB::table('monitoring_assets')
            ->leftJoin('inventories', 'monitoring_assets.inventory_kode_alat_ref', '=', 'inventories.kode_alat')
            ->select('monitoring_assets.*', 'inventories.nama_alat', 'inventories.kategori')
            ->get()
            ->map(fn ($item) => ComponentStatusService::evaluate(
                $item,
                $kmPerBus[$item->bus_nomor_plat_ref] ?? null
            ));

        $totalMonitoring = $allMonitoringAssets->count();
        $totalAman = $allMonitoringAssets->where('status_hasil', ComponentStatusService::AMAN)->count();
        $totalPeringatan = $allMonitoringAssets->where('status_hasil', ComponentStatusService::PERINGATAN)->count();
        $totalWajibGanti = $allMonitoringAssets->where('status_hasil', ComponentStatusService::WAJIB_GANTI)->count();

        // komponen paling mendesak (wajib ganti dulu, lalu sisa terkecil)
        $perluPerhatian = $allMonitoringAssets
            ->where('status_hasil', '!=', ComponentStatusService::AMAN)
            ->sortBy(fn ($item) => [
                $item->status_hasil === ComponentStatusService::WAJIB_GANTI ? 0 : 1,
                $item->sisa_hari ?? $item->sisa_km ?? PHP_INT_MAX,
            ])
            ->take(5)
            ->values();

        $monitoringAssets = $perluPerhatian;

        // maintenance per bulan (12 bulan terakhir) — data asli
        $maintenanceChart = $this->maintenancePerMonth();

        $totalMaintenance = DB::table('maintenance_logs')->count();

        // maintenance terakhir
        $maintenanceTerakhir = DB::table('maintenance_logs')
            ->orderByDesc('tanggal_ganti')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        // ---- status per bus: komponen apa saja + kondisi terburuknya ----
        $statusArmada = DB::table('buses')
            ->orderBy('nomor_plat')
            ->get()
            ->map(function ($bus) use ($allMonitoringAssets) {
                $komponen = $allMonitoringAssets
                    ->where('bus_nomor_plat_ref', $bus->nomor_plat)
                    ->values();

                // bus tanpa komponen bukan "aman" — belum ada data sama sekali
                $terburuk = $komponen->isEmpty()
                    ? 'kosong'
                    : ComponentStatusService::AMAN;

                foreach ($komponen as $k) {
                    $terburuk = ComponentStatusService::worst($terburuk, $k->status_hasil);
                }

                $bus->jumlah_komponen = $komponen->count();
                $bus->terburuk = $terburuk;
                $bus->komponen = $komponen;

                return $bus;
            });

        $totalKomponen = DB::table('inventories')->count();
        $komponenTersedia = DB::table('inventories')->where('status_barang', 'tersedia')->count();

        // cakupan: berapa bus yang benar-benar punya komponen terpantau
        $busTerpantau = DB::table('monitoring_assets')->distinct()->count('bus_nomor_plat_ref');

        // ---- skor kesehatan armada (0-100) ----
        // null = belum ada data untuk dinilai; jangan pura-pura sempurna
        $skor = $totalMonitoring > 0
            ? (int) round((($totalAman * 100) + ($totalPeringatan * 55)) / $totalMonitoring)
            : null;

        return view('dashboard.index', compact(
            'totalBus',
            'busAktif',
            'totalMonitoring',
            'totalAman',
            'totalPeringatan',
            'totalWajibGanti',
            'monitoringAssets',
            'perluPerhatian',
            'maintenanceChart',
            'totalMaintenance',
            'maintenanceTerakhir',
            'statusArmada',
            'skor',
            'totalKomponen',
            'komponenTersedia',
            'busTerpantau'
        ));
    }

    /**
     * Jumlah penggantian komponen per bulan, 12 bulan terakhir.
     * @return array{labels: array<int,string>, data: array<int,int>}
     */
    private function maintenancePerMonth(): array
    {
        $mulai = now()->startOfMonth()->subMonths(11);

        $rows = DB::table('maintenance_logs')
            ->where('tanggal_ganti', '>=', $mulai)
            ->pluck('tanggal_ganti')
            ->map(fn ($t) => Carbon::parse($t)->format('Y-m'))
            ->countBy();

        $labels = [];
        $data = [];

        for ($i = 0; $i < 12; $i++) {
            $bulan = $mulai->copy()->addMonths($i);
            $labels[] = $bulan->translatedFormat('M');
            $data[] = (int) ($rows[$bulan->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
