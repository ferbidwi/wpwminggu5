<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('login');
    }

    // Menampilkan halaman register
    public function showRegisterForm()
    {
        return view('register');
    }

    // Proses registrasi user baru (via web)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('admin');
    }

    // ======================================================
    // BAGIAN LOGIN (WEB + API)
    // ======================================================

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Kalau request dari API, balas JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Kalau request dari web, balas redirect dengan error
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        // ============================================
        // Kalau request dari API → kasih token
        // ============================================
        if ($request->expectsJson() || $request->is('api/*')) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        }

        // ============================================
        // Kalau request dari web → login pakai session
        // ============================================
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    // ======================================================
    // BAGIAN LOGOUT (WEB + API)
    // ======================================================

    public function logout(Request $request)
    {
        // Kalau request dari API → hapus token
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            return response()->json(['message' => 'Logged out']);
        }

        // Kalau request dari web → hapus session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}