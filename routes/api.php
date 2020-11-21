<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyAccount;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('signup', 'Api\AuthController@signup');
    Route::get('verify/{code}/{email}', 'Api\AuthController@verify');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
    });
});

//Class
Route::group([
    'prefix' => 'class',
    'middleware' => 'auth:api'
], function () {
    Route::get('all', 'Api\ClassController@all');
    Route::post('create', 'Api\ClassController@create');
    Route::put('update/{id}', 'Api\ClassController@update');
    Route::delete('delete/{id}', 'Api\ClassController@delete');
    Route::post('assign-module-to-class/{module_id}/{class_id}', 'Api\ClassController@assignModule');
    Route::get('modules', 'Api\ClassController@modules');
    Route::delete('delete-module/module', 'Api\ClassController@deleteModule');
});

//Module
Route::group([
    'prefix' => 'module',
    'middleware' => 'auth:api'
], function () {
    Route::get('allModules', 'Api\ModuleController@allModules');
    Route::get('selfModule', 'Api\ModuleController@selfModule');
    Route::post('create', 'Api\ModuleController@create');
    Route::put('update/{id}', 'Api\ModuleController@update');
    Route::delete('delete/{id}', 'Api\ModuleController@delete');
});
//Route::get('test/{email}', 'Api\AuthController@test');
