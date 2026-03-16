<?php
namespace App\Imports;

use App\Models\Candidate;
use App\Models\ElectionTypeCategory;
use App\Models\Department;
use App\Models\Province;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CandidateImport
{
    private array $etcLookup    = [];
    private array $deptLookup   = [];
    private array $provLookup   = [];
    private array $munLookup    = [];
    private array $existingKeys = [];
    private array $existingListOrders = [];

    private array $toImport   = [];
    private array $errors     = [];
    private array $skipped    = [];
    private array $seenInFile = [];
    private array $seenListOrders = [];

    private int $imported = 0;

    private array $expectedHeaders = [
        'nombre', 'partido', 'nombre_completo_partido', 'color',
        'tipo_eleccion', 'codigo_categoria',
        'orden_lista', 'nombre_lista',
        'departamento', 'provincia', 'municipio',
    ];

    public function import(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }
        $headers = null;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $cleaned = array_map(
                fn($h) => trim(strtolower(str_replace("\xEF\xBB\xBF", '', $h))),
                $row
            );
            if ($cleaned === $this->expectedHeaders) {
                $headers = $cleaned;
                break;
            }
        }

        if (!$headers) {
            fclose($handle);
            return $this->result(0, [[
                'row'      => 1,
                'messages' => ['No se encontró la fila de encabezados. Use la plantilla oficial sin modificar los nombres de columna.'],
                'data'     => '',
            ]], []);
        }

        $this->buildLookups();

        $rowNumber = 1; 
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) continue;
            if (isset($row[0]) && str_starts_with(trim($row[0]), '===')) break;

            if (count($row) !== count($headers)) {
                $this->errors[] = [
                    'row'      => $rowNumber,
                    'messages' => ["Se esperaban " . count($headers) . " columnas, se encontraron " . count($row) . "."],
                    'data'     => implode(', ', $row),
                ];
                continue;
            }

            $data = array_map('trim', array_combine($headers, $row));
            $this->processRow($rowNumber, $data);
        }

        fclose($handle);
        $this->insertRows();

        return $this->result($this->imported, $this->errors, $this->skipped);
    }

    private function processRow(int $rowNumber, array $data): void
    {
        $validator = Validator::make($data, [
            'nombre'                  => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{M}\s\.\-\']+$/u'],
            'partido'                 => 'required|string|max:50',
            'nombre_completo_partido' => 'nullable|string|max:255',
            'color'                   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'tipo_eleccion'           => 'required|string|max:255',
            'codigo_categoria'        => ['required', 'string', 'regex:/^[A-Z]{2,5}$/'],
            'orden_lista'             => 'nullable|integer|min:1|max:9999',
            'nombre_lista'            => 'nullable|string|max:255',
            'departamento'            => 'nullable|string|max:100',
            'provincia'               => 'nullable|string|max:100',
            'municipio'               => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) {
            $this->errors[] = ['row' => $rowNumber, 'messages' => $validator->errors()->all(), 'data' => $data['nombre'] ?? '(sin nombre)'];
            return;
        }
        $etcKey  = strtolower($data['tipo_eleccion']) . '|' . strtoupper($data['codigo_categoria']);
        $etcMeta = $this->etcLookup[$etcKey] ?? null;
        if (!$etcMeta) {
            $this->errors[] = ['row' => $rowNumber, 'messages' => [
                "Combinación tipo_eleccion=\"{$data['tipo_eleccion']}\" + codigo_categoria=\"{$data['codigo_categoria']}\" no existe o no está activa.",
            ], 'data' => $data['nombre']];
            return;
        }
        $etcId = $etcMeta['id'];
        $scope = $etcMeta['scope'];
        [$departmentId, $provinceId, $municipalityId, $geoError] = $this->resolveGeography($data);
        if ($geoError) {
            $this->errors[] = ['row' => $rowNumber, 'messages' => [$geoError], 'data' => $data['nombre']];
            return;
        }
        $scopeError = $this->validateGeographicScope($scope, $departmentId, $provinceId, $municipalityId, $data);
        if ($scopeError) {
            $this->errors[] = ['row' => $rowNumber, 'messages' => [$scopeError], 'data' => $data['nombre']];
            return;
        }
        if ($data['orden_lista'] !== '') {
            $listKey = $etcId . '|' . strtolower($data['nombre_lista']) . '|' . $data['orden_lista'];
            if (isset($this->existingListOrders[$listKey])) {
                $this->errors[] = ['row' => $rowNumber, 'messages' => [
                    "El orden {$data['orden_lista']} en la lista \"{$data['nombre_lista']}\" ya está asignado en la base de datos.",
                ], 'data' => $data['nombre']];
                return;
            }
            if (isset($this->seenListOrders[$listKey])) {
                $this->errors[] = ['row' => $rowNumber, 'messages' => [
                    "El orden {$data['orden_lista']} en la lista \"{$data['nombre_lista']}\" ya fue asignado en la fila {$this->seenListOrders[$listKey]} de este archivo.",
                ], 'data' => $data['nombre']];
                return;
            }
            $this->seenListOrders[$listKey] = $rowNumber;
        }
        $dupKey = strtolower($data['nombre']) . '|' . strtolower($data['partido']) . '|' . $etcId;
        if (isset($this->existingKeys[$dupKey])) {
            $this->skipped[] = ['row' => $rowNumber, 'info' => "Candidato ya existe: \"{$data['nombre']}\" ({$data['partido']}) en {$data['tipo_eleccion']} / {$data['codigo_categoria']}."];
            return;
        }
        if (isset($this->seenInFile[$dupKey])) {
            $this->errors[] = ['row' => $rowNumber, 'messages' => [
                "Candidato duplicado en el archivo, ya aparece en la fila {$this->seenInFile[$dupKey]}.",
            ], 'data' => $data['nombre']];
            return;
        }
        $this->seenInFile[$dupKey] = $rowNumber;

        $this->toImport[] = [
            'name'                      => $data['nombre'],
            'party'                     => $data['partido'],
            'party_full_name'           => $data['nombre_completo_partido'] ?: null,
            'color'                     => $data['color'] ?: null,
            'election_type_category_id' => $etcId,
            'list_order'                => $data['orden_lista'] !== '' ? (int) $data['orden_lista'] : null,
            'list_name'                 => $data['nombre_lista'] ?: null,
            'department_id'             => $departmentId,
            'province_id'               => $provinceId,
            'municipality_id'           => $municipalityId,
            'active'                    => true,
        ];
    }

    private function insertRows(): void
    {
        if (empty($this->toImport)) return;

        DB::transaction(function () {
            foreach ($this->toImport as $idx => $row) {
                try {
                    Candidate::create($row);
                    $this->imported++;
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'row'      => "Lote fila " . ($idx + 1),
                        'messages' => ["Error al guardar: " . $e->getMessage()],
                        'data'     => $row['name'],
                    ];
                }
            }
        });
    }

    private function resolveGeography(array $data): array
    {
        $departmentId = $provinceId = $municipalityId = null;

        if ($data['municipio'] !== '') {
            $munData = $this->munLookup[strtolower($data['municipio'])] ?? null;
            if (!$munData) return [null, null, null, "Municipio \"{$data['municipio']}\" no encontrado."];

            $municipalityId = $munData['id'];
            $provinceId     = $munData['prov_id'];
            $departmentId   = $munData['dept_id'];

            if ($data['provincia'] !== '') {
                $ep = $this->provLookup[strtolower($data['provincia'])] ?? null;
                if ($ep && $ep['id'] != $provinceId)
                    return [null, null, null, "El municipio \"{$data['municipio']}\" no pertenece a la provincia \"{$data['provincia']}\"."];
            }
            if ($data['departamento'] !== '') {
                $ed = $this->deptLookup[strtolower($data['departamento'])] ?? null;
                if ($ed && $ed != $departmentId)
                    return [null, null, null, "El municipio \"{$data['municipio']}\" no pertenece al departamento \"{$data['departamento']}\"."];
            }
        } elseif ($data['provincia'] !== '') {
            $provData = $this->provLookup[strtolower($data['provincia'])] ?? null;
            if (!$provData) return [null, null, null, "Provincia \"{$data['provincia']}\" no encontrada."];
            $provinceId   = $provData['id'];
            $departmentId = $provData['dept_id'];
        } elseif ($data['departamento'] !== '') {
            $departmentId = $this->deptLookup[strtolower($data['departamento'])] ?? null;
            if (!$departmentId) return [null, null, null, "Departamento \"{$data['departamento']}\" no encontrado."];
        }

        return [$departmentId, $provinceId, $municipalityId, null];
    }

    private function validateGeographicScope(string $scope, ?int $deptId, ?int $provId, ?int $munId, array $data): ?string
    {
        return match ($scope) {
            'departamental'  => !$deptId ? "La categoría \"{$data['codigo_categoria']}\" es departamental. Indique el departamento." : null,
            'provincial'     => !$provId ? "La categoría \"{$data['codigo_categoria']}\" es provincial. Indique provincia." : null,
            'municipal', 'indigena_ioc' => !$munId ? "La categoría \"{$data['codigo_categoria']}\" es municipal. Indique el municipio." : null,
            'nacional'       => ($deptId || $provId || $munId) ? "La categoría \"{$data['codigo_categoria']}\" es nacional. No indique geografía." : null,
            default          => null,
        };
    }

    private function buildLookups(): void
    {
        $this->etcLookup = ElectionTypeCategory::with(['electionType', 'electionCategory'])
            ->whereHas('electionType', fn($q) => $q->where('active', true))
            ->get()
            ->mapWithKeys(fn($etc) => [
                strtolower(trim($etc->electionType?->name ?? '')) . '|' . strtoupper(trim($etc->electionCategory?->code ?? '')) => [
                    'id' => $etc->id, 'scope' => $etc->electionCategory?->geographic_scope ?? '',
                ],
            ])->toArray();

        $this->deptLookup = Department::get()
            ->mapWithKeys(fn($d) => [strtolower(trim($d->name)) => $d->id])->toArray();

        $this->provLookup = Province::get()
            ->mapWithKeys(fn($p) => [strtolower(trim($p->name)) => ['id' => $p->id, 'dept_id' => $p->department_id]])->toArray();

        $this->munLookup = Municipality::with('province')->get()
            ->mapWithKeys(fn($m) => [strtolower(trim($m->name)) => [
                'id' => $m->id, 'prov_id' => $m->province_id, 'dept_id' => $m->province?->department_id,
            ]])->toArray();

        $this->existingKeys = Candidate::where('active', true)
            ->get(['name', 'party', 'election_type_category_id'])
            ->mapWithKeys(fn($c) => [
                strtolower(trim($c->name)) . '|' . strtolower(trim($c->party)) . '|' . $c->election_type_category_id => true,
            ])->toArray();

        $this->existingListOrders = Candidate::where('active', true)
            ->whereNotNull('list_order')
            ->get(['election_type_category_id', 'list_name', 'list_order'])
            ->map(fn($c) => $c->election_type_category_id . '|' . strtolower(trim($c->list_name ?? '')) . '|' . $c->list_order)
            ->flip()->toArray();
    }

    private function result(int $imported, array $errors, array $skipped): array
    {
        return compact('imported', 'errors', 'skipped');
    }
}