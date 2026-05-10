<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($credenciales)){
            $request->session()->regenerate();
            return redirect()->intended(route('admin.users.index'));
        }

        return back()
            ->withErrors(['email' => 'credenciales incorrectas'])
            ->onlyInput('email');
    }
}
