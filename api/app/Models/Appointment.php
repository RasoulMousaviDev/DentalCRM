<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    public $fillable = ['treatment', 'due_date',  'desc', 'status'];

    protected $casts = [
        'due_date' => JDate::class,
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient');
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class, 'treatment');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class,'appointment');
    }

    protected function dueDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn($value) => Carbon::parse($value)->toIso8601String()
        );
    }
}
