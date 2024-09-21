<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survay extends Model
{
    use HasFactory;

    public $fillable = ['title', 'desc', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SurvayQuestion::class);
    }
}
