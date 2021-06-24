<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Session;
use Validator;
use Carbon\Carbon;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\AuditLog;
use App\Models\ElectionVote;
use App\Models\ElectionPost;
use App\Models\AssociationVoter;
use App\Models\ElectionParticipant;
use App\Models\ElectionVoterSession;

class IndexController extends Controller
{
    public function __construct(AuditLog $auditLog, ElectionVoterSession $vtrSession)
    {
        $this->auditLog = $auditLog;
        $this->vtrSession = $vtrSession;
    } 
    // public function viewDeclaration($candidate_voter_card) {
    //     // All Sessions will be inactive
    //     ElectionVoterSession::where([ 
    //         'asoci_vtr_id' => Auth::user()->asoci_vtr_id
    //     ])->update([
    //         'is_active' => 0
    //     ]);

    //     Session::put('enc_vtr_card_no', $enc_vtr_card_no);
    //     return view('declaration'); 
    // }

    // public function submitDeclaration(Request $request, $enc_vtr_card_no) {
    //     $validator = Validator::make (
    //         array (
    //             'accept' => $request['accept'],
    //             'g-recaptcha-response' => $request['g-recaptcha-response'],
    //         ), array (
    //             'accept' => 'required|in:on',
    //             'g-recaptcha-response' => new Captcha(),
    //         )
    //     );

    //     if ($validator->fails()) {
    //         return redirect('declaration/'.$enc_vtr_card_no)->withErrors($validator)->withInput();
    //     }

    //     return redirect('verification1/'.$enc_vtr_card_no);
    // }

