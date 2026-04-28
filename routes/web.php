<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;
use App\Models\Dataset;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $datasets = Dataset::where('user_id', auth()->id())->with('rows')->latest()->get();
        return view('dashboard', ['datasets' => $datasets]);
    })->name('dashboard');

    Route::post('/import', [DataController::class, 'importCsv']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
