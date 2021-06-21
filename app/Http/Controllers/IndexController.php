<?php

namespace App\Http\Controllers;

use DB;
use Session;
use Validator;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\AssociationVoter;

class IndexController extends Controller
{
    public function viewDeclaration($id) {
        $enc_card_no = openssl_decrypt($id, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$enc_card_no) {
            return view('error');
        }
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $enc_card_no)->first()->toArray();
        if (!$voter_details) {
            return view('error');
        }
        Session::put('enc_card_no', $id);
        return view('declaration'); 
    }

    public function submitDeclaration(Request $request) {
        $enc_card_no = Session::get('enc_card_no');
        $validator = Validator::make(
            array (
                'accept' => $request['accept'],
                'g-recaptcha-response' => $request['g-recaptcha-response'],
            ), array (
                'accept' => 'required|in:on',
                'g-recaptcha-response' => new Captcha(),
            )
        );

        if ($validator->fails()) {
            return redirect('declaration/'.$enc_card_no)->withErrors($validator)->withInput();
        }

        return redirect('verification1');
    }


    public function viewOtpPage1() {
        return view('otppage1');
    }
}