<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoundationClass extends Model
{
    protected $fillable = ['name', 'description'];

    public function progressChecks(): HasMany
    {
        return $this->hasMany(FoundationProgress::class);
    }
}
