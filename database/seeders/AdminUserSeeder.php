<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Angel Rojas',
            'email' => 'angelitp.arp.0704@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
