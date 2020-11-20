<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyAccount;
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
//Route::get('/send-mail', function () {
//
//    Mail::to('newuser@example.com')->send(new VerifyAccount('nguyenmanh@gmail.com'));
//
//    return 'A message has been sent to Mailtrap!';
//
//});
