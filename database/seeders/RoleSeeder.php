<?php

namespace Database\Seeders;

use App\Enums\RoleNameEnum;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RoleNameEnum::getValues() as $value)
        {
            Role::create(['name' => $value]);
        }
    }
}
