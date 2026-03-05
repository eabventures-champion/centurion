<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class OfficialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officials = [
            [
                'name' => 'Test Official',
                'email' => 'official@centurion.com',
                'password' => Hash::make('password123'),
                'is_default' => true,
            ],
        ];

        foreach ($officials as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'is_default' => $data['is_default'],
                ]
            );

            if (!$user->hasRole('Official')) {
                $user->assignRole('Official');
            }
        }
    }
}

