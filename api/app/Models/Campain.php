<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campain extends Model
{
    use HasFactory;

    public $fillable = ['title', 'desc', 'start_date', 'end_date', 'budget'];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];
}
