<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentServiceOption extends Model
{
    use HasFactory;

    public $fillable = ['title', 'cost', 'status'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    public function services(): BelongsTo
    {
        return $this->belongsTo(TreatmentService::class);
    }
}
