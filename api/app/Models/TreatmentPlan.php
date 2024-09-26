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

    protected $fillable = ['payment_type', 'months', 'desc'];

    protected $casts = [
        'created_at' => JDate::class,
    ];

    protected $appends = ['total_cost', 'treatments_count', 'tooths_count'];

    protected $hidden = ['updated_at'];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient');
    }


    public function details(): HasMany
    {
        return $this->hasMany(TreatmentPlanDetails::class, 'plan_id');
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
