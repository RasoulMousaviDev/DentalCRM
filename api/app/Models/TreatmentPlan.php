<?php

namespace App\Models;

use App\Casts\JDate;
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
        'checks_count',
        'deposit_amount',
        'total_amount',
        'discount_amount',
        'start_date',
        'treatments_details',
        'desc',
        'status',
    ];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
        'start_date' => 'timestamp',
        'treatments_details' => 'object'
    ];

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
}
