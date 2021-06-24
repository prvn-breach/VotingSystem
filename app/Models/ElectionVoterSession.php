<?php

namespace App\Models;

use Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ElectionVoterSession extends Model
{
    protected $table = 'elec_vts_session';

    protected $fillable = [
        'asoci_vtr_id', 'session_id', 'otp', 'otp_expires_on', 'ip_address', 'user_agent_data', 'latitude',
        'longitude', 'session_started_on', 'session_ended_on', 'session_auth_key', 'is_active'
    ];


    public function getCurrentVoterSession() {
        return $this->where([ 
            'asoci_vtr_id' => Auth::user()->asoci_vtr_id,
            'session_id' => Session::getId(),
            'is_active' => 1
        ])->first();
    }
}