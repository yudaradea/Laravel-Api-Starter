<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            if (!$user->profile) {
                $user->profile()->create([
                    'bio' => 'This is a default bio for ' . $user->name,
                    'phone' => '081234567890',
                    'address' => 'Jl. Laragon No. 1, Localhost',
                ]);
            }
        }
    }
}
