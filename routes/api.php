<?php

use App\Http\Controllers\Daily\DailyAccountController;
use App\Http\Controllers\Daily\DailyApiController;
use App\Http\Controllers\Daily\DailyArticleController;
use App\Http\Controllers\Daily\DailyProjectController;
use App\Http\Controllers\Daily\DailyUnitController;
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

// Basic JWT
Route::match(['post', 'get'], '/', [DailyApiController::class, 'index']);
Route::match(['post', 'get'], 'getToken', [DailyApiController::class, 'getToken']);
Route::post('revokeToken', [DailyApiController::class, 'revokeToken']);

Route::match(['post', 'get'], 'getTokenExpir', [DailyApiController::class, 'getTokenExpir']);
Route::post('RefreshToken', [DailyApiController::class, 'RefreshToken']);



// Account
Route::group(['prefix' => '/account'], function () {
    Route::post('register', [DailyAccountController::class, 'create']);
    Route::put('{account_id}/edit', [DailyAccountController::class, 'edit']);
    Route::delete('{account_id}/delete', [DailyAccountController::class, 'delete']);
    Route::match(['get', 'post'], 'show/{action?}', [DailyAccountController::class, 'index']);
});

// CRUD
Route::group(['prefix' => '/show'], function () {
    Route::match(['post', 'get'], 'article/{article_id?}', [DailyArticleController::class, 'index']);
    Route::match(['post', 'get'], 'project/{project_id?}', [DailyProjectController::class, 'index']);
    Route::match(['post', 'get'], 'unit/{unit_id?}', [DailyUnitController::class, 'index']);
});

Route::group(['prefix' => '/create'], function () {
    Route::post('article', [DailyArticleController::class, 'create']);
    Route::post('project', [DailyProjectController::class, 'create']);
    Route::post('unit', [DailyUnitController::class, 'create']);
});

Route::group(['prefix' => '/edit'], function () {
    Route::put('{id}/article', [DailyArticleController::class, 'edit']);
    Route::put('{id}/unit', [DailyUnitController::class, 'edit']);
    Route::put('{id}/project', [DailyProjectController::class, 'edit']);
});

Route::group(['prefix' => '/delete'], function () {
    Route::delete('{id}/article', [DailyArticleController::class, 'delete']);
    Route::delete('{id}/unit', [DailyUnitController::class, 'delete']);
    Route::delete('{id}/project', [DailyProjectController::class, 'delete']);
});
