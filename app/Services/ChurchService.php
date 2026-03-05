<?php

namespace App\Services;

use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Models\Church;
use App\Models\Pcf;
use Illuminate\Support\Facades\DB;

class ChurchService
{
    public function createCategory(array $data)
    {
        return ChurchCategory::create($data);
    }

    public function createGroup(array $data)
    {
        return ChurchGroup::create($data);
    }

    public function createChurch(array $data)
    {
        // Validation check: ensure group belongs to "Other churches"
        $group = ChurchGroup::findOrFail($data['church_group_id']);
        if ($group->churchCategory->name !== 'OTHER CHURCHES') {
            throw new \Exception("Churches can only be created under 'OTHER CHURCHES' category.");
        }

        return Church::create($data);
    }

    public function createPcf(array $data)
    {
        // Validation check: ensure group belongs to "Zonal church"
        $group = ChurchGroup::findOrFail($data['church_group_id']);
        if ($group->churchCategory->name !== 'ZONAL CHURCH') {
            throw new \Exception("PCFs can only be created under 'ZONAL CHURCH' category.");
        }

        return DB::transaction(function () use ($data) {
            $pcf = Pcf::create($data);

            // Extract real first name for email, skipping common titles
            $nameParts = explode(' ', trim($data['leader_name']));
            $titles = ['pastor', 'pst', 'brother', 'bro', 'sister', 'sis', 'deacon', 'dcn', 'deaconess', 'dcns', 'elder', 'evang', 'evangelist', 'rev', 'reverend'];

            $firstName = '';
            foreach ($nameParts as $part) {
                $cleanPart = strtolower(preg_replace('/[^a-z]/i', '', $part));
                if (!in_array($cleanPart, $titles) && !empty($cleanPart)) {
                    $firstName = $cleanPart;
                    break;
                }
            }

            // Fallback to first part if no non-title name found
            if (empty($firstName)) {
                $firstName = strtolower(preg_replace('/[^a-z]/i', '', $nameParts[0]) ?: 'user');
            }

            $baseEmail = $firstName . '@church.com';
            $email = $baseEmail;
            $counter = 1;

            while (\App\Models\User::where('email', $email)->exists()) {
                $email = $firstName . $counter . '@church.com';
                $counter++;
            }

            // Auto-create a user for the PCF leader
            $user = \App\Models\User::create([
                'name' => $data['leader_name'],
                'email' => $email,
                'contact' => $data['leader_contact'],
                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                'gender' => $data['gender'],
                'marital_status' => $data['marital_status'],
                'occupation' => $data['occupation'],
            ]);

            $user->assignRole('PCF Leader');

            return $pcf;
        });
    }

    public function assignOfficialToChurch($churchId, $officialId)
    {
        $church = Church::findOrFail($churchId);
        $church->update(['official_id' => $officialId]);
        return $church;
    }

    public function assignOfficialToPcf($pcfId, $officialId)
    {
        $pcf = Pcf::findOrFail($pcfId);
        $pcf->update(['official_id' => $officialId]);
        return $pcf;
    }
}
