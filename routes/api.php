<?php

use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Identity\AuthController;
use App\Http\Controllers\Identity\EmailVerificationController;
use App\Http\Controllers\Identity\RegisterController;
use App\Http\Controllers\Identity\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth:sanctum'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth:sanctum'])
    ->name('verification.send');

Route::post('/forgot-password', [ResetPasswordController::class, 'forgot'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.reset');

// Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

Route::prefix('admin')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
});
