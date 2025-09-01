<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Institution;
use App\Models\VotingTable;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function root(){
        $dashboard = Dashboard::first();
        if (!$dashboard->is_public && !Auth::check()) {
            return redirect()->route('login');
        }
        $electionData = $this->getElectionData();
        if (Auth::check()) {
            return view('index', array_merge(compact('dashboard'), $electionData));
        }
        return view('landing', array_merge(compact('dashboard'), $electionData));
    }

    public function index(Request $request){
        $dashboard = Dashboard::first();
        if (!$dashboard->is_public && !Auth::check()) {
            return redirect()->route('login');
        }
        $electionData = $this->getElectionData();
        if (Auth::check()) {
            if (view()->exists($request->path())) {
                return view($request->path(), array_merge(compact('dashboard'), $electionData));
            }
            return abort(404);
        }
        return view('landing', array_merge(compact('dashboard'), $electionData));
    }

    public function toggleDashboardVisibility(Request $request){
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $dashboard = Dashboard::first();
        $dashboard->is_public = !$dashboard->is_public;
        $dashboard->save();

        return response()->json([
            'success' => true,
            'is_public' => $dashboard->is_public,
            'message' => 'Dashboard visibility updated successfully'
        ]);
    }

    private function getElectionData() {
        // $presidentialCandidates = Candidate::where('position', 'Presidente')->get();
        $presidentialCandidates = Candidate::all();

        $candidateVotes = Vote::select('candidate_id', DB::raw('SUM(quantity) as total_votes'))
            ->groupBy('candidate_id')
            ->with('candidate')
            ->orderByDesc('total_votes')
            ->get();

        $totalVotes = $candidateVotes->sum('total_votes');
        $candidateStats = [];
        $rank = 1;

        foreach ($candidateVotes->sortByDesc('total_votes') as $cv) {
            $percentage = $totalVotes > 0 ? ($cv->total_votes / $totalVotes) * 100 : 0;
            $trend = $percentage >= 15 ? 'up' : ($percentage < 5 ? 'down' : 'neutral');
            $candidateStats[$cv->candidate_id] = [
                'votes' => (int)$cv->total_votes,
                'percentage' => round($percentage, 1),
                'trend' => $trend,
                'rank' => $rank++,
                'candidate' => $cv->candidate
            ];
        }

        $totalTables = VotingTable::count();
        $reportedTables = Vote::distinct('voting_table_id')->count('voting_table_id');
        $progressPercentage = $totalTables > 0 ? round(($reportedTables / $totalTables) * 100, 2) : 0;
    
        $municipalityResults = $this->getMunicipalityResults();
        $municipalityStats = $this->getMunicipalityStats();
        
        // dd(compact(
        return compact(
            'presidentialCandidates',
            'candidateStats',
            'totalVotes',
            'progressPercentage',
            'totalTables',
            'reportedTables',
            'municipalityResults',
            'municipalityStats'
        );
    }

    private function getMunicipalityResults() {
        $municipalityVotes = DB::table('municipalities')
            ->select(
                'municipalities.id as municipality_id',
                'municipalities.name as municipality_name',
                'municipalities.latitude',
                'municipalities.longitude',
                'candidates.id as candidate_id',
                'candidates.name as candidate_name',
                'candidates.party',
                DB::raw('COALESCE(SUM(votes.quantity), 0) as total_votes')
            )
            ->leftJoin('institutions', 'municipalities.id', '=', 'institutions.municipality_id')
            ->leftJoin('voting_tables', 'institutions.id', '=', 'voting_tables.institution_id')
            ->leftJoin('votes', 'voting_tables.id', '=', 'votes.voting_table_id')
            ->leftJoin('candidates', 'votes.candidate_id', '=', 'candidates.id')
            ->where('municipalities.province_id', 2)
            ->groupBy(
                'municipalities.id',
                'municipalities.name',
                'municipalities.latitude',
                'municipalities.longitude',
                'candidates.id',
                'candidates.name',
                'candidates.party'
            )
            ->orderBy('municipalities.name')
            ->orderByDesc('total_votes')
            ->get()
            ->toArray();

        $municipalityResults = [];
        foreach ($municipalityVotes as $mv) {
            $totalVotesInt = (int)$mv->total_votes;
            if (!isset($municipalityResults[$mv->municipality_id])) {
                $municipalityResults[$mv->municipality_id] = [
                    'name' => $mv->municipality_name,
                    'latitude' => $mv->latitude,
                    'longitude' => $mv->longitude,
                    'total_votes' => 0,
                    'candidates' => []
                ];
            }
            if ($mv->candidate_id !== null) {
                $municipalityResults[$mv->municipality_id]['candidates'][] = [
                    'id' => $mv->candidate_id,
                    'name' => $mv->candidate_name,
                    'party' => $mv->party,
                    'votes' => $totalVotesInt
                ];
                $municipalityResults[$mv->municipality_id]['total_votes'] += $totalVotesInt;
            }
        }
        foreach ($municipalityResults as &$municipality) {
            foreach ($municipality['candidates'] as &$candidate) {
                $candidate['percentage'] = $municipality['total_votes'] > 0
                    ? round(($candidate['votes'] / $municipality['total_votes']) * 100, 1)
                    : 0;
            }
        }
        return $municipalityResults;
    }
    private function getMunicipalityStats() {
        return DB::table('municipalities')
            ->select(
                'municipalities.id',
                'municipalities.name',
                DB::raw('COUNT(DISTINCT institutions.id) as total_institutions'),
                DB::raw('COUNT(DISTINCT voting_tables.id) as total_tables'),
                DB::raw('COUNT(DISTINCT CASE WHEN votes.id IS NOT NULL THEN voting_tables.id END) as reported_tables')
            )
            ->leftJoin('institutions', 'municipalities.id', '=', 'institutions.municipality_id')
            ->leftJoin('voting_tables', 'institutions.id', '=', 'voting_tables.institution_id')
            ->leftJoin('votes', 'voting_tables.id', '=', 'votes.voting_table_id')
            ->where('municipalities.province_id', 2)
            ->groupBy('municipalities.id', 'municipalities.name')
            ->get();
    }


    /*Language Translation*/
    public function lang($locale){
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar =  $avatarName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "User Details Updated successfully!"
            // ], 200); // Status code here
            return redirect()->back();
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "Something went wrong!"
            // ], 200); // Status code here
            return redirect()->back();

        }
    }

    public function updatePassword(Request $request, $id){
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}
