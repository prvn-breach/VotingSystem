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

Route::get('/votingnew', 'IndexController@votingNewDesign');

Route::get('/downloadpdf', 'IndexController@downloadPdf');

Route::group([ 'middleware' => 'session' ], function () {
    // Route::get('/declaration/{enc_vtr_card_no}', 'IndexController@viewDeclaration');
    // Route::post('/submit-declaration/{enc_vtr_card_no}', 'IndexController@submitDeclaration');
    Route::get('/verification1/{enc_vtr_card_no}', 'IndexController@viewOtpPage1');
    Route::post('/submitOtp1/{enc_vtr_card_no}', 'IndexController@submitOtp1');
    Route::post('/resendOtp1', 'IndexController@resendOtp1');
    Route::get('/voting/{enc_vtr_card_no}', 'IndexController@viewVoting');
    Route::post('/submitVotes/{enc_vtr_card_no}', 'IndexController@submitVotes');
    Route::get('/verification2/{enc_vtr_card_no}', 'IndexController@viewOtpPage2');
    Route::post('/submitOtp2/{enc_vtr_card_no}', 'IndexController@submitOtp2');
    Route::post('/resendOtp2', 'IndexController@resendOtp2');
});

Route::get('/thankyou', 'IndexController@thankyou');
Route::get('/commonError', 'IndexController@commonError');
Route::get('/already-voted', 'IndexController@alreadyVoted');

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
