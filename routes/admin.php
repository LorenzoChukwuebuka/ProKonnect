<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;

Route::prefix('admin_restricted')->group(function () {
    Route::post('login',[AdminAuthController::class,'login']);
});
