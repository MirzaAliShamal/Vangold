<?php

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

Route::post('login', 'API\UserController@login')->name('login');
Route::post('register', 'API\UserController@register')->name('register');

Route::middleware('auth:api')->group(function () {
    Route::get('email/verify/{id}', 'API\UserController@verify')->name('verification.verify');
    Route::get('email/verify/resend', 'API\UserController@verifyResend')->name('verification.resend');
});
