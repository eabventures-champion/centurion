<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = ['first_timer_id', 'retained_member_id', 'service_date', 'marked_by'];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function firstTimer(): BelongsTo
    {
        return $this->belongsTo(FirstTimer::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function retainedMember(): BelongsTo
    {
        return $this->belongsTo(RetainedMember::class);
    }
}
