<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::resource('companies', App\Http\Controllers\CompanyController::class);
    });
    Route::resource('tilefiles', App\Http\Controllers\TilefileController::class);

    Route::get('/tilefiles/{tilefile}/make-zip', [App\Http\Controllers\TilefileController::class, 'download'])->name('tilefiles.download');

    Route::get('/tilefiles/{tilefile:uid}/upload', [App\Http\Controllers\TilefileController::class, 'upload'])->name('tilefiles.upload');
    Route::post('/tilefiles/{tilefile:uid}/upload', [App\Http\Controllers\TilefileController::class, 'uploadStore'])->name('tilefiles.upload.store');
});
