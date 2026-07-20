<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminPinGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected AdminPinGuard $pinGuard)
    {
    }

    public function showLogin(Request $request)
    {
        if ($this->pinGuard->isBlocked($request)) {
            abort(403, 'Akses ditolak.');
        }

        if (session('demo_admin_logged_in')) {
            return redirect()->route('admin.undangan.index');
        }

        if (session('demo_admin_pending_pin')) {
            return redirect()->route('admin.pin');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        if ($this->pinGuard->isBlocked($request)) {
            abort(403, 'Akses ditolak.');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $request->session()->regenerate();
            // Belum full login — wajib PIN dulu
            $request->session()->forget(['demo_admin_logged_in']);
            $request->session()->put('demo_admin_pending_pin', true);
            $request->session()->put('demo_admin_name', $user->name);
            $request->session()->put('demo_admin_id', $user->id);

            return redirect()
                ->route('admin.pin')
                ->with('success', 'Login berhasil. Masukkan PIN 6 digit.');
        }

        return back()->withInput($request->only('email'))->with('error', 'Email atau password salah. Pastikan seeder admin sudah dijalankan.');
    }

    public function showPin(Request $request)
    {
        if ($this->pinGuard->isBlocked($request)) {
            $request->session()->flush();
            abort(403, 'Akses ditolak. IP diblokir karena terlalu banyak percobaan PIN salah.');
        }

        if (session('demo_admin_logged_in')) {
            return redirect()->route('admin.undangan.index');
        }

        if (! session('demo_admin_pending_pin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login dulu.');
        }

        return view('admin.pin', [
            'attemptsLeft' => $this->pinGuard->attemptsLeft($request),
        ]);
    }

    public function verifyPin(Request $request)
    {
        if ($this->pinGuard->isBlocked($request)) {
            $request->session()->flush();
            abort(403, 'Akses ditolak. IP diblokir karena terlalu banyak percobaan PIN salah.');
        }

        if (! session('demo_admin_pending_pin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login dulu.');
        }

        $request->validate([
            'pin' => 'required|digits:6',
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.digits' => 'PIN harus 6 digit angka.',
        ]);

        if (! $this->pinGuard->verify((string) $request->input('pin'), $request)) {
            if ($this->pinGuard->isBlocked($request)) {
                $request->session()->flush();
                abort(403, 'Akses ditolak. IP diblokir karena terlalu banyak percobaan PIN salah.');
            }

            $left = $this->pinGuard->attemptsLeft($request);

            return back()->with('error', 'PIN salah. Sisa percobaan: '.$left);
        }

        $request->session()->forget('demo_admin_pending_pin');
        $request->session()->put('demo_admin_logged_in', true);
        $request->session()->regenerate();

        return redirect()
            ->route('admin.undangan.index')
            ->with('success', 'Selamat datang kembali!');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'demo_admin_logged_in',
            'demo_admin_pending_pin',
            'demo_admin_name',
            'demo_admin_id',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Berhasil logout.');
    }
}
