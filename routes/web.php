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

Route::get('/', 'IndexController@entryPage');
Route::get('/declaration/{enc_vtr_card_no}', 'IndexController@viewDeclaration');
Route::post('/submit-declaration/{enc_vtr_card_no}', 'IndexController@submitDeclaration');
Route::get('/verification1/{enc_vtr_card_no}', 'IndexController@viewOtpPage1');
Route::post('/submitOtp1/{enc_vtr_card_no}', 'IndexController@submitOtp1');
Route::get('/voting/{enc_vtr_card_no}', 'IndexController@viewVoting');
Route::post('/submitVotes/{enc_vtr_card_no}', 'IndexController@submitVotes');
Route::get('/verification2/{enc_vtr_card_no}', 'IndexController@viewOtpPage2');
Route::post('/submitOtp2/{enc_vtr_card_no}', 'IndexController@submitOtp2');

Route::group([ 'middleware' => 'auth' ], function () {
    // Route::get('/', function () {
    //     return view('entrypage');
    // });
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
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
