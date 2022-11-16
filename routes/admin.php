<?php
use App\Http\Controllers\Admin\InterestsController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin_restricted')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    #interests
    Route::post('/create_interests', [InterestsController::class, 'create']);
    Route::get('/get_interests', [InterestsController::class, 'findAll']);
    Route::put('/edit_interests/{id}', [InterestsController::class, 'update']);
    Route::delete('/delete_interests/{id}', [InterestsController::class, 'delete']);

    Route::post('/create_qualifications');

    Route::post('/create_services');

    Route::post('/create_specialization');
});
