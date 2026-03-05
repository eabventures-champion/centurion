<?php

namespace Database\Seeders;

use App\Models\Church;
use App\Models\ChurchGroup;
use Illuminate\Database\Seeder;

class ChurchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $charis = ChurchGroup::where('group_name', 'CHARIS')->first();

        $churches = [
            [
                'church_group_id' => $charis->id,
                'name' => 'CE DOME',
                'leader_name' => 'Kofi Affum',
                'leader_contact' => '0246868672',
                'location' => 'DOME location',
                'venue' => 'DOME venue',
            ],
            [
                'church_group_id' => $charis->id,
                'name' => 'CE NORTH KANESHIE',
                'leader_name' => 'Emmanuel Arthur',
                'leader_contact' => '0545282656',
                'location' => 'Kaneshie location',
                'venue' => 'Kaneshie venue',
            ],
            [
                'church_group_id' => $charis->id,
                'name' => 'CE COMMUNITY 18 TEMA',
                'leader_name' => 'Philomena Edusei',
                'leader_contact' => '0244539889',
                'location' => 'Tema location',
                'venue' => 'Tema venue',
            ],
        ];

        foreach ($churches as $data) {
            Church::updateOrCreate(
                ['name' => $data['name']],
                [
                    'church_group_id' => $data['church_group_id'],
                    'leader_name' => $data['leader_name'],
                    'leader_contact' => $data['leader_contact'],
                    'location' => $data['location'],
                    'venue' => $data['venue'],
                ]
            );
        }
    }
}
