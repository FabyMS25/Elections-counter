<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\Locality;
use App\Models\Institution;
use App\Models\VotingTable;
use App\Models\VotingTableCategoryResult;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Dashboard;
use App\Models\ElectionType;
use App\Models\ElectionTypeCategory;
use App\Models\VotingTableElection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function root(Request $request)
    {
        $dashboard = Dashboard::first();
        if (!$dashboard?->is_public && !Auth::check()) {
            return redirect()->route('login');
        }
        $data = $this->buildDashboardData($request, $dashboard);
        return Auth::check() ? view('index', $data) : view('landing', $data);
    }

    public function index(Request $request)
    {
        $dashboard = Dashboard::first();
        if (!$dashboard?->is_public && !Auth::check()) {
            return redirect()->route('login');
        }
        $data = $this->buildDashboardData($request, $dashboard);
        if (Auth::check()) {
            if (view()->exists($request->path())) {
                return view($request->path(), $data);
            }
            return abort(404);
        }
        return view('landing', $data);
    }

    public function toggleDashboardVisibility(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }
        if (!Auth::user()->hasPermission('manage_settings')) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
        }
        $dashboard            = Dashboard::first();
        $dashboard->is_public = !$dashboard->is_public;
        $dashboard->save();
        return response()->json([
            'success'   => true,
            'is_public' => $dashboard->is_public,
            'message'   => 'Estado del dashboard actualizado correctamente.',
        ]);
    }

    public function refreshDashboard(Request $request)
    {
        $dashboard = Dashboard::first();
        if (!$dashboard?->is_public && !Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $data = $this->buildDashboardData($request, $dashboard);
        return response()->json([
            'success'            => true,
            'last_updated'       => now()->format('d/m/Y H:i:s'),
            'totalVotes'         => $data['totalVotes'],
            'reportedTables'     => $data['reportedTables'],
            'totalTables'        => $data['totalTables'],
            'progressPercentage' => $data['progressPercentage'],
            'totalBlankVotes'    => $data['totalBlankVotes'],
            'totalNullVotes'     => $data['totalNullVotes'],
            'candidateStats'     => $data['candidateStats'],
            'categoryStats'      => $data['categoryStats'],   // needed to refresh charts
            'activeCategoryCode' => $data['activeCategoryCode'],
        ]);
    }

    public function getDashboardData(Request $request)
    {
        $dashboard = Dashboard::first();
        $data      = $this->buildDashboardData($request, $dashboard);
        return response()->json(array_merge(
            ['success' => true, 'last_updated' => now()->toDateTimeString()],
            $data
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);
        $user->name  = $request->name;
        $user->email = $request->email;
        if ($request->hasFile('avatar')) {
            $path         = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = basename($path);
        }
        $user->save();
        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Su contraseña actual no coincide.');
        }
        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function getProvinces($departmentId)
    {
        return response()->json(Province::where('department_id', $departmentId)->get());
    }

    public function getMunicipalities($provinceId)
    {
        return response()->json(Municipality::where('province_id', $provinceId)->get());
    }

    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        }
        return redirect()->back();
    }

    private function buildDashboardData(Request $request, ?Dashboard $dashboard): array
    {
        $electionTypes       = ElectionType::where('active', true)->get();
        $departments         = Department::all();
        $defaultElectionType = $dashboard?->defaultElectionType ?? $electionTypes->first();

        $defaultDeptId = $dashboard?->default_department_id
            ?? $departments->first()?->id;
        $defaultProvId = $dashboard?->default_province_id
            ?? ($defaultDeptId ? Province::where('department_id', $defaultDeptId)->value('id') : null);
        $defaultMuniId = $dashboard?->default_municipality_id
            ?? ($defaultProvId ? Municipality::where('province_id', $defaultProvId)->value('id') : null);

        $electionTypeId = $request->get('election_type', $defaultElectionType?->id);
        $departmentId   = (int) $request->get('department',   $defaultDeptId);
        $provinceId     = (int) $request->get('province',     $defaultProvId);
        $municipalityId = (int) $request->get('municipality', $defaultMuniId);

        if (!$municipalityId) {
            return $this->emptyData(
                $dashboard, $electionTypes, $departments,
                Province::where('department_id', $departmentId)->get(),
                collect(),
                $departmentId, $provinceId, null
            );
        }

        $departmentId   = (int) $departmentId;
        $provinceId     = (int) $provinceId;
        $municipalityId = (int) $municipalityId;
        $provinces      = Province::where('department_id', $departmentId)->get();
        $municipalities = Municipality::where('province_id', $provinceId)->get();

        $selectedElectionType = ElectionType::find($electionTypeId);
        if (!$selectedElectionType) {
            return $this->emptyData(
                $dashboard, $electionTypes, $departments,
                $provinces, $municipalities,
                $departmentId, $provinceId, $municipalityId
            );
        }
        $allTableIds = VotingTable::whereHas('institution', function ($q) use ($municipalityId) {
            $q->whereHas('locality', fn($q2) => $q2->where('municipality_id', $municipalityId));
        })->pluck('id');
        $totalTables = $allTableIds->count();
        $reportedTables = \App\Models\VotingTableElection::whereIn('voting_table_id', $allTableIds)
            ->where('election_type_id', $selectedElectionType->id)
            ->whereIn('status', [
                \App\Models\VotingTableElection::STATUS_ESCRUTADA,
                \App\Models\VotingTableElection::STATUS_TRANSMITIDA,
            ])
            ->count();
        $progressPercentage = $totalTables > 0
            ? round(($reportedTables / $totalTables) * 100, 2)
            : 0;
        $typeCategories = ElectionTypeCategory::where('election_type_id', $selectedElectionType->id)
            ->with('electionCategory')
            ->orderBy('ballot_order')
            ->get();
        $categoryStats   = [];
        $totalBlankVotes = 0;
        $totalNullVotes  = 0;
        foreach ($typeCategories as $tc) {
            $cat  = $tc->electionCategory;
            $code = $cat->code;
            $tcId = $tc->id;
            $candidates = Candidate::where('election_type_category_id', $tcId)
                ->where('active', true)
                ->orderBy('list_order')
                ->get();
            $votes = Vote::select('candidate_id', DB::raw('SUM(quantity) as total_votes'))
                ->where('election_type_id', $selectedElectionType->id)
                ->where('election_type_category_id', $tcId)
                ->whereIn('voting_table_id', $allTableIds)
                ->groupBy('candidate_id')
                ->with('candidate')
                ->orderByDesc('total_votes')
                ->get();
            $totalValidVotes = (int) $votes->sum('total_votes');
            $specialVotes = VotingTableCategoryResult::where('election_type_category_id', $tcId)
                ->whereIn('voting_table_id', $allTableIds)
                ->selectRaw('COALESCE(SUM(blank_votes), 0) as blank, COALESCE(SUM(null_votes), 0) as null_v')
                ->first();
            $catBlank = (int) ($specialVotes->blank  ?? 0);
            $catNull  = (int) ($specialVotes->null_v ?? 0);
            $catTotal = $totalValidVotes + $catBlank + $catNull;
            $totalBlankVotes += $catBlank;
            $totalNullVotes  += $catNull;
            $categoryStats[$code] = [
                'category'       => $cat,
                'typeCategoryId' => $tcId,
                'candidates'     => $candidates,
                'stats'          => $this->calculateStats($votes, $catTotal),
                'totalVotes'     => $totalValidVotes,
                'totalBallots'   => $catTotal,
                'blankVotes'     => $catBlank,
                'nullVotes'      => $catNull,
            ];
        }
        $defaultCategoryCode = $dashboard?->defaultCategory?->code ?? array_key_first($categoryStats);
        $activeCategoryCode  = $request->get('category', $defaultCategoryCode);
        if (!isset($categoryStats[$activeCategoryCode])) {
            $activeCategoryCode = array_key_first($categoryStats);
        }
        $totalVotes      = $categoryStats[$activeCategoryCode]['totalBallots'] ?? 0;
        $localityResults = $this->getLocalityResults($selectedElectionType->id, $municipalityId, $typeCategories);
        $localityStats   = $this->getLocalityStats($municipalityId, $selectedElectionType->id);
        $conCategoryId = null;
        foreach ($typeCategories as $tc) {
            if ($tc->electionCategory && $tc->electionCategory->code === 'CON') {
                $conCategoryId = $tc->id;
                break;
            }
        }
        $concejalSeatsValidated = ['seats' => [], 'analysis' => [], 'cutoff' => 0];
        $concejalSeatsAll = ['seats' => [], 'analysis' => [], 'cutoff' => 0];
        $concejalSeatChanges = [];
        $institutionProgress = [];
        if ($conCategoryId) {
            $validatedTableIds = DB::table('voting_table_elections')
                ->whereIn('voting_table_id', $allTableIds)
                ->where('election_type_id', $selectedElectionType->id)
                ->whereIn('status', [
                    \App\Models\VotingTableElection::STATUS_ESCRUTADA,
                    \App\Models\VotingTableElection::STATUS_TRANSMITIDA,
                ])
                ->pluck('voting_table_id')
                ->toArray();
            $allWithVotesTableIds = Vote::whereIn('voting_table_id', $allTableIds)
                ->where('election_type_id', $selectedElectionType->id)
                ->distinct()
                ->pluck('voting_table_id')
                ->toArray();
            $votesValidated = $this->getConcejalVotesByParty($validatedTableIds, $selectedElectionType->id, $conCategoryId);
            $votesAll = $this->getConcejalVotesByParty($allWithVotesTableIds, $selectedElectionType->id, $conCategoryId);
            $concejalSeatsValidated = $this->calculateConcejalSeats($votesValidated, 11);
            $concejalSeatsAll = $this->calculateConcejalSeats($votesAll, 11);
            $concejalSeatChanges = $this->calculateSeatChanges($concejalSeatsValidated, $concejalSeatsAll);
            $institutionProgress = $this->getInstitutionProgress($municipalityId, $selectedElectionType->id);
        }
        $currentSeatMode = $request->get('seat_mode', 'all');
        return [
            'dashboard'            => $dashboard,
            'electionTypes'        => $electionTypes,
            'departments'          => $departments,
            'provinces'            => $provinces,
            'municipalities'       => $municipalities,
            'selectedDepartment'   => $departmentId,
            'selectedProvince'     => $provinceId,
            'selectedMunicipality' => $municipalityId,
            'selectedElectionType' => $selectedElectionType,
            'typeCategories'       => $typeCategories,
            'categoryStats'        => $categoryStats,
            'activeCategoryCode'   => $activeCategoryCode,
            'totalTables'          => $totalTables,
            'reportedTables'       => $reportedTables,
            'progressPercentage'   => $progressPercentage,
            'localityResults'      => $localityResults,
            'localityStats'        => $localityStats,
            'alcaldeCandidates'    => $categoryStats['ALC']['candidates']  ?? collect(),
            'alcaldeStats'         => $categoryStats['ALC']['stats']        ?? [],
            'concejalCandidates'   => $categoryStats['CON']['candidates']  ?? collect(),
            'concejalStats'        => $categoryStats['CON']['stats']        ?? [],
            'totalVotesAlcalde'    => $categoryStats['ALC']['totalBallots'] ?? 0,
            'totalVotesConcejal'   => $categoryStats['CON']['totalBallots'] ?? 0,
            'totalVotes'           => $totalVotes,
            'candidateStats'       => $categoryStats[$activeCategoryCode]['stats']      ?? [],
            'candidates'           => $categoryStats[$activeCategoryCode]['candidates'] ?? collect(),
            'totalBlankVotes'      => $totalBlankVotes,
            'totalNullVotes'       => $totalNullVotes,
            'concejalSeatsValidated' => $concejalSeatsValidated,
            'concejalSeatsAll'       => $concejalSeatsAll,
            'concejalSeatChanges'    => $concejalSeatChanges,
            'institutionProgress'    => $institutionProgress,
            'currentSeatMode'        => $currentSeatMode,
        ];
    }

    private function getConcejalVotesByParty($tableIds, $electionTypeId, $tcId): array
    {
        if (empty($tableIds)) {
            return [];
        }
        $votes = Vote::select('candidate_id', DB::raw('SUM(quantity) as total_votes'))
            ->where('election_type_id', $electionTypeId)
            ->where('election_type_category_id', $tcId)
            ->whereIn('voting_table_id', $tableIds)
            ->groupBy('candidate_id')
            ->with('candidate')
            ->get();
        $votesByParty = [];
        foreach ($votes as $vote) {
            if ($vote->candidate && $vote->candidate->party) {
                $party = $vote->candidate->party;
                $votesByParty[$party] = ($votesByParty[$party] ?? 0) + $vote->total_votes;
            }
        }
        return $votesByParty;
    }

    private function calculateConcejalSeats(array $votesByParty, int $totalSeats = 11): array
    {
        $quotients = [];
        foreach ($votesByParty as $party => $votes) {
            if ($votes <= 0) continue;
            for ($i = 1; $i <= $totalSeats; $i++) {
                $quotients[] = [
                    'party' => $party,
                    'value' => $votes / $i,
                    'divisor' => $i,
                    'votes' => $votes,
                ];
            }
        }
        if (empty($quotients)) {
            return [
                'seats' => [],
                'analysis' => [],
                'cutoff' => 0,
            ];
        }
        usort($quotients, function($a, $b) {
            return $b['value'] <=> $a['value'];
        });
        $topQuotients = array_slice($quotients, 0, min($totalSeats, count($quotients)));
        $seats = [];
        foreach ($topQuotients as $q) {
            $seats[$q['party']] = ($seats[$q['party']] ?? 0) + 1;
        }
        $cutoffIndex = min($totalSeats - 1, count($quotients) - 1);
        $cutoff = $cutoffIndex >= 0 ? $quotients[$cutoffIndex]['value'] : 0;
        $analysis = [];
        foreach ($votesByParty as $party => $votes) {
            $currentSeats = $seats[$party] ?? 0;
            $nextDivisor = $currentSeats + 1;
            $nextQuotient = $votes / $nextDivisor;
            $neededVotes = $cutoff > 0 ? max(0, ceil(($cutoff * $nextDivisor) - $votes)) : 0;
            $analysis[$party] = [
                'votes' => $votes,
                'seats' => $currentSeats,
                'next_quotient' => round($nextQuotient, 2),
                'votes_needed_for_next_seat' => $neededVotes,
                'is_close' => $cutoff > 0 ? ($nextQuotient >= $cutoff * 0.9) : false,
                'competing_for_last_seat' => $cutoff > 0 ? (abs($nextQuotient - $cutoff) < 0.01) : false,
            ];
        }
        uasort($analysis, function($a, $b) {
            if ($a['seats'] != $b['seats']) {
                return $b['seats'] <=> $a['seats'];
            }
            return $b['votes'] <=> $a['votes'];
        });
        return [
            'seats' => $seats,
            'analysis' => $analysis,
            'cutoff' => $cutoff,
        ];
    }

    private function calculateSeatChanges(array $validatedSeats, array $allSeats): array
    {
        $allParties = array_unique(array_merge(
            array_keys($validatedSeats['analysis'] ?? []),
            array_keys($allSeats['analysis'] ?? [])
        ));
        $changes = [];
        foreach ($allParties as $party) {
            $validatedSeatCount = $validatedSeats['seats'][$party] ?? 0;
            $allSeatCount = $allSeats['seats'][$party] ?? 0;
            $delta = $allSeatCount - $validatedSeatCount;
            $changes[$party] = [
                'validated' => $validatedSeatCount,
                'all' => $allSeatCount,
                'delta' => $delta,
                'trend' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'same'),
            ];
        }
        return $changes;
    }

    private function getInstitutionProgress($municipalityId, $electionTypeId)
    {
        $institutions = Institution::whereHas('locality', function($q) use ($municipalityId) {
            $q->where('municipality_id', $municipalityId);
        })->with(['locality', 'district'])->get();
        $progress = [];
        foreach ($institutions as $inst) {
            $tableIds = VotingTable::where('institution_id', $inst->id)->pluck('id');
            $totalTables = $tableIds->count();
            if ($totalTables === 0) {
                continue;
            }
            $reportedTables = Vote::whereIn('voting_table_id', $tableIds)
                ->where('election_type_id', $electionTypeId)
                ->distinct('voting_table_id')
                ->count('voting_table_id');
            $validatedTables = DB::table('voting_table_elections')
                ->whereIn('voting_table_id', $tableIds)
                ->where('election_type_id', $electionTypeId)
                ->whereIn('status', [
                    VotingTableElection::STATUS_ESCRUTADA,
                    VotingTableElection::STATUS_TRANSMITIDA,
                ])
                ->count();
            $pendingTables = $totalTables - $reportedTables;
            $progressPercent = $totalTables > 0 ? round(($reportedTables / $totalTables) * 100, 1) : 0;
            $progress[] = [
                'id' => $inst->id,
                'name' => $inst->name,
                'locality' => $inst->locality->name ?? '',
                'total_tables' => $totalTables,
                'reported_tables' => $reportedTables,
                'validated_tables' => $validatedTables,
                'pending_tables' => $pendingTables,
                'progress' => $progressPercent,
                'district' => $inst->district?->name ?? 'Sin Distrito',
            ];
        }
        usort($progress, function($a, $b) {
            if ($a['district'] != $b['district']) {
                return $a['district'] <=> $b['district'];
            }
            if ($a['locality'] != $b['locality']) {
                return $a['locality'] <=> $b['locality'];
            }
            return $a['name'] <=> $b['name'];
        });
        
        return $progress;
    }

    private function calculateStats($votes, int $totalVotes): array
    {
        $stats = [];
        $rank  = 1;
        foreach ($votes as $vote) {
            $pct = $totalVotes > 0 ? ($vote->total_votes / $totalVotes) * 100 : 0;
            $stats[$vote->candidate_id] = [
                'votes'      => (int) $vote->total_votes,
                'percentage' => round($pct, 1),
                'rank'       => $rank++,
                'candidate'  => $vote->candidate, // This includes party_logo
            ];
        }
        uasort($stats, fn($a, $b) => $b['votes'] - $a['votes']);
        return $stats;
    }

    private function getLocalityResults(int $electionTypeId, int $municipalityId, $typeCategories): array
    {
        $localities = Locality::where('municipality_id', $municipalityId)->get();
        $results    = [];
        foreach ($localities as $locality) {
            $tableIds = VotingTable::whereHas('institution', fn($q) =>
                $q->where('locality_id', $locality->id)
            )->pluck('id');
            $specialVotes = VotingTableCategoryResult::whereIn('voting_table_id', $tableIds)
                ->selectRaw('COALESCE(SUM(blank_votes), 0) as blank, COALESCE(SUM(null_votes), 0) as null_v')
                ->first();
            $results[$locality->id] = [
                'name'                 => $locality->name,
                'latitude'             => $locality->latitude,
                'longitude'            => $locality->longitude,
                'total_votes'          => 0,
                'blank_votes'          => (int) ($specialVotes->blank  ?? 0),
                'null_votes'           => (int) ($specialVotes->null_v ?? 0),
                'categories'           => [],
                'total_votes_alcalde'  => 0,
                'total_votes_concejal' => 0,
                'alcalde'              => [],
                'concejal'             => [],
            ];
            foreach ($typeCategories as $tc) {
                $code    = $tc->electionCategory?->code ?? 'UNK';
                $catName = $tc->electionCategory?->name ?? $code;
                $votes = Vote::whereIn('voting_table_id', $tableIds)
                    ->where('election_type_id', $electionTypeId)
                    ->where('election_type_category_id', $tc->id)
                    ->select('candidate_id', DB::raw('SUM(quantity) as total'))
                    ->groupBy('candidate_id')
                    ->with('candidate')
                    ->orderByDesc(DB::raw('SUM(quantity)'))
                    ->get();
                $catTotal = (int) $votes->sum('total');
                $results[$locality->id]['total_votes'] += $catTotal;
                $candidateList = $votes->map(fn($v) => [
                    'id'             => $v->candidate_id,
                    'candidate_name' => $v->candidate?->name ?? '—',
                    'name'           => $v->candidate?->name ?? '—',
                    'party'          => $v->candidate?->party ?? '—',
                    'color'          => $v->candidate?->color ?? '#888',
                    'party_logo'     => $v->candidate?->party_logo,
                    'votes'          => (int) $v->total,
                    'percentage'     => $catTotal > 0 ? round(($v->total / $catTotal) * 100, 1) : 0,
                ])->values()->toArray();
                $results[$locality->id]['categories'][$code] = [
                    'label'       => $catName,
                    'total_votes' => $catTotal,
                    'candidates'  => $candidateList,
                ];
                if ($code === 'ALC') {
                    $results[$locality->id]['total_votes_alcalde'] = $catTotal;
                    $results[$locality->id]['alcalde']             = $candidateList;
                }
                if ($code === 'CON') {
                    $results[$locality->id]['total_votes_concejal'] = $catTotal;
                    $results[$locality->id]['concejal']             = $candidateList;
                }
            }
        }
        return $results;
    }

    private function getLocalityStats(int $municipalityId, int $electionTypeId = 0)
    {
        return Locality::where('municipality_id', $municipalityId)
            ->withCount([
                'institutions as total_institutions',
                'institutions as total_tables' => fn($q) =>
                    $q->select(DB::raw('COALESCE(SUM(institutions.total_voting_tables), 0)')),
            ])
            ->get()
            ->map(function ($locality) use ($electionTypeId) {
                $locality->reported_tables = DB::table('voting_table_elections as vte')
                    ->join('voting_tables as vt', 'vte.voting_table_id', '=', 'vt.id')
                    ->join('institutions as inst', 'vt.institution_id', '=', 'inst.id')
                    ->where('inst.locality_id', $locality->id)
                    ->when($electionTypeId, fn($q) => $q->where('vte.election_type_id', $electionTypeId))
                    ->whereIn('vte.status', ['escrutada', 'transmitida'])
                    ->count();
                return $locality;
            });
    }

    private function emptyData($dashboard, $electionTypes, $departments, $provinces, $municipalities, $deptId, $provId, $muniId): array
    {
        return [
            'dashboard'            => $dashboard,
            'electionTypes'        => $electionTypes,
            'departments'          => $departments,
            'provinces'            => $provinces,
            'municipalities'       => $municipalities,
            'selectedDepartment'   => $deptId,
            'selectedProvince'     => $provId,
            'selectedMunicipality' => $muniId,
            'selectedElectionType' => null,
            'typeCategories'       => collect(),
            'categoryStats'        => [],
            'activeCategoryCode'   => null,
            'totalTables'          => 0,
            'reportedTables'       => 0,
            'progressPercentage'   => 0,
            'localityResults'      => [],
            'localityStats'        => collect(),
            'alcaldeCandidates'    => collect(),
            'alcaldeStats'         => [],
            'concejalCandidates'   => collect(),
            'concejalStats'        => [],
            'totalVotesAlcalde'    => 0,
            'totalVotesConcejal'   => 0,
            'totalVotes'           => 0,
            'candidateStats'       => [],
            'candidates'           => collect(),
            'totalBlankVotes'      => 0,
            'totalNullVotes'       => 0,
            'concejalSeatsValidated' => ['seats' => [], 'analysis' => [], 'cutoff' => 0],
            'concejalSeatsAll'       => ['seats' => [], 'analysis' => [], 'cutoff' => 0],
            'concejalSeatChanges'    => [],
            'institutionProgress'    => [],
            'currentSeatMode'        => 'all',
        ];
    }

    public function tablesByInstitution($id, Request $request)
    {
        $institution = Institution::with('locality')->findOrFail($id);
        $electionTypeId = $request->get('election_type') ?? ElectionType::where('active', true)->value('id');
        $tables = VotingTable::where('institution_id', $id)->get();

        $data = $tables->map(function ($table) use ($electionTypeId) {
            $votes = Vote::where('voting_table_id', $table->id)
                ->where('election_type_id', $electionTypeId)
                ->sum('quantity');

            $status = DB::table('voting_table_elections')
                ->where('voting_table_id', $table->id)
                ->where('election_type_id', $electionTypeId)
                ->value('status');

            $isValidated = in_array($status, [
                VotingTableElection::STATUS_ESCRUTADA,
                VotingTableElection::STATUS_TRANSMITIDA,
            ]);

            if ($votes == 0) {
                $state = 'pending';
            } elseif (!$isValidated) {
                $state = 'partial';
            } else {
                $state = 'complete';
            }

            return [
                'id' => $table->id,
                'number' => $table->number ?? $table->id,
                'votes' => $votes,
                'status' => $status,
                'validated' => $isValidated,
                'state' => $state,
            ];
        });
        return view('partials.institution-tables-content', [
            'institution' => $institution,
            'tables' => $data,
            'electionTypeId' => $electionTypeId,
        ]);
    }

    private function simulateSeatImpact(array $currentVotesByParty, array $mesaVotes, int $totalSeats = 11): array
    {
        $current = $this->calculateConcejalSeats($currentVotesByParty, $totalSeats);
        $newVotes = $currentVotesByParty;
        foreach ($mesaVotes as $party => $votes) {
            $newVotes[$party] = ($newVotes[$party] ?? 0) + $votes;
        }
        $new = $this->calculateConcejalSeats($newVotes, $totalSeats);
        $changes = [];
        foreach ($new['seats'] as $party => $seats) {
            $oldSeats = $current['seats'][$party] ?? 0;
            if ($seats != $oldSeats) {
                $changes[$party] = [
                    'before' => $oldSeats,
                    'after'  => $seats,
                    'diff'   => $seats - $oldSeats,
                ];
            }
        }
        return [
            'before' => $current,
            'after'  => $new,
            'changes' => $changes,
            'has_impact' => count($changes) > 0,
        ];
    }
}