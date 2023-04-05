<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
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
    //auth()->login(User::first());
    return 'api server functioning properly.';
    return view('welcome');
});

Route::get('/optimize', function () {
    \Artisan::call('optimize:clear');
    return 'done';
});

Route::get('/verified/{person_id}/{user_id}/{from}/{method}', [App\Http\Controllers\CallbackController::class, 'verified']);

Route::get('/check-event', [App\Http\Controllers\CallbackController::class, 'checkEvent']);

Route::get('/check-notification', [App\Http\Controllers\CallbackController::class, 'checkNotification']);

Route::get('/check-childs', [App\Http\Controllers\CallbackController::class, 'checkChilds']);

Route::get('/check-bank-id', [App\Http\Controllers\CallbackController::class, 'checkBankId']);

