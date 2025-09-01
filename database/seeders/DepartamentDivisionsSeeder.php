<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\District;
use App\Models\Zone;

class DepartamentDivisionsSeeder extends Seeder
{
    public function run()
    {
        $cochabamba = Department::where('name', 'Cochabamba')->first();
        
        if (!$cochabamba) {
            $cochabamba = Department::create(['name' => 'Cochabamba', 'capital' => 'Cochabamba']);
        }
        
        $provinces = [
            ['name' => 'Cercado', 'department_id' => $cochabamba->id],
            ['name' => 'Quillacollo', 'department_id' => $cochabamba->id],
            ['name' => 'Quillacollo', 'department_id' => $cochabamba->id,
                'latitude' => -17.3333, 'longitude' => -66.2500,],
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

        foreach ($provinces as $province) {
            Province::create($province);
        }

        $quillacollo = Province::where('name', 'Quillacollo')->first();        
        if ($quillacollo) {
            $municipalities = [
                [
                    'name' => 'Quillacollo',
                    'province_id' => $quillacollo->id,
                    'latitude' => -17.3983,
                    'longitude' => -66.2771,
                ],
                [
                    'name' => 'Sipe Sipe',
                    'province_id' => $quillacollo->id,
                    'latitude' => -17.4475,
                    'longitude' => -66.3438,
                ],
                [
                    'name' => 'Tiquipaya',
                    'province_id' => $quillacollo->id,
                    'latitude' => -17.3380,
                    'longitude' => -66.2158,
                ],
                [
                    'name' => 'Vinto',
                    'province_id' => $quillacollo->id,
                    'latitude' => -17.3833,
                    'longitude' => -66.3000,
                ],
                [
                    'name' => 'Colcapirhua',
                    'province_id' => $quillacollo->id,
                    'latitude' => -17.4000,
                    'longitude' => -66.2333,
                ],
            ];

            foreach ($municipalities as $municipality) {
                Municipality::create($municipality);
            }

            $quillacolloMunicipality = Municipality::where('name', 'Quillacollo')->first();
            if ($quillacolloMunicipality) {
                $districts = [
                    ['name' => 'Distrito 1 - Centro', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 2 - Norte', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 3 - Sur', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 4 - Este', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 5 - Oeste', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 6 - Periurbano Norte', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 7 - Periurbano Sur', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 8 - Periurbano Este', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 9 - Periurbano Oeste', 'municipality_id' => $quillacolloMunicipality->id],
                    ['name' => 'Distrito 10 - Rural', 'municipality_id' => $quillacolloMunicipality->id],
                ];

                foreach ($districts as $district) {
                    $createdDistrict = District::create($district);
                    
                    $zones = $this->getZonesForDistrict($createdDistrict->name);
                    foreach ($zones as $zone) {
                        Zone::create([
                            'name' => $zone,
                            'district_id' => $createdDistrict->id
                        ]);
                    }
                }
            }
        }
    }

    private function getZonesForDistrict($districtName)
    {
        $zones = [
            'Distrito 1 - Centro' => [
                'Zona Central',
                'Zona Plaza Principal',
                'Zona Mercado Campesino',
                'Zona Estación de Ferrocarril',
                'Zona San Miguel',
                'Zona Catedral'
            ],
            'Distrito 2 - Norte' => [
                'Zona Villa América',
                'Zona Villa Bolívar',
                'Zona Villa Primero de Mayo',
                'Zona Villa Juan XXIII',
                'Zona Villa 14 de Septiembre',
                'Zona Villa Nuevo Amanecer'
            ],
            'Distrito 3 - Sur' => [
                'Zona Villa España',
                'Zona Villa Obrera',
                'Zona Villa Ecología',
                'Zona Villa San Cristóbal',
                'Zona Villa Los Ángeles',
                'Zona Villa San Antonio'
            ],
            'Distrito 4 - Este' => [
                'Zona Villa Fatima',
                'Zona Villa Los Olivos',
                'Zona Villa Universitaria',
                'Zona Villa Teresa',
                'Zona Villa Esperanza',
                'Zona Villa San Pedro'
            ],
            'Distrito 5 - Oeste' => [
                'Zona Villa Copacabana',
                'Zona Villa Pagador',
                'Zona Villa Armonía',
                'Zona Villa Sebastián Pagador',
                'Zona Villa Bella Vista',
                'Zona Villa San José'
            ],
            'Distrito 6 - Periurbano Norte' => [
                'Zona Lomas de Aranjuez',
                'Zona Valle Hermoso',
                'Zona Los Pinos',
                'Zona Santa Rosa',
                'Zona El Carmen'
            ],
            'Distrito 7 - Periurbano Sur' => [
                'Zona San Isidro',
                'Zona La Tamborada',
                'Zona Los Laureles',
                'Zona Villa Tunari',
                'Zona El Rosal'
            ],
            'Distrito 8 - Periurbano Este' => [
                'Zona El Pedregal',
                'Zona Los Tusequis',
                'Zona La Florida',
                'Zona Villa Israel',
                'Zona Los Molinos'
            ],
            'Distrito 9 - Periurbano Oeste' => [
                'Zona El Mirador',
                'Zona Villa Victoria',
                'Zona Los Cactus',
                'Zona Villa Liberación',
                'Zona Las Palmas'
            ],
            'Distrito 10 - Rural' => [
                'Zona Rural Norte - Comunidades',
                'Zona Rural Sur - Comunidades',
                'Zona Rural Este - Comunidades',
                'Zona Rural Oeste - Comunidades',
                'Zona Suburbana - Asentamientos',
                'Zona Agrícola - Campos de Cultivo'
            ]
        ];

        return $zones[$districtName] ?? [];
    }
}