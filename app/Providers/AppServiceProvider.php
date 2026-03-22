<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\Observation; // Make sure these models exist
use App\Models\Acta;        // Adjust based on your model names
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('currentUser', auth()->user());
                $recentObservations = \App\Models\Observation::with(['votingTable.institution'])
                    ->where('status', \App\Models\Observation::STATUS_PENDING)
                    ->latest()
                    ->take(5)
                    ->get();
                $rejectedActas = \App\Models\Acta::with(['votingTable.institution'])
                    ->where('status', \App\Models\Acta::STATUS_REJECTED)
                    ->latest()
                    ->take(5)
                    ->get();
                $recentActas = \App\Models\Acta::with(['votingTable.institution'])
                    ->where('status', \App\Models\Acta::STATUS_UPLOADED)
                    ->latest()
                    ->take(5)
                    ->get();
                $validatedByRecinto = \App\Models\VotingTable::has('votes')
                    ->with('institution')
                    ->select('institution_id',
                        DB::raw('count(*) as validated_count'),
                        DB::raw('max(updated_at) as last_validation'))
                    ->groupBy('institution_id')
                    ->orderBy('last_validation', 'desc')
                    ->take(5)
                    ->get();
                $view->with([
                    'recentObservations' => $recentObservations,
                    'rejectedActas'      => $rejectedActas,
                    'recentActas'        => $recentActas,
                    'validatedByRecinto' => $validatedByRecinto,
                    'totalNotifications' => $recentObservations->count() + $rejectedActas->count() + $recentActas->count()
                ]);
            }
        });
    }
}
