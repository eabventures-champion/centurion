<?php

namespace App\Observers;

use App\Models\FirstTimer;
use App\Jobs\MoveToRetainedMembers;

class FirstTimerObserver
{
    /**
     * Handle the FirstTimer "updated" event.
     */
    public function updated(FirstTimer $firstTimer): void
    {
        // Locking is now handled explicitly by AttendanceController@toggle
        // Only auto-unlock if service_count drops below 3 (safety net)
        if ($firstTimer->service_count < 3 && $firstTimer->locked) {
            $firstTimer->setAttribute('locked', false);
            $firstTimer->saveQuietly();
        }
    }

    /**
     * Handle the FirstTimer "deleted" event.
     */
    public function deleted(FirstTimer $firstTimer): void
    {
        //
    }

    /**
     * Handle the FirstTimer "restored" event.
     */
    public function restored(FirstTimer $firstTimer): void
    {
        //
    }

    /**
     * Handle the FirstTimer "force deleted" event.
     */
    public function forceDeleted(FirstTimer $firstTimer): void
    {
        //
    }
}
