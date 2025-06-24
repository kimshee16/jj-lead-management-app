<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Try users table first
        $user = DB::table('users')->where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            $request->session()->put('user', [
                'id' => $user->id,
                'name' => $user->client_name,
                'email' => $user->email,
                'type' => 'user',
            ]);
            $request->session()->regenerate();
            return redirect()->route('lead-clients.index');
        }

        // Try admins table if not found in users
        $admin = DB::table('admins')->where('email', $credentials['email'])->first();
        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            $request->session()->put('user', [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'type' => 'admin',
            ]);
            $request->session()->regenerate();
            return redirect()->route('lead-clients.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
} 