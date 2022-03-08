<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login']);
Route::get('brokers/{user}', [UserController::class, 'display']);

Route::group(['middleware' => ['api.auth']], function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::post('users/store', [UserController::class, 'store'])->middleware('permission:Usuários');
    Route::post('users/edit/{user}', [UserController::class, 'update'])->middleware('permission:Usuários');
    Route::post('users/self', [UserController::class, 'self']);
    Route::delete('users/destroy/{user}', [UserController::class, 'destroy'])->middleware('permission:Usuários');
});
