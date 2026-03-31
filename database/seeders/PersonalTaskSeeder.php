<?php

namespace Database\Seeders;

use App\Models\PersonalTask;
use Illuminate\Database\Seeder;

class PersonalTaskSeeder extends Seeder
{
    public function run(): void
    {
        if (PersonalTask::query()->exists()) {
            return;
        }

        PersonalTask::factory(10)->create();
    }
}
