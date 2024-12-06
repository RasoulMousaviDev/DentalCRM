<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    use HasFactory;

    public $fillable = ['months_count', 'deposit_percent', 'interest_percent', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];
}
