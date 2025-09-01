<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Candidate;
use App\Models\VotingTable;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotingTableVoteController extends Controller
{
    public function index(Request $request){
        try {
            $totalCount = 0;
            $totalCapacity = 0;
            $institutionId = $request->input('institution_id');
            $institutions = Institution::where('active', true)->get();            
            $votingTablesQuery = VotingTable::with(['institution', 'votes.candidate']);            
            
            if ($institutionId) {
                $votingTablesQuery->where('institution_id', $institutionId);
            }
            
            $votingTables = $votingTablesQuery->get();
            $candidates = Candidate::all();
            
            // Calculate candidate totals
            $candidateTotals = [];
            foreach ($candidates as $candidate) {
                $candidateTotals[$candidate->id] = 0;
            }
            
            foreach ($votingTables as $table) {
                $totalCapacity += $table->capacity ?? 0;
                
                foreach ($table->votes as $vote) {
                    $candidateTotals[$vote->candidate_id] += $vote->quantity;
                    $totalCount += $vote->quantity;
                }
            }
            
            // Calculate candidate rankings and trends
            $candidateStats = [];
            $sortedCandidates = collect($candidateTotals)->sortDesc();
            $maxVotes = $sortedCandidates->first() ?? 0;
            
            foreach ($candidates as $candidate) {
                $votes = $candidateTotals[$candidate->id] ?? 0;
                $percentage = $totalCount > 0 ? ($votes / $totalCount) * 100 : 0;
                
                // Simple trend logic: only winner gets up arrow, others get down arrow
                $trend = 'down'; // Default to down arrow
                if ($maxVotes > 0 && $votes == $maxVotes) {
                    $trend = 'up'; // Only the winning candidate gets up arrow
                } elseif ($totalCount == 0) {
                    $trend = 'neutral'; // No votes yet
                }
                
                $candidateStats[$candidate->id] = [
                    'votes' => $votes,
                    'percentage' => $percentage,
                    'trend' => $trend,
                    'rank' => array_search($votes, $sortedCandidates->values()->toArray()) + 1
                ];
            }
            
            return view('voting-table-votes', compact(
                'totalCount',
                'totalCapacity',
                'votingTables',
                'candidates',
                'institutions',
                'institutionId',
                'candidateTotals',
                'candidateStats'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error loading voting table votes: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading voting table data.');
        }
    }

    public function registerVotes(Request $request){
        try {
            $user = Auth::user();
            $votingTableId = $request->input('voting_table_id');
            $votesData = $request->input('votes', []);
            $closeTable = $request->input('close', false);
            $votingTable = VotingTable::find($votingTableId);

            if (!$votingTable) {
                return response()->json(['success' => false, 'message' => 'Voting table not found.'], 404);
            }

            $totalVotes = array_sum($votesData);
            if ($votingTable->capacity && $totalVotes > $votingTable->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total votes exceed the voting table capacity of ' . $votingTable->capacity
                ], 422);
            }

            DB::transaction(function () use ($votesData, $votingTable, $user, $closeTable) {
                foreach ($votesData as $candidateId => $quantity) {
                    if ($quantity > 0) {
                        Vote::updateOrCreate(
                            ['voting_table_id' => $votingTable->id, 'candidate_id' => $candidateId],
                            ['quantity' => $quantity, 'user_id' => $user->id, 'verified_at' => now()]
                        );
                    } else {
                        Vote::where('voting_table_id', $votingTable->id)
                            ->where('candidate_id', $candidateId)
                            ->delete();
                    }
                }

                // Update table status
                if ($votingTable->status === 'pending') {
                    $votingTable->status = 'active';
                }
                
                // Close table if requested
                if ($closeTable) {
                    $votingTable->status = 'closed';
                }
                
                $votingTable->save();
            });

            return response()->json(['success' => true, 'message' => 'Votes registered successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registerAllVotes(Request $request){
        try {
            $user = Auth::user();
            $tablesData = $request->input('tables', []);
            $closeAll = $request->input('close_all', false);
            $errors = [];

            DB::transaction(function () use ($tablesData, $user, $closeAll, &$errors) {
                foreach ($tablesData as $tableId => $votesData) {
                    $votingTable = VotingTable::find($tableId);
                    
                    if (!$votingTable) {
                        $errors[] = "Table $tableId not found";
                        continue;
                    }

                    $totalVotes = array_sum($votesData);
                    if ($votingTable->capacity && $totalVotes > $votingTable->capacity) {
                        $errors[] = "Table {$votingTable->code} exceeds capacity ({$totalVotes}/{$votingTable->capacity})";
                        continue;
                    }

                    foreach ($votesData as $candidateId => $quantity) {
                        if ($quantity > 0) {
                            Vote::updateOrCreate(
                                ['voting_table_id' => $votingTable->id, 'candidate_id' => $candidateId],
                                ['quantity' => $quantity, 'user_id' => $user->id, 'verified_at' => now()]
                            );
                        } else {                        
                            Vote::where('voting_table_id', $votingTable->id)
                                ->where('candidate_id', $candidateId)
                                ->delete();
                        }
                    }

                    // Update table status
                    if ($votingTable->status === 'pending') {
                        $votingTable->status = 'active';
                    }
                    
                    // Close table if requested
                    if ($closeAll && $votingTable->status !== 'closed') {
                        $votingTable->status = 'closed';
                    }
                    
                    $votingTable->save();
                }
            });

            if (!empty($errors)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Some tables had errors: ' . implode(', ', $errors)
                ], 422);
            }

            $message = $closeAll 
                ? 'All votes registered and tables closed successfully.' 
                : 'All votes registered successfully.';
                
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}