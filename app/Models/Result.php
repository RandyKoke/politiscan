<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['session_id', 'party_id', 'score'];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public $timestamps = false;
}
