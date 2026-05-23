<?php

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PosController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/services', [WebsiteController::class, 'services'])->name('services');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::post('/contact', [WebsiteController::class, 'submitContact'])->name('contact.submit');
Route::get('/test', function () {
    return 'Test works';
});

// Custom Reports Route
Route::get('/custom-reports', function () {
    $customReports = new \App\Filament\Pages\CustomReports;
    $data = $customReports->getViewData();

    return view('custom-reports', compact('data'));
})->name('custom-reports');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::middleware('auth')->prefix('app')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
});
