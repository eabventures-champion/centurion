<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pcf extends Model
{
    use SoftDeletes;
    protected $fillable = ['church_group_id', 'name', 'leader_name', 'leader_contact', 'official_id', 'gender', 'marital_status', 'occupation'];

    public function churchGroup(): BelongsTo
    {
        return $this->belongsTo(ChurchGroup::class);
    }

    public function official(): BelongsTo
    {
        return $this->belongsTo(User::class, 'official_id');
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
