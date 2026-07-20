<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('demo_admin_logged_in')) {
            return redirect()->route('admin.undangan.index');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $request->session()->regenerate();
            $request->session()->put('demo_admin_logged_in', true);
            $request->session()->put('demo_admin_name', $user->name);
            $request->session()->put('demo_admin_id', $user->id);

            // Langsung ke daftar undangan (hindari /panel/ bentrok folder fisik)
            return redirect()
                ->route('admin.undangan.index')
                ->with('success', 'Selamat datang kembali!');
        }

        return back()->withInput($request->only('email'))->with('error', 'Email atau password salah. Pastikan seeder admin sudah dijalankan.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['demo_admin_logged_in', 'demo_admin_name', 'demo_admin_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Berhasil logout.');
    }
}
