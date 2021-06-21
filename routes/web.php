<?php

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

// Route::get('/', function () {
//     return view('entrypage');
// });

Route::get('/declaration/{id}', 'IndexController@viewDeclaration');
Route::post('/submit-declaration', 'IndexController@submitDeclaration');
Route::get('/verification1', 'IndexController@viewOtpPage1');

Route::group([ 'middleware' => 'auth' ], function () {
});

// Route::get('/', function () {
//     return view('entrypage');
// });


// Route::get('/declaration', function () {
//     return view('declaration');
// });

// Route::get('/otp', function () {
//     return view('otppage');
// });

// Route::get('/voting', function () {
//     return view('voting');
// });

// Route::get('/thankyou', function () {
//     return view('thankyou');
// });

// Route::get('/error', function () {
//     return view('error');
// });

// Route::get('/pollcompleted', function () {
//     return view('pollcompleted');
// });

// Route::get('/alreadyvoted', function () {
//     return view('alreadyvoted');
// });