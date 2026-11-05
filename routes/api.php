<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Identity\RegisterController;
use App\Http\Controllers\Administration\UserController;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [RegisterController::class, 'register'])->name('register');
//Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('email-verification');
//Route::post('/email/resend/notification', [EmailVerificationController::class, 'resend'])->name('resend-verification-email');
//Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('reset-password');
Route::get('login', function () {
    return response()->json(['message' => 'You must log in.']);
})->name('login');
//Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

Route::prefix('admin')->group(function (){
    Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
});