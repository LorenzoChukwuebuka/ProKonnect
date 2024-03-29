<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\InterestsController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\QualificationsController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\BankDetailsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupMessagesController;
use App\Http\Controllers\ProguideChatController;
use App\Http\Controllers\ProguideRatingController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\StudentsProguideController;
use App\Http\Controllers\Transaction\PaymentController;
use App\Http\Controllers\Transaction\WithdrawalRequestController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\User\fetchCountriesController;
use App\Http\Controllers\User\ReferalController;
use App\Http\Controllers\User\UserInterestsController;
use App\Http\Controllers\User\UserQualificationController;
use App\Http\Controllers\User\UserSpecializationController;
use App\Http\Controllers\VideoTutorialUploadController;
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
Route::get('/get_all_plans', [PlanController::class, 'get_all_plans']);
Route::get('/get_service_categories', [ServiceCategoryController::class, 'get_service_category']);
Route::get('/get_service_categories_with_service_id/{id}', [ServiceCategoryController::class, 'get_service_category_with_service_id']);
Route::get('/get_plan_option_with_plan_id/{id}', [PlanController::class, 'get_all_plan_options_with_plan_id']);
Route::get('/get_reviews', [RatingController::class, 'get_all_reviews']);
Route::get('/get_proguides_alphabetically', [UserAuthController::class, 'get_proguides_alphabetically']);

Route::get('/search_students', [AdminUserController::class, 'search_students']);
Route::get('/search_proguides', [AdminUserController::class, 'search_proguides']);

Route::get('/get_userinterests_by_interest_id/{id}', [UserAuthController::class, 'filter_userinterests_by_interests_id']);
Route::get('/get_userspecialization_by_specialization_id/{id}', [UserAuthController::class, 'filter_userspecialization_by_specialization_id']);
Route::get('/get_userqualification_by_qualification_id/{id}', [UserAuthController::class, 'filter_userqualification_by_qualification_id']);
Route::get('/get_student_qualifications_by_qualification_id/{id}', [UserAuthController::class, 'filter_student_userqualification_by_qualification_id']);

Route::get('/filter_proguides', [UserAuthController::class, 'filter_proguide']);
Route::get('/filter_students', [UserAuthController::class, 'filter_students']);

Route::get('/get_student_university/{university}', [UserAuthController::class, 'get_students_by_university']);
Route::get('/get_student_country/{country_id}', [UserAuthController::class, 'get_students_by_country']);

Route::get('/filter_proguides_by_rating', [UserAuthController::class, 'filter_by_rating']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/edit_user_credentials', [UserAuthController::class, 'editUserCredentials']);
    Route::post('/update_profile_picture', [UserAuthController::class, 'update_profile_image']);
    Route::post('/change_user_password', [UserAuthController::class, 'user_change_password']);
    Route::get('/get_user_details', [UserAuthController::class, 'user_details']);

    Route::get('/get_all_students', [UserAuthController::class, 'get_all_students']);
    Route::get('/get_all_proguides', [UserAuthController::class, 'get_all_proguides']);

    #get bio
    Route::get('/get_bio', [UserAuthController::class, 'get_bio']);

    #project api

    Route::post('/create_project', [ProjectController::class, 'create_project']);
    Route::get('/get_all_projects', [ProjectController::class, 'get_all_projects']);
    Route::get('/get_one_project/{id}', [ProjectController::class, 'get_projects_by_id']);
    Route::put('/edit_project/{id}', [ProjectController::class, 'edit_project']);
    Route::delete('/delete_project/{id}', [ProjectController::class, 'delete_project']);
    Route::get('/get_projects_by_proguide_id/{id}', [ProjectController::class, 'get_projects_by_proguide_id']);
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

    # payment
    Route::post('/initialize_payment', [PaymentController::class, 'initialize_payment']);
    Route::get('/confirm_payment/{reference}', [PaymentController::class, 'confirm_payment']);

    #socials

    Route::post('/create_socials', [UserAuthController::class, 'add_socials']);
    Route::get('/get_socials', [UserAuthController::class, 'get_socials']);
    Route::put('/edit_socials/{id}', [UserAuthController::class, 'edit_socials']);
    Route::delete('/delete_socials/{id}', [UserAuthController::class, 'delete_socials']);

    #referal
    Route::get('/get_referals', [ReferalController::class, 'get_referals_for_a_user']);
    Route::get('/get_referal_commissions', [ReferalController::class, 'get_referal_commission']);
    #rating

    Route::post('/create_rating', [RatingController::class, 'create_rating']);
    Route::get('/get_review_for_a_user', [RatingController::class, 'get_review_for_a_user']);

    #student proguide

    Route::post('/create_student_proguide', [StudentsProguideController::class, 'create_students_proguide']);
    Route::get('/get_student_proguide', [StudentsProguideController::class, 'get_all_students_proguides']);
    Route::get('/get_proguide_student', [StudentsProguideController::class, 'get_all_proguides_students']);
    Route::delete('/delete_student_proguide/{id}', [StudentsProguideController::class, 'delete_student_proguide']);

    Route::post('/create_tutorial', [VideoTutorialUploadController::class, 'create_tutorial']);
    Route::get('/get_all_tutorials', [VideoTutorialUploadController::class, 'get_all_tutorials']);
    Route::get('/get_all_tutorial_for_a_particular_proguide/{id}', [VideoTutorialUploadController::class, 'get_all_tutorials_for_a_particular_proguide']);
    Route::get('/get_single_tutorial/{id}', [VideoTutorialUploadController::class, 'get_single_tutorial']);
    Route::put('/edit_tutorial/{id}', [VideoTutorialUploadController::class, 'edit_tutorial']);
    Route::delete('/delete_tutorial/{id}', [VideoTutorialUploadController::class, 'delete_tutorial']);
    Route::get('/get_proguide_tutorial', [VideoTutorialUploadController::class, 'get_proguide_tutorial']);

    #proguide chat

    # messages
    Route::post('/create_pro_message', [ProguideChatController::class, 'store']);
    // Route::get('/get_pro_messages', [ProguideChatController::class, 'index']);
    Route::get('/get_last_messages_in_pro_chatlist', [ProguideChatController::class, 'getMessages']);
    Route::get('/get_pro_messages_between_two_users/{id}', [ProguideChatController::class, 'show']);

    #proguide rating

    Route::post('/create_proguide_rating', [ProguideRatingController::class, 'create_rating']);
    Route::get('/get_all_proguide_rating', [ProguideRatingController::class, 'get_all_reviews']);
    Route::get('/get_proguide_rating', [ProguideRatingController::class, 'get_review_for_a_user']);

    #wallets

    Route::get('/get_wallet_balance', [PaymentController::class, 'wallet_balance']);
    Route::get('/get_referal_wallet_balance', [ReferalController::class, 'get_referal_transactions_wallet_ballance']);

});

require __DIR__ . '/admin.php';

Route::fallback(function () {
    return response()->json([
        'code' => 404,
        'message' => 'Route Not Found',
    ], 404);
});
