<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomepageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\HomepageSetting::updateOrCreate(
            ['id' => 1],
            [
                'hero_heading' => 'CENTURION CAMPAIGN',
                'hero_description' => 'Join the movement to win 100 souls per member. Let\'s saturate our world with the gospel of Jesus Christ.',
                'hero_subtext' => '100 souls per member',
                'background_image' => null,
                'welcome_modal_heading' => 'Welcome Home, {title} {name}!',
                'welcome_modal_message' => 'We are thrilled to have you lead your congregation in the Centurion Campaign. Together, we will reach our goal of 100 souls per member!',
                'show_welcome_modal' => true,
            ]
        );
    }
}
