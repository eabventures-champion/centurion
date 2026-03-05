<?php

namespace App\Services;

use App\Models\User;
use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\Pcf;
use App\Models\FirstTimer;
use App\Models\RetainedMember;
use App\Models\Bringer;
use Illuminate\Support\Facades\Log;

class ContactCheckService
{
    /**
     * Check for duplicate contact across all relevant models.
     *
     * @param string $contact The contact number to check
     * @param mixed $excludeId ID to exclude from search
     * @param string|null $excludeType Type of entity to exclude ('user', 'church', 'group', 'pcf', 'visitor', 'bringer')
     * @return array
     */
    public function checkDuplicate($contact, $excludeId = null, $excludeType = null)
    {
        $startTime = microtime(true);
        $contact = trim($contact);
        if (empty($contact)) {
            return ['exists' => false];
        }

        $cleanContact = preg_replace('/[^0-9]/', '', $contact);

        $logCheck = function ($name, $start) {
            $duration = round((microtime(true) - $start) * 1000, 2);
            Log::info("Contact Check: $name took {$duration}ms");
        };

        // 1. Check Users
        $userStart = microtime(true);
        $user = User::where(function ($q) use ($contact, $cleanContact) {
            $q->where('contact', $contact)->orWhere('contact', $cleanContact);
        })->when($excludeId && $excludeType === 'user', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('Users', $userStart);

        if ($user) {
            return [
                'exists' => true,
                'owner' => $user->name,
                'entity' => 'Pastor Account',
                'type' => 'user'
            ];
        }

        // 2. Check Church Groups
        $groupStart = microtime(true);
        $group = ChurchGroup::where(function ($q) use ($contact, $cleanContact) {
            $q->where('pastor_contact', $contact)->orWhere('pastor_contact', $cleanContact);
        })->when($excludeId && $excludeType === 'group', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('ChurchGroups', $groupStart);

        if ($group) {
            return [
                'exists' => true,
                'owner' => $group->pastor_name,
                'entity' => 'Church Group (' . $group->group_name . ')',
                'type' => 'group'
            ];
        }

        // 3. Check Churches
        $churchStart = microtime(true);
        $church = Church::where(function ($q) use ($contact, $cleanContact) {
            $q->where('leader_contact', $contact)->orWhere('leader_contact', $cleanContact);
        })->when($excludeId && $excludeType === 'church', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('Churches', $churchStart);

        if ($church) {
            return [
                'exists' => true,
                'owner' => $church->leader_name,
                'entity' => 'Church (' . $church->name . ')',
                'type' => 'church'
            ];
        }

        // 4. Check PCFs
        $pcfStart = microtime(true);
        $pcf = Pcf::where(function ($q) use ($contact, $cleanContact) {
            $q->where('leader_contact', $contact)->orWhere('leader_contact', $cleanContact);
        })->when($excludeId && $excludeType === 'pcf', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('PCFs', $pcfStart);

        if ($pcf) {
            return [
                'exists' => true,
                'owner' => $pcf->leader_name,
                'entity' => 'PCF (' . $pcf->name . ')',
                'type' => 'pcf'
            ];
        }

        // 5. Check First Timers
        $ftStart = microtime(true);
        $firstTimer = FirstTimer::where(function ($q) use ($contact, $cleanContact) {
            $q->where('primary_contact', $contact)
                ->orWhere('primary_contact', $cleanContact)
                ->orWhere('alternate_contact', $contact)
                ->orWhere('alternate_contact', $cleanContact);
        })->when($excludeId && ($excludeType === 'visitor' || $excludeType === 'first_timer'), function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('FirstTimers', $ftStart);

        if ($firstTimer) {
            return [
                'exists' => true,
                'owner' => $firstTimer->full_name,
                'entity' => 'First Timer',
                'type' => 'visitor'
            ];
        }

        // 6. Check Retained Members
        $rmStart = microtime(true);
        $retained = RetainedMember::where(function ($q) use ($contact, $cleanContact) {
            $q->where('primary_contact', $contact)
                ->orWhere('primary_contact', $cleanContact)
                ->orWhere('alternate_contact', $contact)
                ->orWhere('alternate_contact', $cleanContact);
        })->when($excludeId && $excludeType === 'retained', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('RetainedMembers', $rmStart);

        if ($retained) {
            return [
                'exists' => true,
                'owner' => $retained->full_name,
                'entity' => 'Retained Member',
                'type' => 'retained'
            ];
        }

        // 7. Check Bringers
        $bStart = microtime(true);
        $bringer = Bringer::where(function ($q) use ($contact, $cleanContact) {
            $q->where('contact', $contact)->orWhere('contact', $cleanContact);
        })->when($excludeId && $excludeType === 'bringer', function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })->first();
        $logCheck('Bringers', $bStart);

        if ($bringer) {
            return [
                'exists' => true,
                'owner' => $bringer->name,
                'entity' => 'Bringer',
                'type' => 'bringer'
            ];
        }

        $totalDuration = round((microtime(true) - $startTime) * 1000, 2);
        Log::info("Contact Check Total Time: {$totalDuration}ms");

        return ['exists' => false];
    }
}
