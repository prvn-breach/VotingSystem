<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;
use App\Models\AssociationVoter;
use App\Models\ElectionVoterSession;
use Illuminate\Support\Facades\Auth;

class AuthSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->route('enc_vtr_card_no')) {
            $enc_vtr_card_no = $request->route('enc_vtr_card_no');
        } else if ($request['enc_vtr_card_no']) {
            $enc_vtr_card_no = $request['enc_vtr_card_no'];
        } else {
            return redirect('commonError');
        }

        // Hex Validation
        if (!ctype_xdigit($enc_vtr_card_no) || (strlen($enc_vtr_card_no) % 2!=0)) {
            return redirect('commonError');
        }

        // Decryption
        $vtr_card_no = openssl_decrypt(hex2bin($enc_vtr_card_no), config('app.cipher'), config('app.key'), OPENSSL_RAW_DATA, config('app.IV')); 
        if (!$vtr_card_no) {
            return redirect('commonError');
        }

        // Get Voter Details
        $voter_details = AssociationVoter::where('asoci_vtr_card_no', $vtr_card_no)->first();
        if (!$voter_details) {
            return redirect('commonError');
        }

        // Check If Voter has Already Voted or Not
        if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('is_active', 1)
            ->whereNotNull('session_ended_on')
            ->count() > 0 ) 
        {
            return redirect('already-voted');
        }

        if (!strpos($_SERVER['REQUEST_URI'], 'verification1')) {
            // If Session Expired
            if (!Session::has('enc_vtr_card_no')) {
                return redirect('verification1/'.$enc_vtr_card_no);
            }

            // If Session starts already on another device
            if ( ElectionVoterSession::where('asoci_vtr_id', $voter_details['asoci_vtr_id'])
            ->where('session_id', '!=', Session::getId())
            ->where('is_active', 1)
            ->count() > 0 ) 
            {
                return redirect('commonError');
            }
        }

        // // Ignore to Check Voter is inactive
        // $ignore_urls = [ 'declaration', 'verification1' ];
        // $ignore = false;
        // foreach ( $ignore_urls as $url ) {
        //     $ignore = strpos($_SERVER['REQUEST_URI'], $url)!==false;
        //     if ($ignore) { break; }
        // }

        if (!strpos($_SERVER['REQUEST_URI'], 'verification1')) {
            // If Voter is Inactive
            if (ElectionVoterSession::where([ 
                'asoci_vtr_id' => $voter_details['asoci_vtr_id'],
                'session_id' => Session::getId(),
                'is_active' => 1
            ])->count() == 0) {
                return redirect('commonError');
            }
        }

        if (strpos($_SERVER['REQUEST_URI'], 'verification1')) {
            Auth::login($voter_details, $remember=true);
        }

        return $next($request);
    }
}
