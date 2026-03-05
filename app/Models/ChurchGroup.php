<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChurchGroup extends Model
{
    use SoftDeletes;
    protected $fillable = ['church_category_id', 'group_name', 'pastor_name', 'pastor_contact'];

    public function churchCategory(): BelongsTo
    {
        return $this->belongsTo(ChurchCategory::class);
    }

    public function churches(): HasMany
    {
        return $this->hasMany(Church::class);
    }

    public function pcfs(): HasMany
    {
        return $this->hasMany(Pcf::class);
    }
}
