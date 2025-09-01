<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\VotingTableController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingTableVoteController;
use App\Http\Controllers\ElectionDashboardController;
use App\Http\Controllers\HomeController;

Auth::routes();

Route::get('index/{locale}', [HomeController::class, 'lang']);
Route::get('/', [HomeController::class, 'root'])->name('root');

Route::middleware('auth')->group(function () {
    Route::post('/toggle-dashboard-visibility', [HomeController::class, 'toggleDashboardVisibility'])
        ->name('toggle-dashboard-visibility');
    // Route::get('index', [ElectionDashboardController::class, 'index']);
    // Route::get('election-data/live', [ElectionDashboardController::class, 'getLiveData'])->name('election.live-data');
    Route::resource('institutions', InstitutionController::class);
    Route::resource('voting-tables', VotingTableController::class);
    Route::resource('managers', ManagerController::class);
    Route::get('/managers/voting-tables/{institution}', [ManagerController::class, 'getVotingTables'])->name('managers.voting-tables');
    Route::resource('candidates', CandidateController::class);
    
    // Route::get('votes', [VoteController::class, 'index'])->name('votes.index');
    // Route::post('/votes/get-table-details', [VoteController::class, 'getTableDetails'])->name('votes.getTableDetails');

    // Route::get('votes/voting-tables/{institutionId}', [VoteController::class, 'getVotingTables'])->name('votes.getVotingTables');
    // Route::post('votes/table-details', [VoteController::class, 'getTableDetails'])->name('votes.getTableDetails'); // NEW ROUTE
    // Route::post('votes/complete', [VoteController::class, 'completeVotation'])->name('votes.complete');
    // Route::get('votes/manage', [VoteController::class, 'manageVotes'])->name('votes.manage');
    // Route::post('votes/save', [VoteController::class, 'saveVotes'])->name('votes.save');
    // Route::resource('votes', VoteController::class)->except(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::get('/voting-table-votes', [VotingTableVoteController::class, 'index'])->name('voting-table-votes.index');
    Route::post('/voting-table-votes/register', [VotingTableVoteController::class, 'registerVotes'])->name('voting-table-votes.register');
    Route::post('/voting-table-votes/register-all', [VotingTableVoteController::class, 'registerAllVotes'])->name('voting-table-votes.register-all');
});

// Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
// Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
