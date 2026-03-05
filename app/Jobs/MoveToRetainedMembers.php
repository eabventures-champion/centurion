<?php

namespace App\Jobs;

use App\Models\FirstTimer;
use App\Models\RetainedMember;
use App\Models\AttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MoveToRetainedMembers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $firstTimer;

    public function __construct(FirstTimer $firstTimer)
    {
        $this->firstTimer = $firstTimer;
    }

    public function handle(): void
    {
        try {
            DB::transaction(function () {
                // Ensure we only take relevant fields that exist in RetainedMember
                $fillable = (new RetainedMember())->getFillable();
                $data = collect($this->firstTimer->getAttributes())
                    ->only($fillable)
                    ->toArray();

                // Remove ID to avoid conflict
                unset($data['id']);

                // Calculate retained_date based on the latest attendance date
                $maxAttendance = $this->firstTimer->attendanceLogs()->max('service_date');
                $data['retained_date'] = $maxAttendance ?: ($this->firstTimer->date_of_visit ?: now());
                $data['locked'] = true;
                $data['acknowledged'] = false;

                // Use updateOrCreate to handle potential duplicates gracefully
                $retainedMember = RetainedMember::updateOrCreate(
                    ['primary_contact' => $data['primary_contact']],
                    $data
                );

                // Re-link attendance logs from FirstTimer to RetainedMember
                AttendanceLog::where('first_timer_id', $this->firstTimer->id)
                    ->update([
                        'retained_member_id' => $retainedMember->id,
                        'first_timer_id' => null
                    ]);

                // Force delete the first timer after successful migration
                $this->firstTimer->delete();

                \Illuminate\Support\Facades\Log::info('Migration successful for: ' . $this->firstTimer->full_name);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Migration failed for: ' . $this->firstTimer->full_name, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
