<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AssociationVoter extends Authenticatable
{
    use Notifiable;
    
    protected $table = 'asoci_voters';

    protected $fillable = [
        'asoci_vtr_name', 'asoci_vtr_mobile', 'asoci_vtr_enrol_no', 'asoci_vtr_card_no', 'remember_token'
    ];

    protected $hidden = [
        'remember_token'
    ];

    protected $primaryKey = 'asoci_vtr_id';
}