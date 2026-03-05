<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundationProgress extends Model
{
    protected $fillable = [
        'foundation_class_id',
        'first_timer_id',
        'retained_member_id',
        'completed',
        'completed_at',
        'marked_by'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function foundationClass(): BelongsTo
    {
        return $this->belongsTo(FoundationClass::class);
    }

    public function firstTimer(): BelongsTo
    {
        return $this->belongsTo(FirstTimer::class);
    }

    public function retainedMember(): BelongsTo
    {
        return $this->belongsTo(RetainedMember::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
