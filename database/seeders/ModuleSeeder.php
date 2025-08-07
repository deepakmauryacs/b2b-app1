<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'categories',
            'sub_categories',
            'products',
            'vendors',
            'buyers',
            'users',
            'roles'
        ];

        foreach ($modules as $name) {
            Module::firstOrCreate(['name' => $name]);
        }
    }
}
