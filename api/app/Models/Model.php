<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as ParentModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Model extends ParentModel
{
    use HasFactory;

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'model_statuses');
    }
}
