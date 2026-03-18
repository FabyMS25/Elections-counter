<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActaController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\VotingTableController;
use App\Http\Controllers\VotingTableVoteController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes — Sistema Electoral
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /api automatically by RouteServiceProvider.
| Authentication uses Laravel Sanctum — requires a valid Bearer token.
|
| To authenticate requests, add the header:
|   Authorization: Bearer {token}
|
*/

// ── Public: Auth ───────────────────────────────────────────────────────────
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
        'device'   => 'nullable|string|max:100',
    ]);

    $user = User::where('email', $request->email)
                ->where('is_active', true)
                ->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Credenciales inválidas.',
        ], 401);
    }

    // Revoke any previous token with the same device name to avoid accumulation
    $deviceName = $request->input('device', 'api-client');
    $user->tokens()->where('name', $deviceName)->delete();

    $token = $user->createToken($deviceName)->plainTextToken;

    // Record the login (mirrors LoginController behaviour)
    $user->last_login_at = now();
    $user->last_login_ip = $request->ip();
    $user->save();

    \App\Models\AuditLog::create([
        'user_id'      => $user->id,
        'action'       => 'login',
        'model_type'   => User::class,
        'model_id'     => $user->id,
        'ip_address'   => $request->ip(),
        'user_agent'   => $request->userAgent(),
        'notes'        => 'Inicio de sesión vía API',
        'performed_at' => now(),
    ]);

    return response()->json([
        'token' => $token,
        'user'  => $user->load('roles.permissions'),
    ]);
})->name('api.login');

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Sesión cerrada correctamente.']);
})->middleware('auth:sanctum')->name('api.logout');

// ── Public: current authenticated user ────────────────────────────────────
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles.permissions');
})->name('api.user');

// ── All other API routes require Sanctum token ─────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── Geography helpers ──────────────────────────────────────────────────
    // Used by cascading selects in forms (candidates, institutions, etc.)
    Route::prefix('geo')->name('api.geo.')->group(function () {
        Route::get('provinces/{department}',           [HomeController::class,        'getProvinces'])->name('provinces');
        Route::get('municipalities/{province}',        [HomeController::class,        'getMunicipalities'])->name('municipalities');
        Route::get('institutions/{institution}/tables',[VotingTableController::class, 'getByInstitution'])->name('institution-tables');
    });

    // ── Candidates ─────────────────────────────────────────────────────────
    Route::prefix('candidates')->name('api.candidates.')->group(function () {
        Route::get('/',                             [CandidateController::class, 'apiIndex'])->name('index');
        Route::get('/by-election/{electionTypeId}', [CandidateController::class, 'getByElection'])->name('by-election');
        Route::get('/{candidate}',                  [CandidateController::class, 'apiShow'])->name('show');
    });

    // ── Voting tables ──────────────────────────────────────────────────────
    // NOTE: POST /{table}/validate was removed here — it duplicated POST /votes/{tableId}/validate.
    //       Use the /votes/{tableId}/validate endpoint for all vote validation actions.
    Route::prefix('voting-tables')->name('api.voting-tables.')->group(function () {
        Route::get('/',                   [VotingTableController::class,      'apiIndex'])->name('index');
        Route::get('/{table}',            [VotingTableController::class,      'apiShow'])->name('show');
        Route::get('/{table}/votes',      [VotingTableVoteController::class,  'getTableVotes'])->name('votes');
        Route::get('/{table}/stats',      [VotingTableVoteController::class,  'getTableStats'])->name('stats');
        Route::get('/{table}/observations',[ObservationController::class,     'getByTable'])->name('observations');
    });

    // ── Votes — full lifecycle ─────────────────────────────────────────────
    Route::prefix('votes')->name('api.votes.')->group(function () {
        Route::post('/register',          [VotingTableVoteController::class, 'registerVotes'])->name('register');
        Route::post('/{tableId}/review',  [VotingTableVoteController::class, 'reviewTable'])->name('review');
        Route::post('/{tableId}/correct', [VotingTableVoteController::class, 'correctTable'])->name('correct');
        Route::post('/{tableId}/validate',[VotingTableVoteController::class, 'validateTable'])->name('validate');
        Route::post('/{tableId}/observe', [VotingTableVoteController::class, 'observeTable'])->name('observe');
        Route::post('/{tableId}/reopen',  [VotingTableVoteController::class, 'reopenTable'])->name('reopen');
    });

    // ── Actas ──────────────────────────────────────────────────────────────
    Route::prefix('actas')->name('api.actas.')->group(function () {
        Route::post('/upload',         [ActaController::class, 'store'])->name('upload');
        Route::get('/table/{tableId}', [ActaController::class, 'getTableActas'])->name('table');
        Route::post('/{id}/verify',    [ActaController::class, 'verify'])->name('verify');
        Route::post('/{id}/observe',   [ActaController::class, 'observe'])->name('observe');
        Route::post('/{id}/approve',   [ActaController::class, 'approve'])->name('approve');
        // Photo/PDF served inline — still authenticated
        Route::get('/{id}/photo',      [ActaController::class, 'servePhoto'])->name('photo');
        Route::get('/{id}/pdf',        [ActaController::class, 'servePdf'])->name('pdf');
    });

    // ── Observations ───────────────────────────────────────────────────────
    Route::prefix('observations')->name('api.observations.')->group(function () {
        Route::post('/',               [ObservationController::class, 'store'])->name('store');
        Route::post('/{id}/resolve',   [ObservationController::class, 'resolve'])->name('resolve');
        Route::get('/table/{tableId}', [ObservationController::class, 'getTableObservations'])->name('table');
        Route::get('/stats',           [ObservationController::class, 'getStats'])->name('stats');
    });

    // ── Institutions (read-only for external consumers) ────────────────────
    Route::prefix('institutions')->name('api.institutions.')->group(function () {
        Route::get('/',                    [InstitutionController::class,  'apiIndex'])->name('index');
        Route::get('/{institution}',       [InstitutionController::class,  'apiShow'])->name('show');
        Route::get('/{institution}/tables',[VotingTableController::class,  'getByInstitution'])->name('tables');
    });

});
