<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class AuditLog extends Model
{
    protected $fillable = [
        'audit_time', 'asoci_vtr_id', 'session_id', 'audit_details'
    ];

    public $timestamps = false;

    public function addLog($data) {
        $fields = [
            'audit_time' => Carbon::now(),
            'asoci_vtr_id' => $data['voter_id'],
            'session_id' => $data['session_id'],
            'audit_details' => $data['comments']
        ];

        $this->create($fields);
    }
}