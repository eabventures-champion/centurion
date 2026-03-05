<?php

namespace App\Services;

use App\Models\FirstTimer;
use App\Models\Bringer;
use Illuminate\Support\Facades\DB;

class FirstTimerService
{
    /**
     * Register a new first timer with automated bringer logic.
     */
    public function registerFirstTimer(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data = $this->handleExtraFields($data);
            $data['service_count'] = 1; // First visit is registration day

            $firstTimer = FirstTimer::create($data);

            // Create an actual attendance log for the registration date
            \App\Models\AttendanceLog::create([
                'first_timer_id' => $firstTimer->id,
                'service_date' => $firstTimer->date_of_visit ?? now()->toDateString(),
                'marked_by' => auth()->id(),
            ]);

            return $firstTimer;
        });
    }

    /**
     * Update an existing first timer with automated bringer logic.
     */
    public function updateFirstTimer(FirstTimer $firstTimer, array $data)
    {
        return DB::transaction(function () use ($firstTimer, $data) {
            $data = $this->handleExtraFields($data, $firstTimer);
            $firstTimer->update($data);
            return $firstTimer;
        });
    }

    /**
     * Shared logic for handling bringer and birthday fields.
     */
    private function handleExtraFields(array $data, ?FirstTimer $firstTimer = null): array
    {
        $bringerId = $data['bringer_id'] ?? null;
        $contextPcfId = $data['pcf_id'] ?? null;
        $contextChurchId = $data['church_id'] ?? null;

        // Handle Bringer Logic
        if (!empty($data['bringer_id'])) {
            $bringerId = $data['bringer_id'];
            $bringer = Bringer::find($bringerId);
            if ($bringer) {
                if (
                    ($bringer->pcf_id && $bringer->pcf_id != $contextPcfId) ||
                    ($bringer->church_id && $bringer->church_id != $contextChurchId)
                ) {
                    $assignedTo = $bringer->pcf ? "PCF: {$bringer->pcf->name}" : "Church: {$bringer->church->name}";
                    throw new \Exception("The selected bringer is already assigned to {$assignedTo}.");
                }

                if (!$bringer->pcf_id && !$bringer->church_id) {
                    $bringer->update(['pcf_id' => $contextPcfId, 'church_id' => $contextChurchId]);
                }
            }
        } elseif (!empty($data['new_bringer'])) {
            $bringerContact = $data['new_bringer']['contact'];
            $existingBringer = Bringer::where('contact', $bringerContact)->first();

            if ($existingBringer) {
                if (
                    ($existingBringer->pcf_id && $existingBringer->pcf_id != $contextPcfId) ||
                    ($existingBringer->church_id && $existingBringer->church_id != $contextChurchId)
                ) {
                    $assignedTo = $existingBringer->pcf ? "PCF: {$existingBringer->pcf->name}" : "Church: {$existingBringer->church->name}";
                    throw new \Exception("This bringer (contact: {$bringerContact}) is already assigned to {$assignedTo}.");
                }

                if (!$existingBringer->pcf_id && !$existingBringer->church_id) {
                    $existingBringer->update(['pcf_id' => $contextPcfId, 'church_id' => $contextChurchId]);
                }
                $bringerId = $existingBringer->id;
            } else {
                $bringerData = array_merge($data['new_bringer'], [
                    'pcf_id' => $contextPcfId,
                    'church_id' => $contextChurchId
                ]);
                $bringer = Bringer::create($bringerData);
                $bringerId = $bringer->id;
            }
        } elseif (!empty($data['is_self_brought'])) {
            $bringer = Bringer::where('contact', $data['primary_contact'])->first();

            if ($bringer) {
                if (
                    ($bringer->pcf_id && $bringer->pcf_id != $contextPcfId) ||
                    ($bringer->church_id && $bringer->church_id != $contextChurchId)
                ) {
                    $assignedTo = $bringer->pcf ? "PCF: {$bringer->pcf->name}" : "Church: {$bringer->church->name}";
                    throw new \Exception("Self-bringer error: This person is already a bringer in {$assignedTo}.");
                }

                if (!$bringer->pcf_id && !$bringer->church_id) {
                    $bringer->update(['pcf_id' => $contextPcfId, 'church_id' => $contextChurchId]);
                }
            } else {
                $bringer = Bringer::create([
                    'pcf_id' => $contextPcfId,
                    'church_id' => $contextChurchId,
                    'contact' => $data['primary_contact'],
                    'name' => $data['full_name'],
                    'senior_cell_name' => '',
                    'cell_name' => ''
                ]);
            }
            $bringerId = $bringer->id;
        }

        if ($bringerId) {
            $data['bringer_id'] = $bringerId;
        }

        if (!empty($data['birth_day_day']) && !empty($data['birth_day_month'])) {
            $data['date_of_birth'] = $data['birth_day_day'] . '-' . $data['birth_day_month'];
        }

        // Cleanup temporary fields
        unset($data['birth_day_day'], $data['birth_day_month'], $data['new_bringer'], $data['is_self_brought']);

        // Handle Date of Visit (fallback to today if missing and new)
        if (!$firstTimer) {
            $data['date_of_visit'] = $data['date_of_visit'] ?? now()->toDateString();
        }

        return $data;
    }
}
