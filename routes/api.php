<?php

use App\Http\Controllers\Auth\UserAuthController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register_user', [UserAuthController::class, 'create_user']);
Route::post('/verify_user', [UserAuthController::class, 'verify_user']);
Route::post('/login_user', [UserAuthController::class, 'login_user']);
Route::post('/user_forget_password', [UserAuthController::class, 'user_forget_password']);
Route::post('/user_reset_password', [UserAuthController::class, 'user_reset_password']);
Route::post('/edit_user_credentials', [UserAuthController::class, 'editUserCredentials']);
Route::post('/update_profile_picture', [UserAuthController::class, 'update_profile_image']);
Route::post('/change_user_password', [UserAuthController::class, 'user_change_password']);

Route::fallback(function () {
    return response()->json([
        'code' => 404,
        'message' => 'Route Not Found',
    ], 404);
});
