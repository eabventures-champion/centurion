<?php

namespace Database\Seeders;

use App\Models\ChurchGroup;
use App\Models\Pcf;
use App\Models\User;
use Illuminate\Database\Seeder;

class PcfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $charis = ChurchGroup::where('group_name', 'CHARIS')->first();
        $achievers = ChurchGroup::where('group_name', 'ACHIEVERS')->first();

        $official = User::role('Official')->first();

        $pcfs = [
            [
                'church_group_id' => $charis->id,
                'name' => 'REVELATION',
                'leader_name' => 'Leader Rev',
                'leader_contact' => '0240001113',
                'official_id' => $official->id ?? null,
            ],
            [
                'church_group_id' => $achievers->id,
                'name' => 'GENESIS',
                'leader_name' => 'Leader Gen',
                'leader_contact' => '0240001114',
                'official_id' => $official->id ?? null,
            ],
            [
                'church_group_id' => $achievers->id,
                'name' => 'EXODUS',
                'leader_name' => 'Leader Exo',
                'leader_contact' => '0240001115',
                'official_id' => $official->id ?? null,
            ],
        ];

        foreach ($pcfs as $data) {
            Pcf::updateOrCreate(
                ['name' => $data['name']],
                [
                    'church_group_id' => $data['church_group_id'],
                    'leader_name' => $data['leader_name'],
                    'leader_contact' => $data['leader_contact'],
                    'official_id' => $data['official_id'],
                ]
            );
        }
    }
}
