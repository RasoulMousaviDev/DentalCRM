<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientMobile extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['number'];

    public $hidden = ['patient_id'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

}
