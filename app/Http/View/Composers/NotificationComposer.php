<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Observation;
use App\Models\Acta;

class NotificationComposer
{
    public function compose(View $view)
    {
        $recentObservations = Observation::with('votingTable.institution')
            ->where('status', Observation::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        $recentActas = Acta::with('votingTable.institution')
            ->latest()
            ->take(5)
            ->get();

        $view->with([
            'recentObservations' => $recentObservations,
            'recentActas' => $recentActas,
            'totalNotifications' => $recentObservations->count() + $recentActas->count()
        ]);
    }
}
