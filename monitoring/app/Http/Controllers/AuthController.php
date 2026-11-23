<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        // angka ringkas untuk panel login; cache 5 menit, tabel ini jarang berubah
        $ringkas = Cache::remember('login-ringkas', 300, function () {
            return [
                'bus' => DB::table('buses')->where('status_bus', 'aktif')->count(),
                'komponen' => DB::table('monitoring_assets')->count(),
                'riwayat' => DB::table('maintenance_logs')->count(),
            ];
        });

        return view('auth.login', compact('ringkas'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
