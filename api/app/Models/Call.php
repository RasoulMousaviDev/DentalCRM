<?php

namespace App\Models;

use App\Casts\JDate;
use App\Models\Model as ModelsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    use HasFactory;
    
    public $fillable = ['status', 'mobile',  'desc', 'log'];

    public $hidden = ['updated_at'];

    protected $casts = [
        'created_at' => JDate::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', Call::class);
    }
}
