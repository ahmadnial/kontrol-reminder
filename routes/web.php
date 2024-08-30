<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\getDataController;
use App\Http\Controllers\brodcastController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/non-jkn', [App\Http\Controllers\HomeController::class, 'nonJkn'])->name('non-jkn');
Route::get('getDataPasien', [getDataController::class, 'getDataPasien'])->name('getDataPasien');
Route::get('getDataPasienNonJkn', [getDataController::class, 'getDataPasienNonJkn'])->name('getDataPasienNonJkn');
Route::post('postBrodcastMessage', [brodcastController::class, 'postBrodcastMessage'])->name('postBrodcastMessage');
Route::post('BrodcastMessageNonJkn', [brodcastController::class, 'BrodcastMessageNonJkn'])->name('BrodcastMessageNonJkn');