    public function viewOtpPage1($candidate_voter_card) {

        $client_ip = json_decode($this->CallAPI('GET', 'https://api.ipify.org/?format=json'))->ip;

        // All Sessions will be inactive
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id
        ])->update([
            'is_active' => 0
        ]);

        Session::put('candidate_voter_card', $candidate_voter_card);

        $elec_vtr_session = $this->vtrSession->getCurrentVoterSession();

        $otp = $this->generateNumericOTP(4);
        
        if (empty($elec_vtr_session)) {
            $created_elec_vtr_session = ElectionVoterSession::create([
                'asoci_vtr_id' => Auth::user()->asoci_vtr_id,
                'session_id' => Session::getId(),
                'otp' => '1234',
                'otp_expires_on' => Carbon::now()->addMinutes(2),
                'ip_address' => json_decode($this->CallAPI('GET', 'https://api.ipify.org/?format=json'))->ip,
                'user_agent_data' => $_SERVER['HTTP_USER_AGENT'],
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
            $elec_vtr_session['otp_expiry'] = $created_elec_vtr_session['otp_expires_on'];
        }

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'Clicked on SMS link and Recieved OTP'
        ]);

        // $this->sendSms($otp);

        return view('otppage1')->with('otpExpiryDate', $elec_vtr_session['otp_expiry']);
    }

    public function submitOtp1(Request $request, $candidate_voter_card) {

        if (
            $request['username']!=null || 
            $request['password']!=null || 
            $request['sec_key']!=null
        ) {
            return redirect('commonError');
        }

        $elec_vtr_session = $this->vtrSession->getCurrentVoterSession();

        $diff = strtotime($elec_vtr_session['otp_expiry']) - strtotime(Carbon::now());

        $otp_validate_msg = 'The Otp is invalid';
        if ($diff<=0) {
            $elec_vtr_session['pin'] = null;
            $otp_validate_msg = 'The Otp was expired. please resend otp';
        }

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['pin']
            ), array (
                'otp.required' => 'the otp field is required.',
                'otp.in' => $otp_validate_msg
            )
        );

        if ($validator->fails()) {
            // Add Log
            $this->auditLog->addLog([
                'voter_id' => Auth::user()->asoci_vtr_id,
                'session_id' => Session::getId(),
                'comments' => 'Before poll start wrong OTP submitted'
            ]);
            return view('otppage1')->withErrors($validator)->with('otpExpiryDate', $elec_vtr_session['otp_expiry']);
        }

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'Correct OTP submitted and entered into voting screen'
        ]);

        return redirect('voting/'.$candidate_voter_card);
    }

    public function resendOtp1(Request $request) {
        $candidate_voter_card = $request['candidate_voter_card'];

        // Update OTP
        $extended_time = Carbon::now()->addMinutes(2);
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'otp' => '1234',
            'otp_expires_on' => $extended_time
        ]);

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'Before poll start resend OTP clicked'
        ]);

        return redirect('verification1/'.$candidate_voter_card)->with('otpExpiryDate', $extended_time);
    }

    public function posts() {
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

        return $election_posts;
    }

    public function viewVoting($candidate_voter_card) {
        $election_posts = $this->posts();
        return view('voting')->with('election_posts', $election_posts);
    }

    public function submitVotes(Request $request, $candidate_voter_card) {
        Session::put('participant_ids', $request['participent_ids']);

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'End poll and received OTP'
        ]);

        return redirect('verification2/'.$candidate_voter_card);
    }

    public function viewOtpPage2($candidate_voter_card) {
        $extended_time = Carbon::now()->addMinutes(2);
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id
        ])->update([
            'otp' => '2345',
            'otp_expires_on' => $extended_time
        ]);

        return view('otppage2')->with('otpExpiryDate', $extended_time);
    }

    public function submitOtp2(Request $request, $candidate_voter_card) { 
        if (
            $request['username']!=null || 
            $request['password']!=null || 
            $request['sec_key']!=null
        ) {
            return redirect('commonError');
        }

        $elec_vtr_session = $this->vtrSession->getCurrentVoterSession();

        $diff = strtotime($elec_vtr_session['otp_expiry']) - strtotime(Carbon::now());

        $otp_validate_msg = 'The Otp is invalid';
        if ($diff<=0) {
            $elec_vtr_session['pin'] = null;
            $otp_validate_msg = 'The Otp was expired. please resend otp';
        }

        $validator = Validator::make (
            array (
                'otp' => $request['otp']
            ), array (
                'otp' => 'required|in:'.$elec_vtr_session['pin']
            ), array (
                'otp.required' => 'the otp field is required.',
                'otp.in' => $otp_validate_msg
            )
        );

        if ($validator->fails()) {
            // Add Log
            $this->auditLog->addLog([
                'voter_id' => Auth::user()->asoci_vtr_id,
                'session_id' => Session::getId(),
                'comments' => 'After end poll wrong OTP submitted'
            ]);
            return view('otppage2')->withErrors($validator)->with('otpExpiryDate', $elec_vtr_session['otp_expires_on']);
        }

        $participant_ids = Session::get('participant_ids');
        $voted_participants = [];
        if (!empty($participant_ids)) {
            $enc_vtr_id = bin2hex(openssl_encrypt(Auth::user()->asoci_vtr_id, config('app.cipher'), config('app.key'), OPENSSL_RAW_DATA, config('app.IV')));
            foreach ($participant_ids as $id) {
                $ids = explode(':', $id);
                $voted_participants[] = $ids[1];
                $enc_participnt_id = bin2hex(openssl_encrypt($ids[1], config('app.cipher'), config('app.key'), OPENSSL_RAW_DATA, config('app.IV'))); 
                ElectionVote::create([
                    'elec_participnt_id' => $enc_participnt_id,
                    'asoci_vtr_id' => $enc_vtr_id
                ]);
            }
        }

        // Update vote_receipt_key and session_ended_on 
        $receipt_key = bin2hex(openssl_encrypt(json_encode($participant_ids), config('app.cipher'), config('app.key'), OPENSSL_RAW_DATA, config('app.IV'))); 
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'vote_receipt_key' => $receipt_key,
            'session_ended_on' => Carbon::now()
        ]);

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'After end poll Correct OTP submitted and entered into thank you page'
        ]);

        // Save Pdf
        $this->createPDF($voted_participants);

        // Destroy session
        Session::flush();

        return redirect('thankyou');
    }

    public function resendOtp2(Request $request) {
        $candidate_voter_card = $request['candidate_voter_card'];

        // Update OTP
        $extended_time = Carbon::now()->addMinutes(2);
        ElectionVoterSession::where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->update([
            'otp' => '1234',
            'otp_expires_on' => $extended_time
        ]);

        // Add Log
        $this->auditLog->addLog([
            'voter_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'comments' => 'After poll start resend OTP clicked'
        ]);

        return redirect('verification2/'.$candidate_voter_card)->with('otpExpiryDate', $extended_time);
    }

    public function thankyou() {
        Session::pull('candidate_voter_card');
        Session::pull('participant_ids');
        Session::flush();
        Auth::logout();
        return view('thankyou');
    }

    public function commonError() {
        Auth::logout();
        return view('error');
    }

    public function alreadyVoted() {
        return view('alreadyvoted');
    }

    public function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public function createPDF($voted_participants) {
        $customPaper = array(0,0,750,800.80);
        $html = '';        
        $posts = $this->posts();
        foreach ($posts as $post) {
            $html = $html.view('ebolletpaper')
                ->with('post', $post)
                ->with('voted_participants', $voted_participants)
                ->render();
        }
        $pdf = PDF::loadHTML($html)->setPaper($customPaper, 'portrait');
        Storage::put('public/pdf/ebolletpaper'.uniqid().'.pdf', $pdf->output());
    }

    public function votingNewDesign() {
        $election_posts = $this->posts();
        return view('votingnew')->with('election_posts', $election_posts);
    }

    public function sendSms($otp) {
        $msg = "Thanks for shopping with RAMPS CUBE. Your Order ".$otp." is confirmed and will be shipped shortly. Thank you";
        $api_key = 'wqQsrkPpPfo5kfY4g1g78FZlxOkJIBKsC0IE69Oe';
        $contacts = '8977425125';
        $sender_id = 'RAMSQB';
        $sms_text = urlencode($msg);
        $template_id = '1207162313011752425';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "http://textbeam.in/api/v1/send-sms?api-key=".$api_key."&sender-id=".$sender_id."&sms-type=1&route=1&mobile=".$contacts."&message=".$sms_text."&te_id=".$template_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    function generateNumericOTP($n) {
        $generator = "1357902468";
        $result = "";
        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
        return $result;
    }
}