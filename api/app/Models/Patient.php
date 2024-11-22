<?php

namespace App\Models;

use App\Casts\JDate;
use App\Models\Model as ModelsModel;
use App\Events\PatientUpdated;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Patient extends Model
{
    use HasFactory;

    public $fillable = [
        'firstname',
        'lastname',
        'birthday',
        'telephone',
        'gender',
        'province',
        'city',
        'telephone',
        'lead_source',
        'status',
        'desc'
    ];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    protected $dispatchesEvents = [
        'updated' => PatientUpdated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function mobiles(): HasMany
    {
        return $this->hasMany(PatientMobile::class, 'patient');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class, 'patient');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'patient');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient');
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class, 'patient_treatments');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class, 'patient');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'patient');
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
        return $this->belongsTo(Status::class, 'status');
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', Patient::class);
    }

    public function birthday(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: fn($value) => date('Y/m/d', strtotime($value))
        );
    }
}
