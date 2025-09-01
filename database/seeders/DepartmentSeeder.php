<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'La Paz'],
            ['name' => 'Cochabamba'],
            ['name' => 'Santa Cruz'],
            ['name' => 'Oruro'],
            ['name' => 'Potosí'],
            ['name' => 'Chuquisaca'],
            ['name' => 'Tarija'],
            ['name' => 'Beni'],
            ['name' => 'Pando']
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}