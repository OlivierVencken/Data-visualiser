<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisualizationController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/home');
    }
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $dashboards = \App\Models\Dashboard::withCount('visualizations')->where('user_id', auth()->id())->latest()->get();
        return view('home', ['dashboards' => $dashboards]);
    })->name('home');

    Route::resource('dashboards', DashboardController::class)->only(['create', 'store', 'show', 'destroy']);
    Route::resource('dashboards.visualizations', VisualizationController::class)
        ->scoped()
        ->only(['create', 'store', 'destroy']);

    Route::post('/logout', [UserController::class, 'logout']);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');
    
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});
