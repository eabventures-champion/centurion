<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\FoundationClass;

class FoundationClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['name' => 'Class 1: New Birth', 'description' => 'Introduction to the new life in Christ.'],
            ['name' => 'Class 2: Holy Spirit', 'description' => 'Understanding the person and work of the Holy Spirit.'],
            ['name' => 'Class 3: Christian Doctrine', 'description' => 'Core beliefs and foundational truths.'],
            ['name' => 'Class 4: Evangelism', 'description' => 'Sharing your faith and winning souls.'],
            ['name' => 'Class 5: Stewardship', 'description' => 'Management of time, talents, and resources.'],
        ];

        foreach ($classes as $class) {
            FoundationClass::updateOrCreate(['name' => $class['name']], $class);
        }
    }
}
