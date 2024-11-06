<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::name('api.')->group(function () {
    Route::middleware(JwtMiddleware::class)->get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Data User',
            'data' => $request->user(),
            'error' => null,
        ]);
    });

    Route::post('/register', RegisterController::class)->name('register');
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/logout', LogoutController::class)->name('logout');

    Route::middleware(JwtMiddleware::class)->group(function () {
        Route::prefix('users')->group(function () {
            Route::name('users.')->group(function () {
                Route::controller(UserController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{id}', 'show')->name('show');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                });
            });
        });
    });
})
;
