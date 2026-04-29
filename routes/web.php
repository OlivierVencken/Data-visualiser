<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Models\Dataset;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/home');
    }
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $datasets = Dataset::where('user_id', auth()->id())->with('rows')->latest()->get();
        return view('home', ['datasets' => $datasets]);
    })->name('home');

    Route::post('/import', [DataController::class, 'importCsv']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
