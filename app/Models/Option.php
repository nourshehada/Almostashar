<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Option extends Model
{
    protected $casts = [
        'analysis' => 'integer',
        'creativity' => 'integer',
        'leadership' => 'integer',
        'communication' => 'integer',
        'research' => 'integer',
        'business' => 'integer',
        'technology' => 'integer',
        'humanitarian' => 'integer',
        'scientific' => 'integer',
        'adaptability' => 'integer',
    ];

    protected $fillable = [

        'question_id',

        'option_ar',
        'option_en',

        'analysis',
        'creativity',
        'leadership',
        'communication',
        'research',
        'business',
        'technology',
        'humanitarian',
        'scientific',
        'adaptability'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
