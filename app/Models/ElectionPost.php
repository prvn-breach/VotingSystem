<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionPost extends Model
{
    protected $table = 'elec_posts';

    public function participants() {
        return $this->hasMany(ElectionParticipant::class, 'elec_post_id', 'elec_post_id')->orderBy('priority_seq', 'asc');
    }
}