<?php

namespace Database\Seeders;

use App\Models\ChurchCategory;
use Illuminate\Database\Seeder;

class ChurchCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'ZONAL CHURCH',
                'zonal_pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
            ],
            [
                'name' => 'OTHER CHURCHES',
                'zonal_pastor_name' => 'Highly Esteemed Pastor Lisa Ma',
            ],
        ];

        foreach ($categories as $data) {
            ChurchCategory::updateOrCreate(
                ['name' => $data['name']],
                ['zonal_pastor_name' => $data['zonal_pastor_name']]
            );
        }
    }
}
