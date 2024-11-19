<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Status extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    public function models(): BelongsToMany
    {
        return $this->belongsToMany(Model::class, 'model_statuses');
    }
}
