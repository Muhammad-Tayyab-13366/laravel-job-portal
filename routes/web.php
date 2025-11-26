<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index'])->name('home');



Route::group(['prefix' => 'account'], function(){
    // Guest routes
    Route::group(['middleware' => 'guest'], function(){
        Route::get('register', [AccountController::class, 'registeration'])->name('account.registeration');
        Route::post('process-register', [AccountController::class, 'processRegisteration'])->name('account.process-registeration');
        Route::get('login', [AccountController::class, 'login'])->name('account.login');
        Route::post('process-login', [AccountController::class, 'processLogin'])->name('account.process-login');
    });
    // Authenticated routes
    Route::group(["middleware" => "auth"], function(){

        Route::get('profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::get('logout', [AccountController::class, 'logout'])->name('account.logout');
    });
});