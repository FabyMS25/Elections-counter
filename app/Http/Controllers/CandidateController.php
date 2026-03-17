<?php

namespace App\Http\Controllers;

use App\Exports\CandidateExport;
use App\Imports\CandidateImport;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\ElectionTypeCategory;
use App\Models\Municipality;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Candidate::with([
                'electionTypeCategory.electionType',
                'electionTypeCategory.electionCategory',
                'department', 'province', 'municipality',
            ])->where('candidates.active', true);
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(fn($q) => $q
                    ->where('candidates.name',             'like', "%{$s}%")
                    ->orWhere('candidates.party',          'like', "%{$s}%")
                    ->orWhere('candidates.party_full_name','like', "%{$s}%")
                    ->orWhere('candidates.list_name',      'like', "%{$s}%")
                );
            }
            if ($request->filled('election_type_category_id')) {
                $query->where('candidates.election_type_category_id', $request->election_type_category_id);
            }
            if ($request->filled('election_type_id')) {
                $query->whereHas('electionTypeCategory', fn($q) => $q->where('election_type_id', $request->election_type_id));
            }
            if ($request->filled('department_id'))   $query->where('candidates.department_id',   $request->department_id);
            if ($request->filled('province_id'))     $query->where('candidates.province_id',     $request->province_id);
            if ($request->filled('municipality_id')) $query->where('candidates.municipality_id', $request->municipality_id);
            $sort      = $request->get('sort', 'name');
            $direction = in_array($request->get('direction', 'asc'), ['asc','desc'])
                ? $request->get('direction', 'asc') : 'asc';

            match ($sort) {
                'election_type' => $query
                    ->join('election_type_categories as etc_s', 'candidates.election_type_category_id', '=', 'etc_s.id')
                    ->join('election_types as et_s', 'etc_s.election_type_id', '=', 'et_s.id')
                    ->orderBy('et_s.name', $direction)->select('candidates.*'),
                'election_category' => $query
                    ->join('election_type_categories as etc_s2', 'candidates.election_type_category_id', '=', 'etc_s2.id')
                    ->join('election_categories as ec_s', 'etc_s2.election_category_id', '=', 'ec_s.id')
                    ->orderBy('ec_s.name', $direction)->select('candidates.*'),
                default => $query->orderBy("candidates.{$sort}", $direction),
            };

            $perPage    = in_array((int)$request->get('per_page', 20), [20,50,100,200])
                ? (int)$request->get('per_page', 20) : 20;
            $candidates = $query->paginate($perPage)->withQueryString();

            $etcs           = $this->activeEtcs();
            $electionTypes  = \App\Models\ElectionType::where('active', true)->orderBy('name')->get(['id','name']);
            $departments    = Department::orderBy('name')->get();
            $provinces      = $request->filled('department_id')
                ? Province::where('department_id', $request->department_id)->orderBy('name')->get()
                : collect();
            $municipalities = $request->filled('province_id')
                ? Municipality::where('province_id', $request->province_id)->orderBy('name')->get()
                : collect();
            $stats = $this->buildStats();

        } catch (\Exception $e) {
            Log::error('CandidateController@index: ' . $e->getMessage());
            $candidates = $etcs = $electionTypes = $departments = $provinces = $municipalities = collect();
            $stats = $this->emptyStats();
            session()->flash('error', 'Error al cargar los candidatos.');
        }
        $electionTypeCategories = $etcs;
        return view('candidates.index', compact(
            'candidates', 'etcs', 'electionTypeCategories', 'electionTypes',
            'departments', 'provinces', 'municipalities', 'stats'
        ));
    }

    public function store(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson();
        try {
            $data      = $this->validateAndPrepare($request);
            $data      = $this->handleImages($request, $data);
            $candidate = Candidate::create($data);
            if ($wantsJson) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Candidato creado correctamente.',
                    'candidate' => ['id' => $candidate->id, 'name' => $candidate->name],
                ], 201);
            }
            return redirect()->route('candidates.index')->with('success', '✅ Candidato creado correctamente.');
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('CandidateController@store: ' . $e->getMessage());
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', '❌ Error al crear el candidato.');
        }
    }

    public function update(Request $request, int $id)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson();
        try {
            $candidate = Candidate::findOrFail($id);
            $data      = $this->validateAndPrepare($request);
            $data      = $this->handleImages($request, $data, $candidate);
            $candidate->update($data);
            if ($wantsJson) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Candidato actualizado correctamente.',
                    'candidate' => ['id' => $candidate->id, 'name' => $candidate->name],
                ]);
            }
            return redirect()->route('candidates.index')->with('success', '✅ Candidato actualizado correctamente.');
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('CandidateController@update: ' . $e->getMessage(), ['id' => $id]);
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', '❌ Error al actualizar el candidato.');
        }
    }

    public function destroy(Request $request, int $id)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson();
        try {
            Candidate::findOrFail($id)->update(['active' => false]);
            if ($wantsJson) {
                return response()->json(['success' => true, 'message' => 'Candidato eliminado correctamente.']);
            }
            return redirect()->route('candidates.index')->with('success', '✅ Candidato eliminado.');
        } catch (\Exception $e) {
            Log::error('CandidateController@destroy: ' . $e->getMessage());
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', '❌ Error al eliminar el candidato.');
        }
    }

    public function multipleDelete(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson();
        try {
            $ids = $request->input('ids') ?? $request->input('selected_ids') ?? [];
            $request->merge(['ids' => $ids]);
            $request->validate(['ids' => 'required|array|min:1', 'ids.*' => 'integer|exists:candidates,id']);
            $count = Candidate::whereIn('id', $ids)->update(['active' => false]);
            if ($wantsJson) {
                return response()->json(['success' => true, 'message' => "{$count} candidato(s) eliminados.", 'count' => $count]);
            }
            return redirect()->route('candidates.index')->with('success', "✅ {$count} candidato(s) eliminados.");
        } catch (ValidationException $e) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('CandidateController@multipleDelete: ' . $e->getMessage());
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', '❌ Error al eliminar candidatos.');
        }
    }

    public function getProvinces(int $departmentId)
    {
        return response()->json(
            Province::where('department_id', $departmentId)->orderBy('name')->get(['id','name'])
        );
    }

    public function getMunicipalities(int $provinceId)
    {
        return response()->json(
            Municipality::where('province_id', $provinceId)->orderBy('name')->get(['id','name'])
        );
    }

    public function import(Request $request)
    {
        try {
            $request->validate(['import_file' => 'required|file|mimes:csv,txt|max:5120']);

            $result   = (new CandidateImport)->import($request->file('import_file')->getRealPath());
            $imported = $result['imported'];
            $errors   = $result['errors'];
            $skipped  = $result['skipped'];

            return $this->buildImportResponse($imported, $errors, $skipped);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('CandidateController@import: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Error al importar: ' . $e->getMessage());
        }
    }

    public function exportAll(Request $request)
    {
        try {
            $query = $this->exportBaseQuery($request);
            return (new CandidateExport)->streamCsv(
                $query->get(),
                'candidatos_' . date('Y-m-d_His') . '.csv'
            );
        } catch (\Exception $e) {
            Log::error('CandidateController@exportAll: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Error al exportar.');
        }
    }

    public function exportSelected(Request $request)
    {
        try {
            $request->validate(['selected_ids' => 'required|array|min:1', 'selected_ids.*' => 'integer|exists:candidates,id']);
            $candidates = Candidate::with(['electionTypeCategory.electionType','electionTypeCategory.electionCategory','department','province','municipality'])
                ->whereIn('id', $request->selected_ids)->where('active', true)->get();
            if ($candidates->isEmpty()) {
                return redirect()->back()->with('error', '❌ No se encontraron candidatos seleccionados.');
            }
            return (new CandidateExport)->streamCsv($candidates, 'candidatos_seleccionados_' . date('Y-m-d_His') . '.csv');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('CandidateController@exportSelected: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Error al exportar seleccionados.');
        }
    }

    public function template()
    {
        return (new CandidateExport)->streamTemplate();
    }

    private function validateAndPrepare(Request $request): array
    {
        $v = $request->validate([
            'name'                      => 'required|string|max:255',
            'party'                     => 'required|string|max:255',
            'party_full_name'           => 'nullable|string|max:255',
            'color'                     => ['nullable','string','regex:/^#[0-9A-Fa-f]{6}$/'],
            'election_type_category_id' => 'required|exists:election_type_categories,id',
            'list_order'                => 'nullable|integer|min:1',
            'list_name'                 => 'nullable|string|max:255',
            'municipality_id'           => 'nullable|exists:municipalities,id',
            'province_id'               => 'nullable|exists:provinces,id',
            'department_id'             => 'nullable|exists:departments,id',
            'active'                    => 'boolean',
        ]);

        return [
            'name'                      => $v['name'],
            'party'                     => $v['party'],
            'party_full_name'           => $v['party_full_name'] ?? null,
            'color'                     => $v['color'] ?? null,
            'election_type_category_id' => $v['election_type_category_id'],
            'list_order'                => $v['list_order'] ?? null,
            'list_name'                 => $v['list_name'] ?? null,
            'municipality_id'           => $v['municipality_id'] ?? null,
            'province_id'               => $v['province_id'] ?? null,
            'department_id'             => $v['department_id'] ?? null,
            'active'                    => $v['active'] ?? true,
        ];
    }

    private function handleImages(Request $request, array $data, ?Candidate $existing = null): array
    {
        foreach (['photo' => 'candidates/photos', 'party_logo' => 'candidates/party-logos'] as $field => $folder) {
            if ($request->hasFile($field)) {
                if ($existing?->{$field}) Storage::disk('public')->delete($existing->{$field});
                $data[$field] = $request->file($field)->store($folder, 'public');
            }
        }
        return $data;
    }

    private function exportBaseQuery(Request $request)
    {
        $query = Candidate::with(['electionTypeCategory.electionType','electionTypeCategory.electionCategory','department','province','municipality'])
            ->where('active', true);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%{$s}%")->orWhere('party','like',"%{$s}%"));
        }
        if ($request->filled('election_type_category_id')) $query->where('election_type_category_id', $request->election_type_category_id);
        if ($request->filled('department_id'))   $query->where('department_id',   $request->department_id);
        if ($request->filled('province_id'))     $query->where('province_id',     $request->province_id);
        if ($request->filled('municipality_id')) $query->where('municipality_id', $request->municipality_id);
        return $query;
    }

    private function activeEtcs()
    {
        return ElectionTypeCategory::with(['electionType','electionCategory'])
            ->whereHas('electionType', fn($q) => $q->where('active', true))
            ->orderBy('ballot_order')->get();
    }

    private function buildImportResponse(int $imported, array $errors, array $skipped): \Illuminate\Http\RedirectResponse
    {
        $parts = [];
        if ($imported > 0)       $parts[] = "✅ {$imported} candidato(s) importados.";
        if (!empty($skipped))    $parts[] = "⏭️ " . count($skipped) . " fila(s) omitidas (duplicados).";
        if (!empty($errors))     $parts[] = "❌ " . count($errors)  . " fila(s) con errores.";

        $allNotices = array_merge(
            array_map(fn($s) => ['row' => $s['row'], 'messages' => [$s['info']], 'data' => '', 'type' => 'skip'], $skipped),
            array_map(fn($e) => array_merge($e, ['type' => 'error']), $errors)
        );
        usort($allNotices, fn($a, $b) => $a['row'] <=> $b['row']);
        $flash = array_map(fn($n) => (($n['type'] ?? 'error') === 'skip' ? '⏭️ [OMITIDA]' : '❌ [ERROR]')
            . " Fila {$n['row']}" . ($n['data'] ? " ({$n['data']})" : '') . ": " . implode(' | ', $n['messages']),
            $allNotices
        );

        $route = redirect()->route('candidates.index')->with('import_errors', $flash);
        return $imported > 0
            ? $route->with('success', implode(' ', $parts))
            : $route->with('error', implode(' ', $parts) ?: '❌ No se importó ningún candidato.');
    }

    private function buildStats(): array
    {
        try {
            $byCategory = Candidate::where('active', true)
                ->select('election_type_category_id', DB::raw('count(*) as total'))
                ->whereNotNull('election_type_category_id')
                ->groupBy('election_type_category_id')
                ->with('electionTypeCategory.electionType', 'electionTypeCategory.electionCategory')
                ->get();
            $byDepartment = Candidate::where('active', true)
                ->select('department_id', DB::raw('count(*) as total'))
                ->whereNotNull('department_id')->groupBy('department_id')->with('department')->get();
            $byElectionType = $byCategory
                ->groupBy(fn($i) => $i->electionTypeCategory?->electionType?->name ?? 'Sin tipo')
                ->map(fn($g) => $g->sum('total'));
            $geo = [
                'nacional'      => Candidate::where('active', true)->whereNull('department_id')->whereNull('province_id')->whereNull('municipality_id')->count(),
                'departamental' => Candidate::where('active', true)->whereNotNull('department_id')->whereNull('province_id')->whereNull('municipality_id')->count(),
                'provincial'    => Candidate::where('active', true)->whereNotNull('province_id')->whereNull('municipality_id')->count(),
                'municipal'     => Candidate::where('active', true)->whereNotNull('municipality_id')->count(),
            ];
            return compact('byCategory', 'byDepartment', 'byElectionType', 'geo');
        } catch (\Exception $e) {
            Log::warning('CandidateController buildStats: ' . $e->getMessage());
            return $this->emptyStats();
        }
    }

    private function emptyStats(): array
    {
        return [
            'byCategory'     => collect(), 'byDepartment'   => collect(),
            'byElectionType' => collect(), 'geo'            => ['nacional'=>0,'departamental'=>0,'provincial'=>0,'municipal'=>0],
        ];
    }

    // ── API methods (called from routes/api.php via auth:sanctum) ─────────────
    public function apiIndex(Request $request)
    {
        try {
            $query = Candidate::with([
                'electionTypeCategory.electionType',
                'electionTypeCategory.electionCategory',
                'department', 'province', 'municipality',
            ])->where('active', true);

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(fn($q) => $q
                    ->where('name',             'like', "%{$s}%")
                    ->orWhere('party',          'like', "%{$s}%")
                    ->orWhere('party_full_name', 'like', "%{$s}%")
                );
            }
            if ($request->filled('election_type_id')) {
                $query->whereHas('electionTypeCategory',
                    fn($q) => $q->where('election_type_id', $request->election_type_id)
                );
            }
            if ($request->filled('election_type_category_id')) {
                $query->where('election_type_category_id', $request->election_type_category_id);
            }
            if ($request->filled('category_code')) {
                $query->byCategoryCode($request->category_code);
            }
            if ($request->filled('department_id'))   $query->where('department_id',   $request->department_id);
            if ($request->filled('province_id'))     $query->where('province_id',     $request->province_id);
            if ($request->filled('municipality_id')) $query->where('municipality_id', $request->municipality_id);

            $perPage = min((int) $request->get('per_page', 50), 200);
            $candidates = $query->orderBy('list_order')->orderBy('name')->paginate($perPage);

            return response()->json($candidates);
        } catch (\Exception $e) {
            Log::error('CandidateController@apiIndex: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar candidatos'], 500);
        }
    }

    /**
     * GET /api/candidates/{candidate}
     * Single candidate with full relationships.
     */
    public function apiShow(Candidate $candidate)
    {
        try {
            $candidate->load([
                'electionTypeCategory.electionType',
                'electionTypeCategory.electionCategory',
                'department', 'province', 'municipality',
            ]);
            return response()->json([
                'id'                        => $candidate->id,
                'name'                      => $candidate->name,
                'party'                     => $candidate->party,
                'party_full_name'           => $candidate->party_full_name,
                'color'                     => $candidate->color,
                'list_order'                => $candidate->list_order,
                'list_name'                 => $candidate->list_name,
                'active'                    => $candidate->active,
                'photo_url'                 => $candidate->photo_url,
                'party_logo_url'            => $candidate->party_logo_url,
                'election_type'             => $candidate->electionTypeCategory?->electionType?->name,
                'election_category'         => $candidate->electionTypeCategory?->electionCategory?->name,
                'election_category_code'    => $candidate->electionTypeCategory?->electionCategory?->code,
                'ballot_order'              => $candidate->electionTypeCategory?->ballot_order,
                'votes_per_person'          => $candidate->electionTypeCategory?->votes_per_person,
                'department'                => $candidate->department?->name,
                'province'                  => $candidate->province?->name,
                'municipality'              => $candidate->municipality?->name,
            ]);
        } catch (\Exception $e) {
            Log::error('CandidateController@apiShow: ' . $e->getMessage());
            return response()->json(['error' => 'Candidato no encontrado'], 404);
        }
    }

    /**
     * GET /api/candidates/by-election/{electionTypeId}
     * All active candidates for one election, grouped by category code.
     * Use this to build the vote registration form.
     *
     * Response: { "ALC": [{...}, ...], "CON": [{...}, ...] }
     */
    public function getByElection(int $electionTypeId)
    {
        try {
            $candidates = Candidate::with([
                'electionTypeCategory.electionCategory',
            ])
            ->where('active', true)
            ->whereHas('electionTypeCategory',
                fn($q) => $q->where('election_type_id', $electionTypeId)
            )
            ->orderBy('list_order')
            ->orderBy('name')
            ->get()
            ->map(fn($c) => [
                'id'                     => $c->id,
                'name'                   => $c->name,
                'party'                  => $c->party,
                'party_full_name'        => $c->party_full_name,
                'color'                  => $c->color,
                'list_order'             => $c->list_order,
                'list_name'              => $c->list_name,
                'photo_url'              => $c->photo_url,
                'party_logo_url'         => $c->party_logo_url,
                'election_category_code' => $c->electionTypeCategory?->electionCategory?->code,
                'election_category_name' => $c->electionTypeCategory?->electionCategory?->name,
                'ballot_order'           => $c->electionTypeCategory?->ballot_order,
                'election_type_category_id' => $c->election_type_category_id,
            ])
            ->groupBy('election_category_code');

            return response()->json($candidates);
        } catch (\Exception $e) {
            Log::error('CandidateController@getByElection: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar candidatos'], 500);
        }
    }

    /**
     * POST /candidates or PUT /candidates/{id}
     * When called via API (Accept: application/json), return JSON instead of redirect.
     * The existing store() and update() already handle validation correctly —
     * this helper is used internally if you want to call them from the API.
     * The modal form submits via fetch() with Accept: application/json,
     * so store/update automatically return JSON errors on 422 (Laravel default).
     * On success we need to explicitly return JSON:
     */
    public function storeApi(Request $request)
    {
        try {
            $data = $this->validateAndPrepare($request);
            $data = $this->handleImages($request, $data);
            $candidate = Candidate::create($data);
            return response()->json([
                'success'   => true,
                'message'   => 'Candidato creado correctamente.',
                'candidate' => ['id' => $candidate->id, 'name' => $candidate->name],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('CandidateController@storeApi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateApi(Request $request, int $id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            $data      = $this->validateAndPrepare($request);
            $data      = $this->handleImages($request, $data, $candidate);
            $candidate->update($data);
            return response()->json([
                'success'   => true,
                'message'   => 'Candidato actualizado correctamente.',
                'candidate' => ['id' => $candidate->id, 'name' => $candidate->name],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('CandidateController@updateApi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}