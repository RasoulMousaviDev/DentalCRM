<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use App\Models\Model as ModelsModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    public $fillable = ['amount', 'payment_date',  'refund_date', 'status'];

    protected $casts = [
        'payment_date' => JDate::class,
        'refund_date' => JDate::class,
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment');
    }

    protected function paymentDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->format('Y-m-d H:i:s'),
            get: fn($value) => Carbon::parse($value)->toIso8601String()
        );
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', Deposit::class);
    }
}
