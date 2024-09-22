<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurvayQuestion extends Model
{
    use HasFactory;

    public $fillable = ['title', 'order', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(SurvayQuestionAnswer::class);
    }
}
