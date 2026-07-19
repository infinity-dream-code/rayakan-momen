<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('demo_admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = config('demo.admin_username', 'admin');
        $password = config('demo.admin_password', 'webuntal123');

        if ($request->username === $username && $request->password === $password) {
            $request->session()->put('demo_admin_logged_in', true);
            $request->session()->put('demo_admin_name', 'Admin web_untal');

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali!');
        }

        return back()->withInput()->with('error', 'Username atau password salah.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['demo_admin_logged_in', 'demo_admin_name']);

        return redirect()->route('admin.login')->with('success', 'Berhasil logout.');
    }
}
