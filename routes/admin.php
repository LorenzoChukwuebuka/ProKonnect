<?php
use App\Http\Controllers\Admin\InterestsController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin_restricted')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:admin,admin-api']], function () {

        #interests
        Route::post('/create_interests', [InterestsController::class, 'create']);
        Route::get('/get_interests', [InterestsController::class, 'findAll']);
        Route::put('/edit_interests/{id}', [InterestsController::class, 'update']);
        Route::delete('/delete_interests/{id}', [InterestsController::class, 'delete']);

        Route::post('/create_qualifications', [PlanController::class, 'create_plan']);
        Route::get('/get_all_plans', [PlanController::class, 'get_all_plans']);
        Route::get('/get_plan_by_id/{id}', [PlanController::class, 'get_plan_by_id']);
        Route::put('/edit_plan/{id}', [PlanController::class, 'edit_plan']);
        Route::delete('/delete_plan/{id}', [PlanController::class, 'delete_plan']);

        Route::post('/create_services', [ServicesController::class, 'create_services']);
        Route::get('/get_services', [ServicesController::class, 'get_all_services']);
        Route::put('/edit_services/{id}', [ServicesController::class, 'edit_services']);

        Route::post('/create_specialization');
    });
});
