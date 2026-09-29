<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Tampilkan Halaman Register
    public function showRegisterForm()
    {
        return view('register');
    }

    // 2. Proses Register (Sesuai Acara 20: Form & Validation)
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // Simpan ke database pakai Eloquent (Sesuai Acara 18)
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // 3. Tampilkan Halaman Login
    public function showLoginForm()
    {
        return view('login');
    }

    // 4. Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/admin'); // Kalau sukses, masuk ke dashboard admin
        }

        // Kalau gagal, balik ke login bawa pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // 5. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}