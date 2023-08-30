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
    // return view('welcome');
    return redirect(route('login'));
});

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    });
    Route::resource('tilefiles', App\Http\Controllers\TilefileController::class);
    Route::post('/tiles/{tile}/images', [App\Http\Controllers\TileController::class, 'updateImages'])->name('tiles.update.images');
    Route::post('/tiles/{tile}/destroy-images', [App\Http\Controllers\TileController::class, 'destroyImages'])->name('tiles.destroy.images');

    Route::get('/tilefiles/{tilefile}/make-zip', [App\Http\Controllers\TilefileController::class, 'zip'])->name('tilefiles.zip');

    Route::get('/tilefiles/{tilefile:uid}/upload', [App\Http\Controllers\TilefileController::class, 'upload'])->name('tilefiles.upload');
});
