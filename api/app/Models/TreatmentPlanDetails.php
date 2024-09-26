<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanDetails extends Model
{
    use HasFactory;

    protected $fillable = ['tooth', 'treatment', 'cost'];

    protected $casts = [
        'created_at' => JDate::class,
    ];

    protected $hidden = ['updated_at'];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class, 'treatment');
    }
}
