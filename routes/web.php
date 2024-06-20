<?php

use App\Http\Controllers\LineNotify\LineNotifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

// line notify
Route::prefix('linenotify')->group(function () {
    Route::get('/', [LineNotifyController::class, 'index']);
    Route::get('/getToken', [LineNotifyController::class, 'getToken']);
    Route::post('/sendMessage', [LineNotifyController::class, 'sendMessage']);
    Route::get('/sendClient', [LineNotifyController::class, 'sendClient']);
});
