<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required', 
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('/dashboard');
        }
        else {
            return back()->with('error', 'Username atau  Password salah');
        }
    }
}
