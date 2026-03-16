<?php
namespace App\Exports;

use App\Models\Candidate;
use App\Models\ElectionTypeCategory;
use Illuminate\Database\Eloquent\Collection;

class CandidateExport
{
    public function streamCsv(\Illuminate\Support\Collection|Collection $candidates, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(
            fn() => $this->write($candidates),
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function streamTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $etcs        = ElectionTypeCategory::with(['electionType', 'electionCategory'])
            ->whereHas('electionType', fn($q) => $q->where('active', true))
            ->orderBy('ballot_order')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return response()->streamDownload(function () use ($etcs, $departments) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['=== PLANTILLA DE IMPORTACIÓN DE CANDIDATOS ===']);
            fputcsv($f, ['1. Complete sus datos debajo de los ENCABEZADOS. 2. NO modifique la fila de encabezados.']);
            fputcsv($f, ['3. Use valores EXACTOS de las hojas de referencia. 4. Deje geografía en blanco para candidatos nacionales.']);
            fputcsv($f, []);
            fputcsv($f, ['nombre','partido','nombre_completo_partido','color','tipo_eleccion','codigo_categoria','orden_lista','nombre_lista','departamento','provincia','municipio']);
            $firstEtc = $etcs->first();
            fputcsv($f, [
                'Juan Pérez González', 'PARTIDO A', 'Partido A - Nombre Completo', '#1b8af8',
                $firstEtc?->electionType?->name ?? 'Elecciones Subnacionales 2026',
                $firstEtc?->electionCategory?->code ?? 'ALC',
                '1', 'Lista Única', 'Cochabamba', 'Quillacollo', 'Quillacollo',
            ]);
            fputcsv($f, []);
            fputcsv($f, ['=== REFERENCIA: tipo_eleccion + codigo_categoria válidos ===']);
            fputcsv($f, ['tipo_eleccion','codigo_categoria','nombre_categoria','franja','votos_por_persona','ambito_geografico','nota_geografica']);
            $scopeNotes = [
                'nacional'      => 'Dejar geografía en blanco',
                'departamental' => 'Completar departamento',
                'provincial'    => 'Completar departamento + provincia',
                'municipal'     => 'Completar municipio',
                'indigena_ioc'  => 'Completar municipio',
            ];
            foreach ($etcs as $etc) {
                $scope = $etc->electionCategory?->geographic_scope ?? '';
                fputcsv($f, [$etc->electionType?->name, $etc->electionCategory?->code, $etc->electionCategory?->name,
                    $etc->ballot_order, $etc->votes_per_person ?? 1, $scope, $scopeNotes[$scope] ?? '']);
            }
            fputcsv($f, []);
            fputcsv($f, ['=== REFERENCIA: Departamentos válidos ===']);
            fputcsv($f, ['departamento (copiar exacto)']);
            foreach ($departments as $dept) fputcsv($f, [$dept->name]);
            fclose($f);
        }, 'plantilla_candidatos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function write(\Illuminate\Support\Collection|Collection $candidates): void
    {
        $f = fopen('php://output', 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($f, ['ID','Nombre','Partido','Nombre Completo Partido','Color','Tipo Elección','Categoría','Código',
            'Franja','Votos/Persona','Orden Lista','Nombre Lista','Departamento','Provincia','Municipio','Activo']);
        foreach ($candidates as $c) {
            fputcsv($f, [
                $c->id, $c->name, $c->party, $c->party_full_name ?? '', $c->color ?? '',
                $c->electionTypeCategory?->electionType?->name  ?? 'N/A',
                $c->electionTypeCategory?->electionCategory?->name ?? 'N/A',
                $c->electionTypeCategory?->electionCategory?->code ?? 'N/A',
                $c->electionTypeCategory?->ballot_order       ?? '',
                $c->electionTypeCategory?->votes_per_person   ?? 1,
                $c->list_order ?? '', $c->list_name ?? '',
                $c->department?->name ?? 'N/A', $c->province?->name ?? 'N/A', $c->municipality?->name ?? 'N/A',
                $c->active ? 'Sí' : 'No',
            ]);
        }
        fclose($f);
    }
}