<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionParticipant extends Model
{
    protected $table = 'elec_participnts';

    protected $fillable = [
        'asoci_vtr_id', 'elec_post_id', 'priority_seq', 'is_active'
    ];
}