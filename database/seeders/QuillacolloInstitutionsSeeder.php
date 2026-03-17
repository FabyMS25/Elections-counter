<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Locality, Institution, Municipality, District, VotingTable, VotingTableElection, ElectionType};
use Illuminate\Support\Facades\DB;

class QuillacolloInstitutionsSeeder extends Seeder
{
    protected $recintos = [
        ['locality' => 'Bella Vista', 'recinto' => 'Unidad Educativa Bella Vista', 'mesas' => 23, 'habilitados' => 5338, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Cotapachi', 'recinto' => 'U. E. Mcal. José Ballivian', 'mesas' => 14, 'habilitados' => 3237, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Cotapachi', 'recinto' => 'U. E. Milivoy Eterovic Matenda', 'mesas' => 13, 'habilitados' => 3048, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Cotapachi', 'recinto' => 'Colegio Nacional Calama (Nueva Infraestructura)', 'mesas' => 6, 'habilitados' => 1438, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Cotapachi', 'recinto' => 'U.E. Cotapachi', 'mesas' => 3, 'habilitados' => 571, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'Unidad Educativa Maria Auxiliadora', 'mesas' => 7, 'habilitados' => 1579, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'U. E. El Paso A', 'mesas' => 16, 'habilitados' => 3770, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'Unidad Educativa El Paso', 'mesas' => 17, 'habilitados' => 4006, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'Instituto Tecnologico El Paso', 'mesas' => 9, 'habilitados' => 2133, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'U.E. Molle Molle', 'mesas' => 2, 'habilitados' => 443, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'El Paso', 'recinto' => 'U.E. Santiago Apóstol', 'mesas' => 2, 'habilitados' => 465, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Illataco', 'recinto' => 'U. E. Jose Miguel Lanza (Illataco)', 'mesas' => 9, 'habilitados' => 1978, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Liriuni', 'recinto' => 'Unidad Educativa Liriuni', 'mesas' => 1, 'habilitados' => 246, 'circunscripcion' => 28, 'tipo' => 'Rural'],
        ['locality' => 'Misicuni', 'recinto' => 'U. E. Rene Barrientos Ortuño (Misicuni)', 'mesas' => 2, 'habilitados' => 396, 'circunscripcion' => 28, 'tipo' => 'Rural'],
        ['locality' => 'Misicuni', 'recinto' => 'Centro Internado Misicuni', 'mesas' => 3, 'habilitados' => 580, 'circunscripcion' => 28, 'tipo' => 'Rural'],
        ['locality' => 'Paucarpata', 'recinto' => 'Normal Simón Rodríguez (Ex Nucleo Escolar Paucarpata)', 'mesas' => 14, 'habilitados' => 3152, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Piñami', 'recinto' => 'U.E. Pocpocollo', 'mesas' => 3, 'habilitados' => 502, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Piñami', 'recinto' => 'Centro Integral Niño Jesus Fe y Alegria', 'mesas' => 9, 'habilitados' => 2048, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Piñami', 'recinto' => 'Escuela Felix Martinez', 'mesas' => 27, 'habilitados' => 6360, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Piñami', 'recinto' => 'U.E. Oscar Alfaro', 'mesas' => 4, 'habilitados' => 914, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Potrero', 'recinto' => 'U. E. Potrero', 'mesas' => 7, 'habilitados' => 1471, 'circunscripcion' => 28, 'tipo' => 'Rural'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. 1ro. de Mayo', 'mesas' => 4, 'habilitados' => 840, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => '(Cárcel) Penal San Pablo', 'mesas' => 2, 'habilitados' => 400, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Liceo América', 'mesas' => 22, 'habilitados' => 5175, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Instituto Particular Quillacollo', 'mesas' => 14, 'habilitados' => 3249, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Nestor Adriazola (Ex Colegio Nacional Calama)', 'mesas' => 29, 'habilitados' => 6976, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. San Martín de Porres Tarde', 'mesas' => 1, 'habilitados' => 250, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Villa Moderna', 'mesas' => 18, 'habilitados' => 4247, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Teofilo Vargas Candia B', 'mesas' => 21, 'habilitados' => 4985, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Escuela Simón Bolivar', 'mesas' => 15, 'habilitados' => 3501, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U. E. Nuestra Señora de Urkupiña', 'mesas' => 2, 'habilitados' => 474, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Escuela Fidelia C. De Sanchez', 'mesas' => 11, 'habilitados' => 2613, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Heroinas', 'mesas' => 13, 'habilitados' => 2910, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Colegio Cristina Prada', 'mesas' => 13, 'habilitados' => 3072, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Colegio Franz Tamayo', 'mesas' => 22, 'habilitados' => 5109, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Flora Salinas Hinojosa / Amalia Echalar', 'mesas' => 1, 'habilitados' => 166, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. Villa Asunción', 'mesas' => 4, 'habilitados' => 944, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Escuela Tomas Bata', 'mesas' => 12, 'habilitados' => 2894, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U. E. San Martín de Porres', 'mesas' => 3, 'habilitados' => 529, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. 12 de Enero B', 'mesas' => 5, 'habilitados' => 1214, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Escuela 12 de Septiembre', 'mesas' => 15, 'habilitados' => 3486, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U. E. Tunari', 'mesas' => 8, 'habilitados' => 1787, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. 23 de Marzo', 'mesas' => 3, 'habilitados' => 639, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Martin Cardenas', 'mesas' => 11, 'habilitados' => 2592, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Ironcollo', 'mesas' => 14, 'habilitados' => 3147, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. 21 de Septiembre', 'mesas' => 13, 'habilitados' => 3045, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Villa De Urkupiña', 'mesas' => 18, 'habilitados' => 4151, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. Cerro Cota', 'mesas' => 2, 'habilitados' => 466, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Marquina', 'mesas' => 20, 'habilitados' => 4695, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Unidad Educativa Marquina (Secundaria)', 'mesas' => 5, 'habilitados' => 1006, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'Escuela Arturo Quitón', 'mesas' => 18, 'habilitados' => 4278, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
        ['locality' => 'Quillacollo', 'recinto' => 'U.E. Rene Crespo Rico', 'mesas' => 3, 'habilitados' => 558, 'circunscripcion' => 28, 'tipo' => 'Urbano'],
    ];

    public function run(): void
    {
        $municipality = Municipality::where('name', 'Quillacollo')->first();
        $election = ElectionType::where('name', 'LIKE', '%Municipal%2026%')->first();
        
        if (!$municipality) {
            $this->command->error("❌ Municipio Quillacollo no encontrado");
            return;
        }
        
        if (!$election) {
            $this->command->error("❌ Elección Municipal 2026 no encontrada");
            return;
        }
        $district = District::firstOrCreate(
            ['name' => 'Circunscripción 28 - Quillacollo', 'municipality_id' => $municipality->id],
            [
                'name' => 'Circunscripción 28 - Quillacollo',
                'municipality_id' => $municipality->id
            ]
        );

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $updatedCount = 0;
            $totalMesasCreadas = 0;
            $processedRecintos = 0;
            
            foreach ($this->recintos as $index => $data) {
                $locality = Locality::firstOrCreate(
                    ['name' => $data['locality'], 'municipality_id' => $municipality->id],
                    [
                        'name' => $data['locality'],
                        'municipality_id' => $municipality->id,
                        'latitude' => null,
                        'longitude' => null
                    ]
                );
                
                $code = 'REC-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '-Q';
                $words = explode(' ', $data['recinto']);
                $shortName = count($words) > 4 ? implode(' ', array_slice($words, 0, 4)) . '...' : $data['recinto'];
                
                $existingInstitution = Institution::where('name', $data['recinto'])
                    ->where('municipality_id', $municipality->id)
                    ->first();
                
                if ($existingInstitution) {
                    $inst = $existingInstitution;
                    $inst->update([
                        'code' => $code,
                        'short_name' => $shortName,
                        'locality_id' => $locality->id,
                        'district_id' => $district->id,
                        'registered_citizens' => $data['habilitados'],
                        'total_voting_tables' => $data['mesas'],
                        'observations' => 'Tipo: ' . $data['tipo'] . ', Circunscripción: ' . $data['circunscripcion']
                    ]);
                } else {
                    $inst = Institution::create([
                        'code' => $code,
                        'name' => $data['recinto'],
                        'short_name' => $shortName,
                        'municipality_id' => $municipality->id,
                        'locality_id' => $locality->id,
                        'district_id' => $district->id,
                        'registered_citizens' => $data['habilitados'],
                        'total_voting_tables' => $data['mesas'],
                        'status' => 'activo',
                        'is_operative' => true,
                        'observations' => 'Tipo: ' . $data['tipo'] . ', Circunscripción: ' . $data['circunscripcion']
                    ]);
                }                
                $baseOepCode = 300000 + ($inst->id * 10);
                for ($i = 1; $i <= $data['mesas']; $i++) {
                    $currentVoters = $this->calculateVotersPerTable($i, $data['mesas'], $data['habilitados']);
                    $oepCode = ($baseOepCode + $i) . '-' . $i;
                    $internalCode = "INT-{$inst->id}-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $existingMesa = VotingTable::where('institution_id', $inst->id)
                                                ->where('number', $i)
                                                ->first();
                    if ($existingMesa) {
                        $existingMesa->update([
                            'oep_code' => $oepCode,
                            'internal_code' => $internalCode,
                            'expected_voters' => $currentVoters,
                            'type' => 'mixta'
                        ]);
                        $updatedCount++;
                    } else {
                        VotingTable::create([
                            'institution_id' => $inst->id,
                            'number' => $i,
                            'oep_code' => $oepCode,
                            'internal_code' => $internalCode,
                            'expected_voters' => $currentVoters,
                            'type' => 'mixta'
                        ]);
                        $createdCount++;
                    }
                    $mesa = $existingMesa ?? VotingTable::where('institution_id', $inst->id)
                        ->where('number', $i)
                        ->first();
                    if ($mesa) {
                        VotingTableElection::updateOrCreate(
                            [
                                'voting_table_id' => $mesa->id, 
                                'election_type_id' => $election->id
                            ],
                            [
                                'status' => 'configurada', 
                                'election_date' => $election->election_date ?? now()
                            ]
                        );
                    }
                    $totalMesasCreadas++;
                }
                $processedRecintos++;
            }
            
            DB::commit();
            
            $this->command->info("✅ Éxito: Se procesaron {$processedRecintos} recintos de Quillacollo");
            $this->command->info("   - {$createdCount} mesas nuevas creadas");
            $this->command->info("   - {$updatedCount} mesas existentes actualizadas");
            $this->command->info("   - Total de mesas: {$totalMesasCreadas}");
            
            // Verify totals from Excel
            $totalHabilitados = array_sum(array_column($this->recintos, 'habilitados'));
            $totalMesas = array_sum(array_column($this->recintos, 'mesas'));
            $this->command->info("   - Total habilitados según Excel: {$totalHabilitados}");
            $this->command->info("   - Total mesas según Excel: {$totalMesas}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            $this->command->error("Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
        }
    }
    
    /**
     * Calculate voters per table based on total habilitados and number of tables
     */
    private function calculateVotersPerTable($tableNumber, $totalTables, $totalHabilitados): int
    {
        // Standard distribution: most tables have ~240 voters, last table has remainder
        if ($totalTables <= 1) {
            return $totalHabilitados;
        }
        
        $standardPerTable = 240;
        $totalStandard = $standardPerTable * ($totalTables - 1);
        
        if ($tableNumber < $totalTables) {
            return $standardPerTable;
        } else {
            return $totalHabilitados - $totalStandard;
        }
    }
}