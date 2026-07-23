<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    public $timestamps = true;

    protected $fillable = [

        'answers',

        'profile',

        'ai_result',

        'alternative_details',
    ];

    protected $casts = [
        'answers' => 'array',
        'profile' => 'array',
        'ai_result' => 'array',
        'alternative_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
