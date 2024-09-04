<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use HasFactory;

    public $fillable = ['due_date', 'desc', 'status'];

    public $hidden = ['patient_id'];

    protected $casts = [
        'due_date' => JDate::class,
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    protected function dueDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn($value) => Carbon::parse($value)->toIso8601String()
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value == 'done' ? $value : (Carbon::now()->diffInMinutes(Carbon::parse($this->due_date)) > 5 ? $value : 'missed')
        );
    }
}
