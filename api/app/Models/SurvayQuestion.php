<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurvayQuestion extends Model
{
    use HasFactory;


    public function answers(): HasMany
    {
        return $this->hasMany(SurvayQuestionAnswer::class);
    }
}
