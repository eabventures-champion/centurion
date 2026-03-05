<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RetainedMember extends Model
{
    protected $fillable = [
        'church_id',
        'pcf_id',
        'bringer_id',
        'full_name',
        'email',
        'primary_contact',
        'alternate_contact',
        'gender',
        'date_of_birth',
        'residential_address',
        'occupation',
        'date_of_visit',
        'marital_status',
        'born_again',
        'water_baptism',
        'prayer_requests',
        'service_count',
        'locked',
        'acknowledged',
        'retained_date'
    ];

    protected $casts = [
        'born_again' => 'boolean',
        'water_baptism' => 'boolean',
        'locked' => 'boolean',
        'acknowledged' => 'boolean',
        'date_of_visit' => 'date',
        'retained_date' => 'date',
    ];

    protected $appends = ['earliest_visit_date'];

    public function getEarliestVisitDateAttribute()
    {
        $dates = collect();
        if ($this->date_of_visit) {
            $dates->push($this->date_of_visit);
        }

        // Always query the DB directly for the global min to avoid scoped relationship issues
        $logDate = $this->attendanceLogs()->min('service_date');
        if ($logDate) {
            $dates->push($logDate);
        }

        $min = $dates->min();
        return $min ? \Carbon\Carbon::parse($min)->toDateString() : null;
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function pcf(): BelongsTo
    {
        return $this->belongsTo(Pcf::class);
    }

    public function bringer(): BelongsTo
    {
        return $this->belongsTo(Bringer::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function foundationProgress(): HasMany
    {
        return $this->hasMany(FoundationProgress::class);
    }
}
