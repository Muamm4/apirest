<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("register", [ApiController::class, 'register']);
Route::post("login", [ApiController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('tasks', TaskController::class);
    Route::get('profile', [ApiController::class, 'profile']);
    Route::get('refresh-token', [ApiController::class, 'refreshToken']);
    Route::get('logout', [ApiController::class, 'logout']);
});
