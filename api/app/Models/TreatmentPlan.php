<?php

namespace App\Models;

use App\Casts\JDate;
use App\Models\Model as ModelsModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method',
        'months_count',
        'deposit_amount',
        'total_amount',
        'discount_amount',
        'start_date',
        'treatments_details',
        'visit_type',
        'desc',
        'status',
    ];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
        'start_date' => 'timestamp',
        'treatments_details' => 'object'
    ];

    protected function startDate(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Carbon::parse($value)->setTimezone('Asia/Tehran')->format('Y-m-d H:i:s'),
            get: fn($value) => Carbon::parse($value)->toIso8601String()
        );
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient');
    }


    public function getTotalCostAttribute()
    {
        return $this->details()->sum('cost') ?: 0;
    }

    public function getTreatmentsCountAttribute()
    {
        return $this->details()->count();
    }

    public function getToothsCountAttribute()
    {
        return $this->details()->distinct('tooth')->count();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', TreatmentPlan::class);
    }
}
