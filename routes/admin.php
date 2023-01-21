<?php
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\InterestsController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\QualificationsController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\BadWordsController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UniversityController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin_restricted')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:admin,admin-api']], function () {
        Route::post('/change_password', [AdminAuthController::class, 'changePassword']);
        #interests
        Route::post('/create_interests', [InterestsController::class, 'create']);
        Route::get('/get_interests', [InterestsController::class, 'findAll']);
        Route::put('/edit_interests/{id}', [InterestsController::class, 'update']);
        Route::delete('/delete_interests/{id}', [InterestsController::class, 'delete']);

        Route::post('/create_plan', [PlanController::class, 'create_plan']);
        Route::get('/get_all_plans', [PlanController::class, 'get_all_plans']);
        Route::get('/get_plan_by_id/{id}', [PlanController::class, 'get_plan_by_id']);
        Route::put('/edit_plan/{id}', [PlanController::class, 'edit_plan']);
        Route::delete('/delete_plan/{id}', [PlanController::class, 'delete_plan']);

        Route::post('/create_services', [ServicesController::class, 'create_services']);
        Route::get('/get_services', [ServicesController::class, 'get_all_services']);
        Route::put('/edit_services/{id}', [ServicesController::class, 'update']);
        Route::delete('delete_services/{id}', [ServicesController::class, 'delete']);

        Route::post('/create_specialization', [SpecializationController::class, 'create']);
        Route::get('/get_specialization', [SpecializationController::class, 'findAll']);
        Route::put('/edit_specialization/{id}', [SpecializationController::class, 'update']);
        Route::delete('/delete_specialization/{id}', [SpecializationController::class, 'delete']);

        Route::post('/create_qualification', [QualificationsController::class, 'create']);
        Route::get('/get_qualifications', [QualificationsController::class, 'findAll']);
        Route::put('/edit_qualification/{id}', [QualificationsController::class, 'update']);
        Route::delete('/delete_qualification/{id}', [QualificationsController::class, 'delete']);

        #bad words

        Route::post('/create_bad_word', [BadWordsController::class, 'create_bad_words']);
        Route::get('/get_all_words', [BadWordsController::class, 'get_all_bad_words']);
        Route::delete('/delete_word/{id}', [BadWordsController::class, 'delete_bad_word']);

        Route::get('/get_university', [UniversityController::class, 'get_all_university']);

        #service category
        Route::post('/create_service_category', [ServiceCategoryController::class, 'create_service_category']);
        Route::get('/get_service_categories', [ServiceCategoryController::class, 'get_service_category']);
        Route::put('/edit_service_category/{id}', [ServiceCategoryController::class, 'update_service_category']);
        Route::delete('/delete_service_category/{id}', [ServiceCategoryController::class, 'delete_service_category']);

        #review

        Route::get('/get_all_reviews', [RatingController::class, 'get_all_reviews']);

        #plan option

        Route::post('/create_plan_option', [PlanController::class, 'create_plan_options']);
        Route::get('/get_plan_options', [PlanController::class, 'get_all_plan_options']);
        Route::put('/edit_plan_options/{id}', [PlanController::class, 'edit_plan_options']);
        Route::delete('/delete_plan_options/{id}', [PlanController::class, 'delete_plan_options']);

        #users

        Route::get('/get_all_students_and_filter_by_status', [AdminUserController::class, 'view_all_students_and_filter']);
        Route::get('/get_all_proguides_and_filter_by_status', [AdminUserController::class, 'view_all_proguids_and_filter']);
        Route::put('/unblock_blocked_users/{id}', [AdminUserController::class, 'unblock_blocked_users']);
        Route::get('/search_students', [AdminUserController::class, 'search_students']);
        Route::get('/search_proguides', [AdminUserController::class, 'search_proguides']);

    });
});
