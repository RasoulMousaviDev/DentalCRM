<?php

namespace App\Models;

use App\Casts\JDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SMSTemplate extends Model
{
    use HasFactory;

    public $table = 'sms_templates';

    public $fillable = ['template', 'model','status', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    protected $with = ['status'];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
}
