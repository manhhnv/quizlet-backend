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
    Route::get('detail', 'Api\ClassController@index');
    Route::get('generate-shared-link/{id}/{code}', 'Api\ClassController@generateLink');
    Route::post('send-shared-link', 'Api\ClassController@sendSharedLink');
    Route::group([
        "prefix" => "module"
    ], function () {
        Route::post('assign-module-to-class/{module_id}/{class_id}', 'Api\ClassController@assignModule');
        Route::get('all', 'Api\ClassController@modules');
        Route::delete('delete-from-class', 'Api\ClassController@deleteModule');
        Route::post('add-module-in-class/{id}/{code}', 'Api\ClassController@addModuleInClass');
    });
    Route::group([
        'prefix' => 'folder'
    ], function () {
        Route::post('assign-folder-to-class/{folder_id}/{class_id}', 'Api\ClassController@assignFolder');
        Route::get('all', 'Api\ClassController@folders');
        Route::delete('delete-from-class','Api\ClassController@deleteFolder');
        Route::post('add-folder-in-class/{id}/{code}', 'Api\ClassController@addFolderToClass');
    });
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
    Route::group([
        'prefix' => 'term'
    ], function () {
        Route::post('create', 'Api\TermController@create');
        Route::get('detail/{id}', 'Api\TermController@index');
        Route::get('get-list-terms/{module_id}', 'Api\TermController@getTermByModule');
        Route::put('update-term-by-module/{module_id}/{term_id}', 'Api\TermController@update');
        Route::delete('delete/{module_id}/{term_id}', 'Api\TermController@delete');
    });
});

//Folder
Route::group([
    'prefix' => 'folder',
    'middleware' => 'auth:api'
], function () {
    Route::post('create', 'Api\FolderController@create');
    Route::get('detail', 'Api\FolderController@index');
    Route::get('listFolder', 'Api\FolderController@listFolders');
    Route::put('update/{folder_id}', 'Api\FolderController@update');
    Route::delete('delete/{id}', 'Api\FolderController@delete');
    Route::get('folder-detail', 'Api\FolderController@folderDetail');
    Route::get('generate-shared-link/{id}/{code}', 'Api\FolderController@generateLink');
    Route::post('send-shared-link', 'Api\FolderController@sendSharedLink');
    Route::group([
        'prefix' => 'module'
    ], function (){
        Route::get('list', 'Api\FolderController@modules');
        Route::post('assign-to-folder/{module_id}/{folder_id}', 'Api\FolderController@assignModule');
        Route::delete('delete-from-folder', 'Api\FolderController@deleteModuleFromFolder');
        Route::post('add-module-in-folder/{id}/{code}', 'Api\FolderController@addModuleInFolder');
    });
});
//Member
Route::group([
    'prefix' => 'member',
    'middleware' => 'auth:api'
], function () {
    Route::get('list-joined-members/{class_id}', 'Api\MembersController@listMembers');
    Route::get('list-joined-classes', 'Api\MembersController@joinedClass');
    Route::post('join-class/{class_id}', 'Api\MembersController@join');
    Route::delete('leave-joined-class/{class_id}', 'Api\MembersController@leaveClass');
    Route::delete('delete-joined-member', 'Api\MembersController@deleteMemberFromClass');
});
//Route::get('test/{email}', 'Api\AuthController@test');
Route::group([
    'prefix' => 'join',
    'middleware' => 'auth:api'
], function () {
    Route::group([
        'prefix' => 'module'
    ], function () {
        Route::get('/{id}', 'Api\ViewModule@viewModule');
    });
    Route::group([
        'prefix' => 'folder'
    ], function () {
        Route::get('sharing/{username}/{folder_id}/{code}', 'Api\FolderController@sharing');
    });
});
