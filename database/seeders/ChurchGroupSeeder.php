<?php

namespace Database\Seeders;

use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Database\Seeder;

class ChurchGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zonal = ChurchCategory::where('name', 'ZONAL CHURCH')->first();
        $other = ChurchCategory::where('name', 'OTHER CHURCHES')->first();

        $groups = [
            [
                'church_category_id' => $zonal->id,
                'group_name' => 'CHARIS',
                'pastor_name' => 'Pst Lisa',
                'pastor_contact' => '0241234567',
            ],
            [
                'church_category_id' => $zonal->id,
                'group_name' => 'ACHIEVERS',
                'pastor_name' => 'Pst Kofi',
                'pastor_contact' => '0247654321',
            ],
            [
                'church_category_id' => $other->id,
                'group_name' => 'FAVOUR',
                'pastor_name' => 'Pst Ama',
                'pastor_contact' => '0240001112',
            ],
        ];

        foreach ($groups as $data) {
            ChurchGroup::updateOrCreate(
                ['group_name' => $data['group_name']],
                [
                    'church_category_id' => $data['church_category_id'],
                    'pastor_name' => $data['pastor_name'],
                    'pastor_contact' => $data['pastor_contact'],
                ]
            );
        }
    }
}
