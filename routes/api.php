<?php

use App\Http\Controllers\api\AccessController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CardController;
use App\Http\Controllers\api\SumController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
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
Route::post('auth/register', [UserController::class, 'store']);
Route::get('brokers/{user}', [UserController::class, 'display']);

Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

// Defina uma rota para capturar o link de redefinição e redirecionar para a página de redefinição de senha
Route::get('/password/reset/{token}', function ($token) {
    $frontendUrl = env('APP_ENV') === 'production' 
        ? env('FRONTEND_URL', 'https://centraldecontempladas.com.br/') 
        : env('LOCAL_FRONTEND_URL', 'http://localhost:3000/');

    return redirect()->to("{$frontendUrl}/admin/reset?token={$token}");
})->name('password.reset');

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::group(['middleware' => ['api.auth']], function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::post('users/store', [UserController::class, 'store']);
    Route::post('users/edit/{user}', [UserController::class, 'update']);
    Route::post('users/self', [UserController::class, 'self']);
    Route::delete('users/destroy/{user}', [UserController::class, 'destroy']);

    Route::resource('sums', SumController::class)->except(['create', 'edit']);
    Route::resource('cards', CardController::class)->except(['create', 'edit']);
    Route::resource('accesses', AccessController::class)->except(['create', 'edit']);
    Route::get('accessCount', [AccessController::class, 'access_count']);

    Route::get('/proxy/partners/{slug}', function (Request $request) {
        // Token de serviço
        $serviceToken = env('LANCE_API_TOKEN');
        $partnerBackend = env('LANCE_API_URL');
    
        // URL do backend secundário
        $secondaryApiUrl = $partnerBackend . 'gateway/partners/'.$request->slug;
    
        // Passa os parâmetros da requisição original
        $queryParams = $request->query(); // Captura os parâmetros da query string
        $bodyParams = $request->all(); // Captura os parâmetros do corpo da requisição
    
        // Faz a requisição ao backend secundário com query params e corpo da requisição original
        $response = Http::withToken($serviceToken) // Envia os headers originais
            ->get($secondaryApiUrl, $queryParams); // Envia query params
    
        // Retorna a resposta original ao frontend
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    });
});

Route::group(['middleware' => ['service.token']], function(){
    Route::resource('gateway/sums', SumController::class)->except(['create', 'edit']);
    Route::resource('gateway/accesses', AccessController::class)->except(['create', 'edit']);
    Route::get('gateway/users', [UserController::class, 'index']);
    Route::get('gateway/accessCount', [AccessController::class, 'access_count']);
});