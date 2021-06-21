<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVoterSession extends Model
{
    protected $table = 'elec_vts_session';

    public $timestamps = false;

    protected $fillable = [
        'asoci_vtr_id', 'session_id', 'otp', 'otp_expires_on', 'ip_address', 'latitude',
        'longitude', 'session_started_on', 'session_ended_on', 'session_auth_key', 'is_active'
    ];
}