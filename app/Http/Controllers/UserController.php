<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Institution;
use App\Models\VotingTable;
use App\Models\UserAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function requirePermission(string $perm): void
    {
        if (!Auth::user()->hasPermission($perm)) {
            abort(403, "Acceso denegado: se requiere '{$perm}'.");
        }
    }

    private function buildRolePermMap(): array
    {
        return Role::with('permissions')->get()
            ->mapWithKeys(fn($r) => [$r->id => $r->permissions->pluck('id')->toArray()])
            ->toArray();
    }

    private function resolveAvatar(array $roleNames, string $gender = 'm'): string
    {
        $suffix = $gender === 'w' ? 'w' : 'm';
        $tier   = 'op';
        foreach ($roleNames as $n) {
            $n = strtolower($n);
            if (str_contains($n, 'admin'))      { $tier = 'admin';   break; }
            if (str_contains($n, 'supervisor')) { if ($tier !== 'admin') $tier = 'manager'; }
            if (str_contains($n, 'registrador') || str_contains($n, 'modificador')) {
                if ($tier === 'op') $tier = 'manager';
            }
        }
        $file = match ($tier) { 'admin' => 'admin', 'manager' => 'manager', default => 'op' };
        return "avatar-{$file}-{$suffix}.png";
    }

    private function attachRoles(User $user, array $roleIds): void
    {
        $now = now();
        foreach ($roleIds as $roleId) {
            if (DB::table('role_user')->where('role_id', $roleId)->where('user_id', $user->id)->exists()) {
                continue;
            }
            DB::table('role_user')->insert([
                'role_id'    => $roleId,
                'user_id'    => $user->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function attachDirectPermissions(User $user, array $permIds): void
    {
        $now  = now();
        $rows = array_map(fn($pid) => [
            'permission_id' => $pid,
            'user_id'       => $user->id,
            'created_at'    => $now,
            'updated_at'    => $now,
        ], $permIds);
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('permission_user')->insertOrIgnore($chunk);
        }
    }

    public function index(Request $request)
    {
        $this->requirePermission('view_users');
        $query = User::with(['roles', 'assignments' => function($q) {
            $q->with(['institution', 'votingTable']);
        }]);
        $isDelegatesView = $request->routeIs('users.delegates') || $request->has('delegates_view');
        if ($isDelegatesView) {
            $query->whereHas('assignments', function($q) {
                $q->where('status', 'activo');
            });
            if (!$request->filled('status')) {
                $query->where('is_active', true);
            }
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'ilike', "%{$s}%")
                ->orWhere('last_name', 'ilike', "%{$s}%")
                ->orWhere('email', 'ilike', "%{$s}%")
                ->orWhere('id_card', 'ilike', "%{$s}%")
            );
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('delegate_type')) {
            $query->whereHas('assignments', fn($q) => $q
                ->where('delegate_type', $request->delegate_type)
                ->where('status', 'activo')
            );
        }
        if ($request->filled('institution_id')) {
            $query->whereHas('assignments', fn($q) => $q
                ->where('institution_id', $request->institution_id)
                ->where('status', 'activo')
            );
        }
        $sort = in_array($request->get('sort'), ['name', 'email', 'created_at', 'last_login_at', 'is_active'])
            ? $request->get('sort') : 'name';
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $order);
        $users = $query->paginate(20)->withQueryString();
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'delegates' => UserAssignment::where('status', 'activo')->distinct('user_id')->count('user_id'),
        ];
        if ($isDelegatesView) {
            $stats['active_assignments'] = UserAssignment::where('status', 'activo')->count();
            $stats['recintos'] = UserAssignment::where('status', 'activo')
                ->whereNull('voting_table_id')
                ->distinct('institution_id')
                ->count('institution_id');
            $stats['mesas'] = UserAssignment::where('status', 'activo')
                ->whereNotNull('voting_table_id')
                ->count();
        }
        $roles = Role::orderBy('display_name')->get();
        $delegateTypes = UserAssignment::getDelegateTypes();
        $institutions = Institution::where('status', 'activo')->get(['id', 'name']);
        return view('users.index', compact(
            'users',
            'stats',
            'roles',
            'delegateTypes',
            'institutions',
            'isDelegatesView'
        ));
    }

    public function create()
    {
        $this->requirePermission('create_users');

        $roles       = Role::with('permissions')->orderBy('display_name')->get();
        $permissions = $this->getOrderedPermissions();
        $rolePermMap = $this->buildRolePermMap();

        return view('users.create', compact('roles', 'permissions', 'rolePermMap'));
    }

    public function store(Request $request)
    {
        $this->requirePermission('create_users');

        $request->validate([
            'name'          => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'id_card'       => 'nullable|string|unique:users,id_card',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'password'      => 'required|string|min:8|confirmed',
            'gender'        => 'nullable|in:m,w',
            'roles'         => 'nullable|array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request) {
            $roleNames = $request->filled('roles')
                ? Role::whereIn('id', $request->roles)->pluck('name')->toArray()
                : [];

            $user = User::create([
                'name'       => $request->name,
                'last_name'  => $request->last_name,
                'id_card'    => $request->id_card,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'password'   => Hash::make($request->password),
                'is_active'  => true,
                'avatar'     => $this->resolveAvatar($roleNames, $request->input('gender', 'm')),
                'created_by' => Auth::id(),
            ]);

            if ($request->filled('roles')) {
                $this->attachRoles($user, $request->roles);
            }
            if ($request->filled('permissions')) {
                $this->attachDirectPermissions($user, $request->permissions);
            }
        });

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $this->requirePermission('view_users');
        $user->load(['roles.permissions', 'createdBy', 'updatedBy']);
        $rolePermIds         = $user->roles->flatMap(fn($r) => $r->permissions->pluck('id'))->unique()->values()->toArray();
        $allEffectivePermIds = DB::table('permission_user')->where('user_id', $user->id)->pluck('permission_id')->toArray();
        $directPermIds       = $allEffectivePermIds;
        $totalPermCount      = count($allEffectivePermIds);

        return view('users.show', compact(
            'user',
            'rolePermIds',
            'directPermIds',
            'allEffectivePermIds',
            'totalPermCount'
        ));
    }

    public function edit(User $user)
    {
        $this->requirePermission('edit_users');

        $roles       = Role::with('permissions')->orderBy('display_name')->get();
        $permissions = $this->getOrderedPermissions();
        $rolePermMap = $this->buildRolePermMap();
        $userRoleIds = DB::table('role_user')
            ->where('user_id', $user->id)
            ->pluck('role_id')
            ->toArray();

        $userDirectPermIds = DB::table('permission_user')
            ->where('user_id', $user->id)
            ->pluck('permission_id')
            ->toArray();

        return view('users.edit', compact(
            'user', 'roles', 'permissions', 'rolePermMap',
            'userRoleIds', 'userDirectPermIds'
        ));
    }

    public function update(Request $request, User $user)
    {
        $this->requirePermission('edit_users');

        $request->validate([
            'name'          => 'required|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'id_card'       => 'nullable|string|unique:users,id_card,' . $user->id,
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'password'      => 'nullable|string|min:8|confirmed',
            'is_active'     => 'nullable|boolean',
            'gender'        => 'nullable|in:m,w',
            'roles'         => 'nullable|array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request, $user) {
            $newRoleIds = $request->input('roles', []);
            $roleNames  = !empty($newRoleIds)
                ? Role::whereIn('id', $newRoleIds)->pluck('name')->toArray()
                : $user->roles->pluck('name')->toArray();

            $data = [
                'name'       => $request->name,
                'last_name'  => $request->last_name,
                'id_card'    => $request->id_card,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
                'is_active'  => $request->boolean('is_active', $user->is_active),
                'avatar'     => $this->resolveAvatar($roleNames, $request->input('gender', $user->gender ?? 'm')),
                'updated_by' => Auth::id(),
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
            if ($request->has('roles')) {
                DB::table('role_user')->where('user_id', $user->id)->delete();
                if (!empty($newRoleIds)) {
                    $this->attachRoles($user, $newRoleIds);
                }
            }
            if ($request->has('permissions')) {
                DB::table('permission_user')->where('user_id', $user->id)->delete();
                if (!empty($request->permissions)) {
                    $this->attachDirectPermissions($user, $request->permissions);
                }
            }
        });

        return redirect()->route('users.show', $user)->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $this->requirePermission('delete_users');
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }
        $user->update(['is_active' => false, 'updated_by' => Auth::id()]);
        return redirect()->route('users.index')->with('success', 'Usuario desactivado.');
    }

    public function activate(User $user)
    {
        $this->requirePermission('activate_users');
        $user->update(['is_active' => true, 'updated_by' => Auth::id()]);
        return redirect()->route('users.show', $user)->with('success', 'Usuario activado.');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)
            ->when($request->filled('user_id'), fn($q) => $q->where('id', '!=', $request->user_id))
            ->exists();
        return response()->json(['exists' => $exists]);
    }

    public function delegacionesForm(User $user)
    {
        $this->requirePermission('assign_delegates');

        $assignments = UserAssignment::with(['institution', 'votingTable.institution', 'assignedBy'])
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $institutions = Institution::where('status', 'activo')
            ->with('municipality:id,name')
            ->orderBy('name')
            ->get();

        $votingTables = VotingTable::with('institution:id,name')
            ->orderBy('institution_id')
            ->orderBy('number')
            ->get();

        $delegateTypes      = UserAssignment::getDelegateTypes();
        $institutionOnlyTypes = ['delegado_general', 'tecnico', 'observador'];
        $mesaTypes            = ['delegado_mesa', 'presidente', 'secretario', 'vocal'];

        return view('users.delegaciones', compact(
            'user', 'assignments', 'institutions', 'votingTables',
            'delegateTypes', 'institutionOnlyTypes', 'mesaTypes'
        ));
    }

    public function addDelegacion(Request $request, User $user)
    {
        $this->requirePermission('assign_delegates');

        $request->validate([
            'delegate_type'    => 'required|in:delegado_general,delegado_mesa,presidente,secretario,vocal,tecnico,observador',
            'institution_id'   => 'required|exists:institutions,id',
            'voting_table_id'  => 'nullable|exists:voting_tables,id',
            'credential_number'=> 'nullable|string|max:50',
            'assignment_date'  => 'nullable|date',
            'expiration_date'  => 'nullable|date|after_or_equal:assignment_date',
            'observations'     => 'nullable|string|max:500',
        ]);

        $mesaTypes = ['delegado_mesa', 'presidente', 'secretario', 'vocal'];
        $needsMesa = in_array($request->delegate_type, $mesaTypes);
        $vtId      = $needsMesa ? $request->voting_table_id : null;

        if ($needsMesa && !$vtId) {
            return back()->withInput()->with('error', 'Este tipo de delegación requiere una mesa específica.');
        }
        $exists = UserAssignment::where('user_id', $user->id)
            ->where('institution_id', $request->institution_id)
            ->where('voting_table_id', $vtId)
            ->where('delegate_type', $request->delegate_type)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Ya existe una delegación idéntica para este usuario.');
        }

        UserAssignment::create([
            'user_id'          => $user->id,
            'institution_id'   => $request->institution_id,
            'voting_table_id'  => $vtId,
            'delegate_type'    => $request->delegate_type,
            'credential_number'=> $request->credential_number,
            'assignment_date'  => $request->assignment_date ?? now()->toDateString(),
            'expiration_date'  => $request->expiration_date,
            'status'           => 'activo',
            'assigned_by'      => Auth::id(),
            'observations'     => $request->observations,
        ]);

        return back()->with('success', 'Delegación agregada exitosamente.');
    }

    public function removeDelegacion(User $user, UserAssignment $assignment)
    {
        $this->requirePermission('assign_delegates');
        if ($assignment->user_id !== $user->id) {
            return back()->with('error', 'Delegación no encontrada.');
        }
        $assignment->delete();
        return back()->with('success', 'Delegación eliminada.');
    }

    private function getOrderedPermissions()
    {
        $groupOrder = [
            'Usuarios', 'Roles y Permisos', 'Recintos', 'Mesas de Votación',
            'Votos', 'Actas', 'Observaciones', 'Delegaciones',
            'Auditoría', 'Configuración', 'Dashboard',
        ];

        return Permission::orderBy('display_name')->get()
            ->groupBy('group')
            ->sortBy(fn($_, $g) => ($k = array_search($g, $groupOrder)) !== false ? $k : 99);
    }
}
