<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        
        $user = User::create($incomingFields);
        auth()->login($user);

        return redirect('/home')->with('success', 'Registered and logged in successfully!');
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($incomingFields)) {
            $request->session()->regenerate();
            return redirect('/home')->with('success', 'Logged in successfully!');
        }

        return redirect('/')->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
}
