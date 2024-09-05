<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    use HasFactory;
    
    public $fillable = ['status', 'mobile',  'desc', 'log'];

    public $hidden = ['patient_id', 'updated_at'];

    protected $casts = [
        'created_at' => JDate::class,
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(CallStatus::class, 'status');
    }
}
