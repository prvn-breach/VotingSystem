<?php

namespace App\Http\Controllers;

use DB;
use Session;
use Validator;
use Carbon\Carbon;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\ElectionVote;
use App\Models\ElectionPost;
use App\Models\AssociationVoter;
use App\Models\ElectionParticipant;
use App\Models\ElectionVoterSession;

class IndexController extends Controller
{

    public function viewDeclaration($enc_vtr_card_no) {

        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$vtr_card_no) {
            return view('error');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return view('error');
        }

        ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id']
        ])->update([
            'is_active' => 0
        ]);

        Session::put('enc_vtr_card_no', $enc_vtr_card_no);
        session()->regenerate();
        return view('declaration'); 
    }

    public function submitDeclaration(Request $request, $enc_vtr_card_no) {
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$vtr_card_no) {
            return view('error');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return view('error');
        }

        if(ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'is_active' => 1
        ])->count() > 0) {
            return view('error');
        }

        $validator = Validator::make (
            array (
                'accept' => $request['accept'],
                'g-recaptcha-response' => $request['g-recaptcha-response'],
            ), array (
                'accept' => 'required|in:on',
                'g-recaptcha-response' => new Captcha(),
            )
        );

        if ($validator->fails()) {
            return redirect('declaration/'.$enc_vtr_card_no)->withErrors($validator)->withInput();
        }

        return redirect('verification1/'.$enc_vtr_card_no);
    }

    public function viewOtpPage1($enc_vtr_card_no) {
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$vtr_card_no) {
            return view('error');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return view('error');
        }

        if (!Session::has('enc_vtr_card_no')) {
            ElectionVoterSession::where([
                'asoci_vtr_id' => $voter_details['asoci_vtr_id']
            ])->update([
                'is_active' => 0
            ]);
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        if ( !ElectionVoterSession::where([
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId()
        ])->first() ) {
            $elec_vtr_session = ElectionVoterSession::create([
                'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
                'session_id' => Session::getId(),
                'otp' => '1234',
                'otp_expires_on' => Carbon::now()->addMinutes(2),
                'ip_address' => null,
                'latitude' => null,
                'longitude' => null,
                'session_started_on' => Carbon::now(),
                'session_ended_on' => null,
                'session_auth_key' => null,
                'is_active' => 1
            ]);
            if (empty($elec_vtr_session)) {
                return view('error');
            }
        }

        return view('otppage1');
    }

    public function submitOtp1(Request $request, $enc_vtr_card_no) {
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$vtr_card_no) {
            return view('error');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return view('error');
        }

        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return view('error');
        }

        $elec_vtr_session = $elec_vtr_session->toArray();

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['otp']
            )
        );

        if ($validator->fails()) {
            return redirect('verification1/'.$enc_vtr_card_no)->withErrors($validator)->withInput();
        }

        return redirect('voting/'.$enc_vtr_card_no);
    }

    public function viewVoting($enc_vtr_card_no) {
        $election_posts = ElectionPost::with('participants')->orderBy('priority_seq', 'asc')->get();
        if (empty($election_posts)) {
            $election_posts = [];
        } else {
            $election_posts = $election_posts->toArray();
        }
        
        foreach ($election_posts as $index1 => $post) {
            foreach ($post['participants'] as $index2 => $participant) {
                $voter = AssociationVoter::where('asoci_vtr_id', $participant['asoci_vtr_id'])->first();
                if (empty($voter)) {
                    $voter = null;
                } else {
                    $voter = $voter->toArray();
                }
                $election_posts[$index1]['participants'][$index2]['voter'] = $voter;
            }
        }
        
        return view('voting')->with('election_posts', $election_posts);
    }

    public function submitVotes(Request $request, $enc_vtr_card_no) {
        Session::put('participant_ids', $request['participent_ids']);
        return redirect('verification2/'.$enc_vtr_card_no);
    }

    public function viewOtpPage2($enc_vtr_card_no) {
        return view('otppage2');
    }

    public function submitOtp2(Request $request, $enc_vtr_card_no) { 
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, ''); 
        if (!$vtr_card_no) {
            return view('error');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return view('error');
        }

        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return view('error');
        }

        $elec_vtr_session = $elec_vtr_session->toArray();

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['otp']
            )
        );

        if ($validator->fails()) {
            return redirect('verification2/'.$enc_vtr_card_no)->withErrors($validator)->withInput();
        }

        $participant_ids = Session::get('participant_ids');
        if (!empty($participant_ids)) {
        foreach ($participant_ids as $id) {
            $iv = $this->generateRandomString(16);
            $bytes = openssl_random_pseudo_bytes('16');
            $secret_key = bin2hex($bytes);
            $enc_participnt_id = openssl_encrypt($id, config('app.cipher'), $secret_key, 0, $iv); 
            $enc_vtr_id = openssl_encrypt($voter_details['asoci_vtr_id'], config('app.cipher'), $secret_key, 0, $iv);
        
            ElectionVote::create([
                'elec_participnt_id' => $enc_participnt_id,
                'asoci_vtr_id' => $enc_vtr_id,
                'vote_receipt_key' => $secret_key
            ]);
        }
        }

        return view('thankyou');
    }

    function generateRandomString($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ%#$&*()^,?!';
        $randomString = '';
        
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        
        return $randomString;
    }
}