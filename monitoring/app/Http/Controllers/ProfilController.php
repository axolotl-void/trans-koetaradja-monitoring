<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    // akun yang tidak boleh dihapus (akun yang sedang dipakai)
    private function akunSaya(): int
    {
        return (int) (Auth::id() ?? 0);
    }

    public function index()
    {
        $user = Auth::user();

        $statistik = [
            'bus' => DB::table('buses')->count(),
            'bus_aktif' => DB::table('buses')->where('status_bus', 'aktif')->count(),
            'komponen' => DB::table('inventories')->count(),
            'dipantau' => DB::table('monitoring_assets')->count(),
            'maintenance' => DB::table('maintenance_logs')->count(),
            'odometer' => DB::table('odometer_logs')->count(),
        ];

        $sistem = [
            'aplikasi' => config('app.name'),
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'zona' => config('app.timezone'),
            'locale' => config('app.locale'),
            'lingkungan' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'database' => config('database.default'),
            'waktu' => now(),
        ];

        $akun = DB::table('users')
            ->select('id', 'name', 'email', 'created_at', 'updated_at')
            ->orderBy('id')
            ->get();

        return view('profil.index', compact('user', 'statistik', 'sistem', 'akun'));
    }

    public function storeAkun(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        DB::table('users')->insert([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Akun ' . $validated['email'] . ' berhasil ditambahkan.');
    }

    public function destroyAkun($id)
    {
        $akun = DB::table('users')->where('id', $id)->first();

        if (!$akun) {
            return back()->with('error', 'Akun tidak ditemukan.');
        }

        // jangan sampai tidak ada admin tersisa
        if ((int) $akun->id === $this->akunSaya()) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang Anda pakai.');
        }

        if (DB::table('users')->count() <= 1) {
            return back()->with('error', 'Harus ada minimal satu akun admin.');
        }

        DB::table('users')->where('id', $id)->delete();

        return back()->with('success', 'Akun ' . $akun->email . ' dihapus.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'password_lama.required' => 'Password saat ini wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Password saat ini salah.']);
        }

        DB::table('users')->where('id', $user->id)->update([
            'password' => Hash::make($validated['password']),
            'updated_at' => now(),
        ]);

        // sesi lain ikut keluar setelah password berubah
        Auth::logoutOtherDevices($validated['password']);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
