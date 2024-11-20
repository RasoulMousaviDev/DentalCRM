<?php

namespace App\Models;

use App\Casts\JDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campain extends Model
{
    use HasFactory;

    public $fillable = ['title', 'desc', 'start_date', 'end_date', 'budget'];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
        'start_date' => 'datetime:Y/m/d', 
        'end_date' => 'datetime:Y/m/d'
    ];

    protected function startDate(): Attribute
    {
        return Attribute::set(
            fn($value) => Carbon::parse($value)->setTimezone('Asia/Tehran')->startOfDay()->format('Y-m-d H:i:s'),
        );
    }

    protected function endDate(): Attribute
    {
        return Attribute::set(
            fn($value) => Carbon::parse($value)->setTimezone('Asia/Tehran')->endOfDay()->format('Y-m-d H:i:s'),
        );
    }
}
