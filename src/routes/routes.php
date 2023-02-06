<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\Admin\UserController;

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'destroy'])
->name('logout');
Route::get('/forgot',[AuthController::class, 'forgot'])->name('password.forgot');
Route::post('/forgot',[AuthController::class,'generatePasswordLink'])->name('forgotPassword');
Route::get('/reset_password/{token}',[AuthController::class,'resetPassword'])->name('resetPassword');
Route::post('/change_password',[AuthController::class,'changePassword'])->name('changePassword');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/verify_email/{token}',[RegisterController::class, 'emailVerify'])->name('authenticated.activate');
Route::POST('/active_user',[RegisterController::class,'active'])->name('user.active');

Route::get('/message',[MessageController::class,'index'])->name('user.message');

Route::get('my-captcha', [CaptchaController::class,'myCaptcha'])->name('myCaptcha');
Route::post('my-captcha', [CaptchaController::class,'myCaptchaPost'])->name('myCaptcha.post');
Route::get('refresh_captcha', [CaptchaController::class,'refreshCaptcha'])->name('refresh_captcha');

Route::controller(GoogleController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});


Route::controller(FacebookController::class)->group(function(){
    Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallback');
});

Route::middleware([
    'auth',
    'verified',
    'permission'
])->group(function () {

    Route::get('/home', function () {
        return view('dashboard');
    })->name('home');

    Route::resources([
        'users' => UserController::class
    ]);

    Route::resources([
        'permissions' => PermissionsController::class
    ]);

    Route::resources([
        'roles' => RolesController::class
    ]);

});
