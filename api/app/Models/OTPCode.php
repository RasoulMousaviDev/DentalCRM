<?php

namespace App\Models;

use App\Events\OTPCodeCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';

    public $fillable = ['code','expires_at','type'];

    protected $dispatchesEvents = [
        'created' => OTPCodeCreated::class,
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
}
