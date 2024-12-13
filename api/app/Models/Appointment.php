<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use App\Models\Model as ModelsModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    public $fillable = ['due_date', 'desc', 'status', 'deposit'];

    protected $casts = [
        'due_date' => JDate::class,
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient');
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class, 'appointment_treatments');
    }

    protected function dueDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s'),
        );
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', Appointment::class);
    }
}
