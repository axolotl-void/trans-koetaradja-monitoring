<?php

namespace App\Providers;

use App\Services\ComponentStatusService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // angka ringkas untuk panel sidebar; cache 60 detik biar tidak query tiap request
        View::composer('partials.sidebar', function ($view) {
            $stat = Cache::remember('sidebar-stat', 60, function () {
                return [
                    'bus' => DB::table('buses')->where('status_bus', 'aktif')->count(),
                    'komponen' => DB::table('monitoring_assets')->count(),
                ];
            });

            $view->with('sidebarStat', $stat);
        });

        // notifikasi topbar: komponen yang benar-benar perlu ditindak
        View::composer('partials.topbar', function ($view) {
            $data = Cache::remember('topbar-notif', 60, function () {
                $rows = DB::table('monitoring_assets as m')
                    ->leftJoin('inventories as i', 'i.kode_alat', '=', 'm.inventory_kode_alat_ref')
                    ->select('m.*', 'i.nama_alat')
                    ->get();

                // kilometer terakhir tiap bus
                $kmPerBus = DB::table('odometer_logs')
                    ->select('bus_nomor_plat_ref', DB::raw('MAX(kilometer) as km'))
                    ->groupBy('bus_nomor_plat_ref')
                    ->pluck('km', 'bus_nomor_plat_ref');

                $perlu = $rows->map(function ($r) use ($kmPerBus) {
                    $r->evaluasi = ComponentStatusService::evaluate(
                        $r,
                        $kmPerBus[$r->bus_nomor_plat_ref] ?? null
                    );

                    $r->status = $r->evaluasi->status_hasil;

                    return $r;
                })->filter(fn ($r) => in_array($r->status, ['peringatan', 'wajib_ganti'], true));

                $items = $perlu->sortBy(fn ($r) => $r->status === 'wajib_ganti' ? 0 : 1)
                    ->take(5)
                    ->map(function ($r) {
                        $ev = $r->evaluasi;

                        // label sisa: mana yang lebih dekat habis
                        if (!is_null($ev->sisa_km) && ($ev->sisa_km <= 5000 || is_null($ev->sisa_hari))) {
                            $label = number_format((int) $ev->sisa_km, 0, ',', '.') . ' KM';
                        } elseif (!is_null($ev->sisa_hari)) {
                            $label = (int) $ev->sisa_hari . ' hari';
                        } else {
                            $label = 'Ganti';
                        }

                        // array polos: aman disimpan di cache
                        return [
                            'id' => (int) $r->id,
                            'nama_alat' => (string) ($r->nama_alat ?? $r->inventory_kode_alat_ref),
                            'bus' => (string) $r->bus_nomor_plat_ref,
                            'status' => (string) $r->status,
                            'sisa_label' => $label,
                        ];
                    })
                    ->values()
                    ->all();

                return ['total' => $perlu->count(), 'items' => $items];
            });

            $view->with('topbarNotif', $data);
        });
    }
}
