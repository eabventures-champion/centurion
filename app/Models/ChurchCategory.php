<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChurchCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'zonal_pastor_name'];

    public function churchGroups(): HasMany
    {
        return $this->hasMany(ChurchGroup::class);
    }
}
