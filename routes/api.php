<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\VotingTableController;
use App\Http\Controllers\VotingTableVoteController;

/*
|--------------------------------------------------------------------------
| API Routes — Sistema Electoral
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /api automatically by RouteServiceProvider.
| Authentication uses Laravel Sanctum — requires a valid Bearer token.
|
| To generate a token for a user:
|   $token = $user->createToken('api-token')->plainTextToken;
|
| To authenticate requests, add the header:
|   Authorization: Bearer {token}
|
*/

// ── Public: current authenticated user ────────────────────────────────────
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles.permissions');
});

// ── All other API routes require Sanctum token ─────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Geography helpers ──────────────────────────────────────────────────
    // Used by cascading selects in forms (candidates, institutions, etc.)
    Route::prefix('geo')->name('api.geo.')->group(function () {
        Route::get('provinces/{department}',           [HomeController::class,        'getProvinces'])->name('provinces');
        Route::get('municipalities/{province}',         [HomeController::class,        'getMunicipalities'])->name('municipalities');
        Route::get('institutions/{institution}/tables', [VotingTableController::class, 'getByInstitution'])->name('institution-tables');
    });

    // ── Candidates ─────────────────────────────────────────────────────────
    Route::prefix('candidates')->name('api.candidates.')->group(function () {
        Route::get('/',                             [CandidateController::class, 'apiIndex'])->name('index');
        Route::get('/by-election/{electionTypeId}', [CandidateController::class, 'getByElection'])->name('by-election');
        Route::get('/{candidate}',                  [CandidateController::class, 'apiShow'])->name('show');
    });

    // ── Voting tables ──────────────────────────────────────────────────────
    Route::prefix('voting-tables')->name('api.voting-tables.')->group(function () {
        Route::get('/',                                       [VotingTableController::class, 'apiIndex'])->name('index');
        Route::get('/{table}',                                [VotingTableController::class, 'apiShow'])->name('show');
        Route::get('/{table}/votes',                          [VotingTableVoteController::class, 'getTableVotes'])->name('votes');
        Route::get('/{table}/stats',                          [VotingTableVoteController::class, 'getTableStats'])->name('stats');
        Route::get('/{table}/observations',                   [ObservationController::class, 'getByTable'])->name('observations');
        Route::post('/{table}/validate',                      [VotingTableVoteController::class, 'validateTable'])->name('validate');
    });

    // ── Votes ──────────────────────────────────────────────────────────────
    Route::prefix('votes')->name('api.votes.')->group(function () {
        Route::post('/register',                  [VotingTableVoteController::class, 'registerVotes'])->name('register');
        Route::post('/{tableId}/review',          [VotingTableVoteController::class, 'reviewTable'])->name('review');
        Route::post('/{tableId}/correct',         [VotingTableVoteController::class, 'correctTable'])->name('correct');
        Route::post('/{tableId}/validate',        [VotingTableVoteController::class, 'validateTable'])->name('validate');
        Route::post('/{tableId}/observe',         [VotingTableVoteController::class, 'observeTable'])->name('observe');
        Route::post('/{tableId}/reopen',          [VotingTableVoteController::class, 'reopenTable'])->name('reopen');
    });

    // ── Actas ──────────────────────────────────────────────────────────────
    Route::prefix('actas')->name('api.actas.')->group(function () {
        Route::post('/upload',              [ActaController::class, 'store'])->name('upload');
        Route::get('/table/{tableId}',      [ActaController::class, 'getTableActas'])->name('table');
        Route::post('/{id}/verify',         [ActaController::class, 'verify'])->name('verify');
        Route::post('/{id}/observe',        [ActaController::class, 'observe'])->name('observe');
        Route::post('/{id}/approve',        [ActaController::class, 'approve'])->name('approve');
        // Photo/PDF served inline — still authenticated
        Route::get('/{id}/photo',           [ActaController::class, 'servePhoto'])->name('photo');
        Route::get('/{id}/pdf',             [ActaController::class, 'servePdf'])->name('pdf');
    });

    // ── Observations ───────────────────────────────────────────────────────
    Route::prefix('observations')->name('api.observations.')->group(function () {
        Route::post('/',                    [ObservationController::class, 'store'])->name('store');
        Route::post('/{id}/resolve',        [ObservationController::class, 'resolve'])->name('resolve');
        Route::get('/table/{tableId}',      [ObservationController::class, 'getTableObservations'])->name('table');
        Route::get('/stats',                [ObservationController::class, 'getStats'])->name('stats');
    });

    // ── Institutions (read-only for external consumers) ────────────────────
    Route::prefix('institutions')->name('api.institutions.')->group(function () {
        Route::get('/',                     [InstitutionController::class, 'apiIndex'])->name('index');
        Route::get('/{institution}',        [InstitutionController::class, 'apiShow'])->name('show');
        Route::get('/{institution}/tables', [VotingTableController::class, 'getByInstitution'])->name('tables');
    });

});