<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



Route::group([
    'as' => 'tenant.',
    'middleware' => [
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
    ]
], function () {

    //user tenant routes

    Route::get('/', function () {
        //return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth'])->name('dashboard');

    Route::middleware('guest')->group(function () {
        Route::get('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
                    ->name('register');
    
        Route::post('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
    
        Route::get('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
                    ->name('login');
    
        Route::post('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    
        Route::get('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
                    ->name('password.request');
    
        Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                    ->name('password.email');
    
        Route::get('reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
                    ->name('password.reset');
    
        Route::post('reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
                    ->name('password.update');
    });
    
    Route::middleware('auth')->group(function () {
        Route::get('verify-email', [App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
                    ->name('verification.notice');
    
        Route::get('verify-email/{id}/{hash}', [App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
                    ->middleware(['signed', 'throttle:6,1'])
                    ->name('verification.verify');
    
        Route::post('email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
                    ->middleware('throttle:6,1')
                    ->name('verification.send');
    
        Route::get('confirm-password', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
                    ->name('password.confirm');
    
        Route::post('confirm-password', [App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);
    
        Route::post('logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
    });

//admin tenant routes

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware(['auth:admin'])->name('admin.dashboard');

    Route::group(['middleware' => ['guest:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function(){


        Route::get('register', [App\Http\Controllers\Adminauth\RegisteredUserController::class, 'create'])
                    ->name('register');
    
        Route::post('register', [App\Http\Controllers\Adminauth\RegisteredUserController::class, 'store']);
    
        Route::get('login', [App\Http\Controllers\Adminauth\AuthenticatedSessionController::class, 'create'])
                    ->name('login');
    
        Route::post('login', [App\Http\Controllers\Adminauth\AuthenticatedSessionController::class, 'store']);
    
        Route::get('forgot-password', [App\Http\Controllers\Adminauth\PasswordResetLinkController::class, 'create'])
                    ->name('password.request');
    
        Route::post('forgot-password', [App\Http\Controllers\Adminauth\PasswordResetLinkController::class, 'store'])
                    ->name('password.email');
    
        Route::get('reset-password/{token}', [App\Http\Controllers\Adminauth\NewPasswordController::class, 'create'])
                    ->name('password.reset');
    
        Route::post('reset-password', [App\Http\Controllers\Adminauth\NewPasswordController::class, 'store'])
                    ->name('password.update');
    });
    
    Route::group(['middleware' => ['auth:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function(){
        Route::get('verify-email', [App\Http\Controllers\Adminauth\EmailVerificationPromptController::class, '__invoke'])
                    ->name('verification.notice');
    
        Route::get('verify-email/{id}/{hash}', [App\Http\Controllers\Adminauth\VerifyEmailController::class, '__invoke'])
                    ->middleware(['signed', 'throttle:6,1'])
                    ->name('verification.verify');
    
        Route::post('email/verification-notification', [App\Http\Controllers\Adminauth\EmailVerificationNotificationController::class, 'store'])
                    ->middleware('throttle:6,1')
                    ->name('verification.send');
    
        Route::get('confirm-password', [App\Http\Controllers\Adminauth\ConfirmablePasswordController::class, 'show'])
                    ->name('password.confirm');
    
        Route::post('confirm-password', [App\Http\Controllers\Adminauth\ConfirmablePasswordController::class, 'store']);
    
        Route::post('logout', [App\Http\Controllers\Adminauth\AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
        
    });

    Route::get('admin/dashboard/impersonate/{token}', function ($token) {
        return UserImpersonation::makeResponse($token);
    })->name('admin.impersonation');

});


