<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
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
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
Route::get('/jobs/{job_id}', [JobController::class, 'jobDetail'])->name('job.detail');


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
        Route::put('profile-update', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::put('update-passwrod', [AccountController::class, 'updatePassword'])->name('account.password.update');
        Route::post('update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.profile-pic.update');
        Route::get('logout', [AccountController::class, 'logout'])->name('account.logout');

        Route::get('create-job', [AccountController::class, 'createJob'])->name('account.job.create');
        Route::post('create-job', [AccountController::class, 'saveJob'])->name('account.job.create');
        Route::get('my-jobs', [AccountController::class, 'myJobs'])->name('account.job.my-jobs');
        Route::get('edit-job/{job_id}', [AccountController::class, 'editJob'])->name('account.job.edit');
        Route::put('update-job/{job_id}', [AccountController::class, 'updateJob'])->name('account.job.update');
        Route::post('delete-job', [AccountController::class, 'deleteJob'])->name('account.job.delete');
        Route::post('apply-on-job', [JobController::class, 'applyOnJob'])->name('account.job.apply');
        Route::get('my-applications', [AccountController::class, 'myJobApplications'])->name('account.job.my-applications');
       
    });
});