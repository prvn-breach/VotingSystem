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
    Route::get('/verification1/{candidate_voter_card}', 'IndexController@viewOtpPage1');
    Route::post('/submitOtp1/{candidate_voter_card}', 'IndexController@submitOtp1');
    Route::post('/resendOtp1', 'IndexController@resendOtp1');
    Route::get('/voting/{candidate_voter_card}', 'IndexController@viewVoting');
    Route::post('/submitVotes/{candidate_voter_card}', 'IndexController@submitVotes');
    Route::get('/verification2/{candidate_voter_card}', 'IndexController@viewOtpPage2');
    Route::post('/submitOtp2/{candidate_voter_card}', 'IndexController@submitOtp2');
    Route::post('/resendOtp2', 'IndexController@resendOtp2');
});

Route::get('/thankyou', 'IndexController@thankyou');
Route::get('/commonError', 'IndexController@commonError');
Route::get('/already-voted', 'IndexController@alreadyVoted');
Route::get('/live', 'IndexController@liveCount');

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
