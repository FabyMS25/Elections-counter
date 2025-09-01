<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;
use App\Models\Department;

class MunicipalitySeeder extends Seeder
{
    public function run()
    {
        $cochabamba = Department::where('name', 'Cochabamba')->first();
        
        if (!$cochabamba) {
            $cochabamba = Department::create(['name' => 'Cochabamba', 'capital' => 'Cochabamba']);
        }
        
        $municipalities = [
            ['name' => 'Cercado', 'department_id' => $cochabamba->id],
            ['name' => 'Quillacollo', 'department_id' => $cochabamba->id],
            ['name' => 'Sacaba', 'department_id' => $cochabamba->id],
            ['name' => 'Colcapirhua', 'department_id' => $cochabamba->id],
            ['name' => 'Tiquipaya', 'department_id' => $cochabamba->id],
            ['name' => 'Vinto', 'department_id' => $cochabamba->id],
            ['name' => 'Sipe Sipe', 'department_id' => $cochabamba->id],
            ['name' => 'Tiraque', 'department_id' => $cochabamba->id],
            ['name' => 'Cliza', 'department_id' => $cochabamba->id],
            ['name' => 'Punata', 'department_id' => $cochabamba->id],
            ['name' => 'Arani', 'department_id' => $cochabamba->id],
            ['name' => 'Arque', 'department_id' => $cochabamba->id],
            ['name' => 'Capinota', 'department_id' => $cochabamba->id],
            ['name' => 'Chimore', 'department_id' => $cochabamba->id],
            ['name' => 'Colomi', 'department_id' => $cochabamba->id],
            ['name' => 'Entre Ríos', 'department_id' => $cochabamba->id],
            ['name' => 'Mizque', 'department_id' => $cochabamba->id],
            ['name' => 'Pocona', 'department_id' => $cochabamba->id],
            ['name' => 'Bolívar', 'department_id' => $cochabamba->id],
            ['name' => 'Tapacarí', 'department_id' => $cochabamba->id],
            ['name' => 'Totora', 'department_id' => $cochabamba->id],
            ['name' => 'Villa Tunari', 'department_id' => $cochabamba->id],
            ['name' => 'Morochata', 'department_id' => $cochabamba->id],
            ['name' => 'Pojo', 'department_id' => $cochabamba->id],
            ['name' => 'Pocona', 'department_id' => $cochabamba->id],
            ['name' => 'Alalay', 'department_id' => $cochabamba->id],
            ['name' => 'Pasorapa', 'department_id' => $cochabamba->id],
            ['name' => 'Omereque', 'department_id' => $cochabamba->id],
            ['name' => 'Aiquile', 'department_id' => $cochabamba->id],
            ['name' => 'Tarata', 'department_id' => $cochabamba->id],
            ['name' => 'Anzaldo', 'department_id' => $cochabamba->id],
            ['name' => 'Arbieto', 'department_id' => $cochabamba->id],
            ['name' => 'Sacabamba', 'department_id' => $cochabamba->id],
            ['name' => 'Vacas', 'department_id' => $cochabamba->id],
            ['name' => 'Tacachi', 'department_id' => $cochabamba->id],
            ['name' => 'Cuchumuela', 'department_id' => $cochabamba->id],
            ['name' => 'Villa Rivero', 'department_id' => $cochabamba->id],
            ['name' => 'San Benito', 'department_id' => $cochabamba->id],
            ['name' => 'Tacopaya', 'department_id' => $cochabamba->id],
            ['name' => 'Independencia', 'department_id' => $cochabamba->id],
            ['name' => 'Yaquiba', 'department_id' => $cochabamba->id],
            ['name' => 'Charamoco', 'department_id' => $cochabamba->id],
            ['name' => 'Chuquiago', 'department_id' => $cochabamba->id],
            ['name' => 'Huayculi', 'department_id' => $cochabamba->id],
            ['name' => 'Irpa Irpa', 'department_id' => $cochabamba->id],
            ['name' => 'Ivirgarzama', 'department_id' => $cochabamba->id],
            ['name' => 'Mairana', 'department_id' => $cochabamba->id],
            ['name' => 'Puerto Villarroel', 'department_id' => $cochabamba->id],
            ['name' => 'Shinahota', 'department_id' => $cochabamba->id],
            ['name' => 'Villa Gualberto Villarroel', 'department_id' => $cochabamba->id],
        ];

        foreach ($municipalities as $municipality) {
            Municipality::create($municipality);
        }
    }
}