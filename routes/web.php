<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('cache_config', function () { \Illuminate\Support\Facades\Artisan::call('config:cache'); \Illuminate\Support\Facades\Artisan::call('route:clear'); dd("All cache is cleared"); });

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('/products', \App\Http\Controllers\ProductController::class);
    Route::post('/get_products', [\App\Http\Controllers\ProductController::class, 'getProducts'])->name('products.getProducts');
    Route::get('/export-products', [\App\Http\Controllers\ProductController::class, 'exportExcel'])->name('products.export');
});
