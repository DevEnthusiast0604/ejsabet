<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::patch('settings/profile', [ProfileController::class, 'update']);
    Route::patch('settings/password', [PasswordController::class, 'update']);
    Route::post('settings/generate_2fa_code', [ProfileController::class, 'generate_2fa_code']);
    Route::post('site_status/disable', [SettingController::class, 'disableSiteStatus']);
    Route::post('setting/set', [SettingController::class, 'set']);
});
Route::post('setting/get', [SettingController::class, 'get']);

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('site_status/enable', [SettingController::class, 'enableSiteStatus']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('2fa', [LoginController::class, 'checkOTP']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);
});

Route::group(['middleware' => 'auth:api'], function($router) {
    $router->post('get_dashboard_data', [HomeController::class, 'getDashboardData']);
    $router->post('get_daily_income', [SuperAdminController::class, 'getDailyIncome']);
    $router->post('get_monthly_revenue', [SuperAdminController::class, 'getMonthlyRevenue']);
    $router->post('get_compare_revenue', [SuperAdminController::class, 'getCompareRevenue']);
    $router->get('get_balance', [HomeController::class, 'getBalance']);
    $router->post('backup/upload', [SuperAdminController::class, 'uploadDatabaseBackup']);
});

Route::group(['prefix' => 'transaction', 'middleware' => 'auth:api'], function ($router) {
    $router->post('search', [TransactionController::class, 'search']);
    $router->post('get_total', [TransactionController::class, 'getTotal']);
    $router->post('create', [TransactionController::class, 'create']);
    $router->post('update', [TransactionController::class, 'update']);
    $router->post('upload_image', [TransactionController::class, 'uploadTransactionImage']);
    $router->post('audit', [TransactionController::class, 'audit']);
    $router->post('get_detail', [TransactionController::class, 'getDetail']);
    $router->post('/organize', [TransactionController::class, 'organize']);
    $router->delete('delete/{id}', [TransactionController::class, 'delete']);
    $router->post('authorize/{id}', [TransactionController::class, 'authorizeTransaction']);
    $router->post('super_admin_approve/{id}', [TransactionController::class, 'superAdminApprove']);
    $router->get('/get_last_transaction_date', [TransactionController::class, 'getLastTransactionDate']);
});

Route::group(['prefix' => 'category', 'middleware' => 'auth:api'], function ($router) {
    $router->post('search', [CategoryController::class, 'search']);
    $router->post('create', [CategoryController::class, 'create']);
    $router->post('update', [CategoryController::class, 'update']);
    $router->post('change_status', [CategoryController::class, 'changeStatus']);
    $router->post('approve_by_super_admin/{id}', [CategoryController::class, 'superAdminApprove']);
    $router->delete('delete/{id}', [CategoryController::class, 'delete']);
});

Route::group(['prefix' => 'account', 'middleware' => 'auth:api'], function ($router) {
    $router->get('search', [AccountController::class, 'search']);
    $router->post('create', [AccountController::class, 'create']);
    $router->post('update', [AccountController::class, 'update']);
    $router->get('delete/{id}', [AccountController::class, 'delete']);
});

Route::group(['prefix' => 'company', 'middleware' => 'auth:api'], function ($router) {
    $router->get('search', [CompanyController::class, 'search']);
    $router->post('create', [CompanyController::class, 'create']);
    $router->post('update', [CompanyController::class, 'update']);
    $router->get('delete/{id}', [CompanyController::class, 'delete']);
    $router->post('get_balance', [CompanyController::class, 'getBalance']);
});

Route::group(['prefix' => 'user'], function ($router) {
    $router->get('change_roles', [UserController::class, 'changeRoles']);
    $router->group(['middleware' => 'auth:api'], function($router) {
        $router->any('search', [UserController::class, 'search']);
        $router->get('/', [UserController::class, 'current']);
        $router->post('create', [UserController::class, 'create']);
        $router->post('update', [UserController::class, 'update']);
        $router->post('assign_company', [UserController::class, 'assignCompany']);
        $router->get('delete/{id}', [UserController::class, 'delete']);
    });
});

Route::group(['prefix' => 'advanced_delete', 'middleware' => 'auth:api'], function ($router) {
    $router->post('/request', [HomeController::class, 'requestAdvancedDelete']);
    $router->post('/verify', [HomeController::class, 'verifyAdvancedDelete']);
});

Route::group(['prefix' => 'audit', 'middleware' => 'auth:api'], function ($router) {
    $router->any('search', [AuditController::class, 'search']);
    $router->post('save', [AuditController::class, 'save']);
    $router->get('delete/{id}', [AuditController::class, 'delete']);
    $router->get('get_latest_audit_date', [AuditController::class, 'getLatestAuditDate']);
});

Route::group(['prefix' => 'image', 'middleware' => 'auth:api'], function ($router) {
    $router->delete('delete/{id}', [TransactionController::class, 'deleteImage']);
});

Route::group(['prefix' => 'company_ip', 'middleware' => 'auth:api'], function ($router) {
    $router->post('save', [HomeController::class, 'saveCompanyIp']);
    $router->delete('delete/{id}', [HomeController::class, 'deleteCompanyIp']);
});
Route::get('/company_ip', [HomeController::class, 'getCompanyIps']);


Route::get('update_categories', [CategoryController::class, 'updateCategories']);
Route::get('change_image_path', [TransactionController::class, 'changeImagePath']);


Route::get('test_pdf', [AuditController::class, 'generatePdf']);
Route::any('search_news', [NewsController::class, 'search']);
Route::get('get_client_ip', [HomeController::class, 'getClientIp']);

Route::get('/get_accounts', [TransactionController::class, 'getAccounts']);