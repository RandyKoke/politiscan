<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireSession extends Model
{
    protected $fillable = ['user_id', 'started_at', 'status'];
    public $timestamps = false;
}
