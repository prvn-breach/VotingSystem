<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVote extends Model
{
    protected $table = 'elec_vts';

    public $timestamps = false;

    protected $fillable = [
        'elec_participnt_id', 'asoci_vtr_id', 'vote_receipt_key'
    ];
}