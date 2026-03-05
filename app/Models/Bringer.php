<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bringer extends Model
{
    protected $fillable = ['church_id', 'pcf_id', 'name', 'contact', 'senior_cell_name', 'cell_name'];

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function pcf(): BelongsTo
    {
        return $this->belongsTo(Pcf::class);
    }

    public function firstTimers(): HasMany
    {
        return $this->hasMany(FirstTimer::class);
    }

    public function retainedMembers(): HasMany
    {
        return $this->hasMany(RetainedMember::class);
    }
}
