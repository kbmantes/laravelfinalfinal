<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create() { return view('auth.login'); }

    public function store(Request $request)
    {
        $credentials = $request->validate(['username' => ['required', 'string', 'max:50'], 'password' => ['required', 'string', 'max:200']]);
        if (!Auth::attempt($credentials, false)) return back()->withErrors(['username' => 'The username or password is incorrect.'])->onlyInput('username');
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, '.Auth::user()->full_name.'.');
    }

    public function destroy(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
