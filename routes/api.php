<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\BankDetailsController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\GroupMessagesController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\InterestsController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\User\UserInterestsController;
use App\Http\Controllers\Transaction\PaymentController;
use App\Http\Controllers\User\fetchCountriesController;
use App\Http\Controllers\Admin\QualificationsController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\User\UserQualificationController;
use App\Http\Controllers\User\UserSpecializationController;
use App\Http\Controllers\Transaction\WithdrawalRequestController;

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
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
Route::post('/register_user', [UserAuthController::class, 'create_user']);
Route::post('/verify_user', [UserAuthController::class, 'verify_user']);
Route::post('/login_user', [UserAuthController::class, 'login_user']);
Route::post('/user_forget_password', [UserAuthController::class, 'user_forget_password']);
Route::post('/user_reset_password', [UserAuthController::class, 'user_reset_password']);
Route::post('/create_user_password', [UserAuthController::class, 'create_user_password']);
Route::get('/get_countries', [fetchCountriesController::class, 'getAllCountries']);
Route::get('/get_states/{id}', [fetchCountriesController::class, 'getStatesWithCountry']);
Route::get('/get_specialization', [SpecializationController::class, 'findAll']);
Route::get('/get_qualification', [QualificationsController::class, 'findAll']);
Route::get('/get_services', [ServicesController::class, 'get_all_services']);
Route::get('/get_interests', [InterestsController::class, 'findAll']);
Route::get('/get_university', [UniversityController::class, 'get_all_university']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/edit_user_credentials', [UserAuthController::class, 'editUserCredentials']);
    Route::post('/update_profile_picture', [UserAuthController::class, 'update_profile_image']);
    Route::post('/change_user_password', [UserAuthController::class, 'user_change_password']);

    #get bio
    Route::get('/get_bio', [UserAuthController::class, 'get_bio']);

    #project api

    Route::post('/create_project', [ProjectController::class, 'create_project']);
    Route::get('/get_all_projects', [ProjectController::class, 'get_all_projects']);
    Route::get('/get_one_project/{id}', [ProjectController::class, 'get_projects_by_id']);
    Route::put('/edit_project/{id}', [ProjectController::class, 'edit_project']);
    Route::delete('/delete_project/{id}', [ProjectController::class, '']);
    #get user proguides
    Route::get('/get_user_proguides', [ProjectController::class, 'find_proguides_by_user_interests']);

    # messages
    Route::post('/create_message', [ChatsController::class, 'store']);
    Route::put('/update_message/{id}', [ChatsController::class, 'update']);
    Route::delete('/delete_message/{id}', [ChatssController::class, 'destroy']);
    Route::get('/get_messages', [ChatsController::class, 'index']);
    Route::get('/get_last_messages_in_chatlist', [ChatsController::class, 'getMessages']);
    Route::get('/get_messages_between_two_users/{id}', [ChatsController::class, 'show']);

    #user interests

    Route::post('/create_user_interests', [UserInterestsController::class, 'create_user_interests']);
    Route::get('/get_all_user_interests', [UserInterestsController::class, 'get_all_user_interests']);
    Route::post('/edit_user_interests', [UserInterestsController::class, 'edit_user_interests']);

    #user qualifications

    Route::post('/create_user_qualifications', [UserQualificationController::class, 'create_user_qualification']);
    Route::get('/get_user_qualifications', [UserQualificationController::class, 'get_all_user_qualifications']);
    Route::post('/edit_user_qualifications', [UserQualificationController::class, 'edit_user_qualification']);

    #user specialization

    Route::post('/create_user_specialization', [UserSpecializationController::class, 'create_user_specialization']);
    Route::get('/get_user_specialization', [UserSpecializationController::class, 'get_all_user_specialization']);
    Route::post('/edit_user_specialization', [UserSpecializationController::class, 'edit_user_specialization']);

    #bank details

    Route::post('/create_bank_details', [BankDetailsController::class, 'create_bank_details']);
    Route::get('/get_bank_details', [BankDetailsController::class, 'get_bank_details']);
    Route::get('/get_user_bank_details', [BankDetailsController::class, 'get_bank_details_for_a_particular_user']);

    #group
    Route::post('/create_group', [GroupController::class, 'create_group']);
    Route::get('/get_user_created_groups', [GroupController::class, 'get_all_groups_created_by_a_particular_user']);
    Route::get('/get_single_group_created_by_user/{id}', [GroupController::class, 'get_a_particular_group_for_a_user']);
    Route::get('/get_users_to_add_to_group', [GroupController::class, 'users_with_similar_interests']);
    Route::post('/add_user_to_group', [GroupController::class, 'add_users']);
    Route::delete('/delete_group/{id}', [GroupController::class, 'delete_group']);
    Route::delete('/delete_users_from_group/{user_id}/{group_id}', [GroupController::class, 'delete_users_from_group']);
    Route::put('/change_group_status/{id}', [GroupController::class, 'change_group_status']);
    Route::get('/get_groups_with_users', [GroupController::class, 'get_groups_with_users']);
    Route::get('/get_user_group', [GroupController::class, 'get_user_groups']);

    #withdrawal request

    Route::post('/create_withdrawal_request', [WithdrawalRequestController::class, 'create_withdrawal_request']);
    Route::get('/view_withdrawal_request', [WithdrawalRequestController::class, 'view_withdrawal_requests']);
    Route::put('/cancel_withdrawal_request/{id}', [WithdrawalRequestController::class, 'cancel_withdrawal_request']);

    #group chat

    Route::post('/send_group_chat', [GroupMessagesController::class, 'create_group_messages']);
    Route::get('/get_last_group_message', [GroupMessagesController::class, 'get_last_messages_in_a_group']);
    Route::get('/get_all_group_messages/{id}', [GroupMessagesController::class, 'get_group_messages']);

    #confirm payment

    Route::get('/confirm_payment/{reference}', [PaymentController::class, 'confirm_payment']);

});

require __DIR__ . '/admin.php';

Route::fallback(function () {
    return response()->json([
        'code' => 404,
        'message' => 'Route Not Found',
    ], 404);
});
