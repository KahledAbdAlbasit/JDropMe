<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\API\PostController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group([
    'middleware' => 'api',
    //'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verification', [AuthController::class, 'verification']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);


});

Route::middleware(['jwt.verify'])->group(function () {

    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/get-messages', [ChatController::class, 'getMessages']);
    Route::get('/notRead-messages', [ChatController::class, 'notRead']);
    Route::get('/readed-messages', [ChatController::class, 'readed']);

    Route::post('/logout', [AuthController::class, 'logout']);




});
