<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Church extends Model
{
    use SoftDeletes;
    protected $fillable = ['church_group_id', 'name', 'title', 'leader_name', 'leader_contact', 'location', 'venue', 'official_id'];

    public function churchGroup(): BelongsTo
    {
        return $this->belongsTo(ChurchGroup::class);
    }

    public function firstTimers(): HasMany
    {
        return $this->hasMany(FirstTimer::class);
    }

    public function retainedMembers(): HasMany
    {
        return $this->hasMany(RetainedMember::class);
    }

    public function pastor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_contact', 'contact');
    }
}
