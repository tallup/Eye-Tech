<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/services', [WebsiteController::class, 'services'])->name('services');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::get('/test', function () { return 'Test works'; });

// Custom Reports Route
Route::get('/custom-reports', function () {
    $customReports = new \App\Filament\Pages\CustomReports();
    $data = $customReports->getViewData();
    return view('custom-reports', compact('data'));
})->name('custom-reports');
