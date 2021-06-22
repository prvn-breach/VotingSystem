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
    public function RandomString()
    {
        // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring.= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    public function viewDeclaration($enc_vtr_card_no) {
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0 ) 
        {
            return redirect('already-voted');
        }

        // All Sessions will be inactive
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
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0 ) 
        {
            return redirect('already-voted');
        }

        // If Session Expire
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        if(ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'is_active' => 1
        ])->count() > 0) {
            return redirect('commonError');
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
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        $elec_vtr_session = ElectionVoterSession::where([
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();
        if (empty($elec_vtr_session)) {
            $created_elec_vtr_session = ElectionVoterSession::create([
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
            if (empty($created_elec_vtr_session)) {
                return redirect('commonError');
            }
            $elec_vtr_session = $created_elec_vtr_session->toArray();
        } else {
            $elec_vtr_session = $elec_vtr_session->toArray();
        }

        return view('otppage1')->with('otpExpiryDate', $elec_vtr_session['otp_expires_on']);
    }

    public function submitOtp1(Request $request, $enc_vtr_card_no) {
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        // If Session Expire
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return redirect('commonError');
        }

        $elec_vtr_session = $elec_vtr_session->toArray();

        $diff = strtotime($elec_vtr_session['otp_expires_on']) - strtotime(Carbon::now());

        $otp_validate_msg = 'The Otp is invalid';
        if ($diff<=0) {
            $elec_vtr_session['otp'] = null;
            $otp_validate_msg = 'The Otp was expired. please resend otp';
        }

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['otp']
            ), array (
                'otp.required' => 'the otp field is required.',
                'otp.in' => $otp_validate_msg
            )
        );
        if ($validator->fails()) {
            return redirect('verification1/'.$enc_vtr_card_no)->withErrors($validator)->with('otpExpiryDate', $elec_vtr_session['otp_expires_on']);
        }

        return redirect('voting/'.$enc_vtr_card_no);
    }

    public function resendOtp1(Request $request) {
        $enc_vtr_card_no = $request['enc_vtr_card_no'];
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        // IF Voter is InActive
        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return redirect('commonError');
        }

        // Update OTP
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'otp' => '1234',
            'otp_expires_on' => Carbon::now()->addMinutes(2)
        ]);

        return redirect('verification1/'.$enc_vtr_card_no)->with('otpExpiryDate', Carbon::now()->addMinutes(2));
    }

    public function viewVoting($enc_vtr_card_no) {
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        // If Session Expire
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        if (ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->count() == 0) {
            return redirect('commonError');
        }

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
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        // IF Session Expire
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        Session::put('participant_ids', $request['participent_ids']);
        return redirect('verification2/'.$enc_vtr_card_no);
    }

    public function viewOtpPage2($enc_vtr_card_no) {
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        // IF Session Expire
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return redirect('commonError');
        }

        $elec_vtr_session = $elec_vtr_session->toArray();


        ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id']
        ])->update([
            'otp' => '2345',
            'otp_expires_on' => Carbon::now()->addMinutes(2)
        ]);

        return view('otppage2')->with('otpExpiryDate', Carbon::now()->addMinutes(2));
    }

    public function submitOtp2(Request $request, $enc_vtr_card_no) { 
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }
    
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        // IF SESSION EXPIRE
        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        // IF Voter is InActive
        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return redirect('commonError');
        }

        $elec_vtr_session = $elec_vtr_session->toArray();

        $diff = strtotime($elec_vtr_session['otp_expires_on']) - strtotime(Carbon::now());

        $otp_validate_msg = 'The Otp is invalid';
        if ($diff<=0) {
            $elec_vtr_session['otp'] = null;
            $otp_validate_msg = 'The Otp was expired. please resend otp';
        }

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['otp']
            ), array (
                'otp.required' => 'the otp field is required.',
                'otp.in' => $otp_validate_msg
            )
        );
        if ($validator->fails()) {
            return redirect('verification2/'.$enc_vtr_card_no)->withErrors($validator)->with('otpExpiryDate', $elec_vtr_session['otp_expires_on']);
        }

        $participant_ids = Session::get('participant_ids');
        if (!empty($participant_ids)) {
            $enc_vtr_id = openssl_encrypt($voter_details['asoci_vtr_id'], config('app.cipher'), config('app.key'), 0, config('app.IV'));
            foreach ($participant_ids as $id) {
                $ids = explode(':', $id);
                $enc_participnt_id = openssl_encrypt($ids[0], config('app.cipher'), config('app.key'), 0, config('app.IV')); 
                ElectionVote::create([
                    'elec_participnt_id' => $enc_participnt_id,
                    'asoci_vtr_id' => $enc_vtr_id
                ]);
            }
        }

        // Update vote_receipt_key and session_ended_on 
        $receipt_key = openssl_encrypt(json_encode($participant_ids), config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'vote_receipt_key' => $receipt_key,
            'session_ended_on' => Carbon::now()
        ]);

        // Destroy session
        Session::flush();

        return redirect('thankyou');
    }

    public function resendOtp2(Request $request) {
        $enc_vtr_card_no = $request['enc_vtr_card_no'];
        $vtr_card_no = openssl_decrypt($enc_vtr_card_no, config('app.cipher'), config('app.key'), 0, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // IF Already voted
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0) 
        {
            return redirect('already-voted');
        }

        if (!Session::has('enc_vtr_card_no')) {
            return redirect('declaration/'.$enc_vtr_card_no);
        }

        // IF Voter is InActive
        $elec_vtr_session = ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();

        if(empty($elec_vtr_session)) {
            return redirect('commonError');
        }

        // Update OTP
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'otp' => '1234',
            'otp_expires_on' => Carbon::now()->addMinutes(2)
        ]);

        return redirect('verification2/'.$enc_vtr_card_no)->with('otpExpiryDate', Carbon::now()->addMinutes(2));
    }

    function thankyou() {
        return view('thankyou');
    }

    function commonError() {
        Session::flush();
        return view('error');
    }

    function alreadyVoted() {
        Session::flush();
        return view('alreadyvoted');
    }
}