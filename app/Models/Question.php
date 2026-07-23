<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'question_ar',
        'question_en',
        'type',
        'branch',
        'order'
    ];


    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
