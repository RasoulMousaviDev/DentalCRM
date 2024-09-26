<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PhpParser\Node\Expr\FuncCall;

class Patient extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'national_code',
        'birthday',
        'gender',
        'province',
        'city',
        'lead_source',
        'status',
        'desc'
    ];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    protected $with = [
        'mobiles',
        'city:id,title',
        'province:id,title',
        'leadSource:id,title',
        'status:id,value,severity'
    ];

    public function mobiles(): HasMany
    {
        return $this->hasMany(PatientMobile::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient', 'id');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class, 'patient', 'id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PatientStatus::class, 'status');
    }
}
