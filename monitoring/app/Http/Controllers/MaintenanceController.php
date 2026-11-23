<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('maintenance_logs');

        if ($request->filled('cari')) {
            $cari = trim($request->cari);

            $query->where(function ($q) use ($cari) {
                $q->where('bus_plat', 'like', "%{$cari}%")
                    ->orWhere('komponen_lama_kode', 'like', "%{$cari}%")
                    ->orWhere('komponen_lama_nama', 'like', "%{$cari}%")
                    ->orWhere('komponen_baru_kode', 'like', "%{$cari}%")
                    ->orWhere('komponen_baru_nama', 'like', "%{$cari}%")
                    ->orWhere('user_petugas', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_ganti',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal_ganti',
                '<=',
                $request->tanggal_akhir
            );
        }

        $maintenanceLogs = $query
            ->orderByDesc('tanggal_ganti')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalMaintenance = DB::table('maintenance_logs')->count();

        // ringkasan (selalu total penuh, tidak ikut filter)
        $ringkasan = [
            'total' => $totalMaintenance,
            'bulan_ini' => DB::table('maintenance_logs')
                ->whereMonth('tanggal_ganti', now()->month)
                ->whereYear('tanggal_ganti', now()->year)
                ->count(),
            'bus' => DB::table('maintenance_logs')->distinct()->count('bus_plat'),
            'petugas' => DB::table('maintenance_logs')
                ->whereNotNull('user_petugas')
                ->distinct()
                ->count('user_petugas'),
        ];

        return view('maintenance.index', compact(
            'maintenanceLogs',
            'totalMaintenance',
            'ringkasan'
        ));
    }
}
