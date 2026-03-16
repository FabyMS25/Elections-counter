<?php
namespace App\Http\Controllers;

use App\Models\Acta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ActaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_actas')->only(['index', 'show', 'getByTable', 'getTableActas', 'servePhoto', 'servePdf']);
        $this->middleware('permission:upload_acta')->only(['upload', 'store']);
        $this->middleware('permission:verify_actas')->only(['verify', 'approve', 'observe']);
    }

    public function index(Request $request)
    {
        $query = Acta::with(['votingTable.institution', 'user', 'signedBy']);
        if ($request->filled('status'))          $query->where('status', $request->status);
        if ($request->filled('voting_table_id')) $query->where('voting_table_id', $request->voting_table_id);
        $actas = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('actas.index', compact('actas'));
    }

    public function show(Acta $acta)
    {
        $acta->load(['votingTable.institution', 'user', 'signedBy', 'categoryResults.electionTypeCategory.electionCategory']);
        return view('actas.show', compact('acta'));
    }

    /**
     * Serve acta photo directly from disk — bypasses the storage symlink.
     * Route: GET /actas/{id}/photo
     */
    public function servePhoto(int $id): Response
    {
        $acta = Acta::findOrFail($id);

        if (!$acta->photo_path) {
            abort(404, 'Sin foto registrada');
        }

        $path = $this->absolutePath($acta->photo_path);

        if (!file_exists($path)) {
            Log::error("servePhoto 404", ['id' => $id, 'path' => $path, 'stored' => $acta->photo_path]);
            abort(404, "Archivo no encontrado: {$path}");
        }

        return response(file_get_contents($path), 200, [
            'Content-Type'        => $this->mimeType($path),
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }

    /**
     * Serve acta PDF directly from disk.
     * Route: GET /actas/{id}/pdf
     */
    public function servePdf(int $id): Response
    {
        $acta = Acta::findOrFail($id);

        if (!$acta->pdf_path) {
            abort(404, 'Sin PDF registrado');
        }

        $path = $this->absolutePath($acta->pdf_path);

        if (!file_exists($path)) {
            abort(404, "PDF no encontrado: {$path}");
        }

        return response(file_get_contents($path), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }

    /**
     * List all actas for a voting table.
     *
     * This method reads raw DB columns only — no Eloquent accessors that
     * could throw (status_badge, file_size_formatted, inconsistencies cast).
     * Each row is mapped safely with explicit null-checks.
     */
    public function getTableActas(int $tableId)
    {
        try {
            $rows = DB::table('actas')
                ->where('voting_table_id', $tableId)
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->get();
            $userIds = $rows->pluck('user_id')->merge($rows->pluck('signed_by'))->filter()->unique()->values();
            $users   = DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');
            $result = $rows->map(function ($row) use ($users) {
                $inconsistencies = [];
                if ($row->inconsistencies) {
                    $decoded = json_decode($row->inconsistencies, true);
                    $inconsistencies = is_array($decoded) ? $decoded : [];
                }
                $sizeFmt = null;
                if ($row->file_size) {
                    $bytes = (int) $row->file_size;
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $i = 0;
                    while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
                    $sizeFmt = round($bytes, 2) . ' ' . $units[$i];
                }
                $statusLabels = [
                    'uploaded'  => 'Subida',
                    'verified'  => 'Verificada',
                    'observed'  => 'Observada',
                    'corrected' => 'Corregida',
                    'approved'  => 'Aprobada',
                    'rejected'  => 'Rechazada',
                ];
                $statusLabel = $statusLabels[$row->status] ?? $row->status;
                return [
                    'id'                  => $row->id,
                    'code'                => $row->code ?? '',
                    'acta_number'         => $row->acta_number ?? '',
                    'election_type_id'    => $row->election_type_id,
                    'status'              => $row->status ?? 'uploaded',
                    'status_label'        => $statusLabel,
                    'is_consistent'       => (bool) $row->is_consistent,
                    'inconsistencies'     => $inconsistencies,
                    'photo_url'           => $row->photo_path ? url('/actas/' . $row->id . '/photo') : null,
                    'pdf_url'             => $row->pdf_path   ? url('/actas/' . $row->id . '/pdf')   : null,
                    'file_size_formatted' => $sizeFmt,
                    'uploaded_by'         => $users[$row->user_id] ?? 'N/A',
                    'created_at'          => $row->created_at
                        ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i')
                        : '—',
                    'signed_by'           => $row->signed_by ? ($users[$row->signed_by] ?? null) : null,
                    'signed_at'           => $row->signed_at
                        ? \Carbon\Carbon::parse($row->signed_at)->format('d/m/Y H:i')
                        : null,
                ];
            });
            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('ActaController@getTableActas FAILED', [
                'table_id'  => $tableId,
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error'   => 'Error al cargar actas',
                'detail'  => $e->getMessage(),  
                'file'    => basename($e->getFile()) . ':' . $e->getLine(),
            ], 500);
        }
    }

    public function getByTable(int $tableId)
    {
        return $this->getTableActas($tableId);
    }

    public function upload(Request $request)
    {
        try {
            $phpLimit = min($this->phpMaxUploadBytes(), $this->phpMaxPostBytes());
            $maxKb    = (int) min(10240, floor($phpLimit / 1024));

            $validated = $request->validate([
                'voting_table_id'  => 'required|integer|exists:voting_tables,id',
                'election_type_id' => 'required|integer|exists:election_types,id',
                'acta_number'      => 'required|string|max:50',
                'photo'            => "required|file|image|mimes:jpeg,png,jpg|max:{$maxKb}",
                'pdf'              => 'nullable|file|mimes:pdf|max:' . min(20480, $maxKb * 2),
                'has_physical'     => 'nullable',
            ], [
                'photo.max'      => 'La foto no puede superar ' . round($maxKb / 1024, 1) . ' MB. Reduzca la resolución y vuelva a intentarlo.',
                'photo.image'    => 'El archivo debe ser una imagen (JPEG o PNG).',
                'photo.mimes'    => 'Solo se aceptan imágenes JPEG o PNG.',
                'photo.required' => 'Debe seleccionar la foto del acta.',
            ]);

            DB::beginTransaction();

            $approved = Acta::where('voting_table_id', $validated['voting_table_id'])
                ->where('election_type_id', $validated['election_type_id'])
                ->where('status', Acta::STATUS_APPROVED)
                ->exists();

            if ($approved) {
                return response()->json(['success' => false, 'message' => 'Esta mesa ya tiene un acta aprobada.'], 422);
            }

            $photoPath = $request->file('photo')->store('actas/photos/' . date('Y/m'), 'public');
            $pdfPath   = null;
            if ($request->hasFile('pdf')) {
                $pdfPath = $request->file('pdf')->store('actas/pdfs/' . date('Y/m'), 'public');
            }
            $savedPath = $this->absolutePath($photoPath);
            if (!file_exists($savedPath)) {
                throw new \RuntimeException(
                    "El archivo se guardó pero no es legible en disco. " .
                    "Ruta: {$savedPath}. " .
                    "Verifique permisos en storage/app/public/"
                );
            }
            $acta = Acta::create([
                'code'              => Acta::generateCode(),
                'acta_number'       => $validated['acta_number'],
                'voting_table_id'   => $validated['voting_table_id'],
                'election_type_id'  => (int) $validated['election_type_id'],
                'user_id'           => Auth::id(),
                'photo_path'        => $photoPath,
                'pdf_path'          => $pdfPath,
                'original_filename' => $request->file('photo')->getClientOriginalName(),
                'file_size'         => $request->file('photo')->getSize(),
                'hash'              => hash_file('sha256', $request->file('photo')->getRealPath()),
                'status'            => Acta::STATUS_UPLOADED,
                'is_consistent'     => false,
                'metadata'          => [
                    'mime_type'    => $request->file('photo')->getMimeType(),
                    'upload_ip'    => $request->ip(),
                    'user_agent'   => $request->userAgent(),
                    'has_physical' => in_array($request->input('has_physical'), ['on','1',1,'true',true], true),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Acta subida correctamente',
                'acta'    => [
                    'id'          => $acta->id,
                    'code'        => $acta->code,
                    'acta_number' => $acta->acta_number,
                    'photo_url'   => url('/actas/' . $acta->id . '/photo'),
                    'pdf_url'     => $acta->pdf_path ? url('/actas/' . $acta->id . '/pdf') : null,
                    'status'      => $acta->status,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ActaController@upload: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Error al subir el acta: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        return $this->upload($request);
    }

    public function verify(int $id)
    {
        try {
            DB::beginTransaction();
            $acta = Acta::findOrFail($id);
            if ($acta->status !== Acta::STATUS_UPLOADED) {
                return response()->json(['success' => false, 'message' => 'Esta acta ya fue procesada (estado: ' . $acta->status . ')'], 422);
            }
            $acta->markAsVerified(Auth::id());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Acta verificada', 'is_consistent' => $acta->is_consistent]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ActaController@verify: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function observe(Request $request, int $id)
    {
        try {
            $validated = $request->validate(['notes' => 'required|string|max:500']);
            DB::beginTransaction();
            Acta::findOrFail($id)->markAsObserved(Auth::id(), $validated['notes']);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Acta observada']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function approve(int $id)
    {
        try {
            DB::beginTransaction();
            $acta = Acta::findOrFail($id);
            if ($acta->status !== Acta::STATUS_VERIFIED) {
                return response()->json(['success' => false, 'message' => 'El acta debe estar verificada antes de aprobarse'], 422);
            }
            $acta->markAsApproved(Auth::id());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Acta aprobada']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Acta $acta)
    {
        try {
            if ($acta->photo_path) Storage::disk('public')->delete($acta->photo_path);
            if ($acta->pdf_path)   Storage::disk('public')->delete($acta->pdf_path);
            $acta->delete();
            return response()->json(['success' => true, 'message' => 'Acta eliminada']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function absolutePath(string $stored): string
    {
        $p = str_replace('\\', '/', ltrim($stored, '/'));
        foreach (['storage/public/', 'storage/', 'public/'] as $prefix) {
            if (str_starts_with($p, $prefix)) {
                $p = substr($p, strlen($prefix));
                break;
            }
        }
        return storage_path('app' . DIRECTORY_SEPARATOR . 'public')
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $p);
    }

    private function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => (function_exists('mime_content_type') ? mime_content_type($path) : null) ?: 'application/octet-stream',
        };
    }

    private function parseIniSize(string $v): int
    {
        $n = (int) $v;
        return match (strtolower(substr(trim($v), -1))) {
            'g' => $n << 30, 'm' => $n << 20, 'k' => $n << 10, default => $n,
        };
    }

    private function phpMaxUploadBytes(): int { return $this->parseIniSize(ini_get('upload_max_filesize') ?: '8M'); }
    private function phpMaxPostBytes():  int  { return $this->parseIniSize(ini_get('post_max_size')       ?: '8M'); }
}