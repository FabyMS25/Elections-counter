<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    protected array $permissions = [
        // ── Usuarios ──────────────────────────────────────────────────────────
        ['name' => 'view_users',     'display_name' => 'Ver Usuarios',                 'group' => 'Usuarios'],
        ['name' => 'create_users',   'display_name' => 'Crear Usuarios',               'group' => 'Usuarios'],
        ['name' => 'edit_users',     'display_name' => 'Editar Usuarios',              'group' => 'Usuarios'],
        ['name' => 'delete_users',   'display_name' => 'Eliminar/Desactivar Usuarios', 'group' => 'Usuarios'],
        ['name' => 'activate_users', 'display_name' => 'Activar Usuarios',             'group' => 'Usuarios'],

        // ── Roles y Permisos ──────────────────────────────────────────────────
        ['name' => 'view_roles',         'display_name' => 'Ver Roles',                'group' => 'Roles y Permisos'],
        ['name' => 'create_roles',       'display_name' => 'Crear Roles',              'group' => 'Roles y Permisos'],
        ['name' => 'edit_roles',         'display_name' => 'Editar Roles',             'group' => 'Roles y Permisos'],
        ['name' => 'delete_roles',       'display_name' => 'Eliminar Roles',           'group' => 'Roles y Permisos'],
        ['name' => 'assign_roles',       'display_name' => 'Asignar Roles a Usuarios', 'group' => 'Roles y Permisos'],
        ['name' => 'view_permissions',   'display_name' => 'Ver Permisos',             'group' => 'Roles y Permisos'],
        ['name' => 'assign_permissions', 'display_name' => 'Asignar Permisos Directos','group' => 'Roles y Permisos'],

        // ── Recintos ──────────────────────────────────────────────────────────
        ['name' => 'view_recintos',  'display_name' => 'Ver Recintos',    'group' => 'Recintos'],
        ['name' => 'create_recinto', 'display_name' => 'Crear Recinto',   'group' => 'Recintos'],
        ['name' => 'edit_recinto',   'display_name' => 'Editar Recinto',  'group' => 'Recintos'],
        ['name' => 'delete_recinto', 'display_name' => 'Eliminar Recinto','group' => 'Recintos'],

        // ── Mesas de Votación ─────────────────────────────────────────────────
        ['name' => 'view_mesas',     'display_name' => 'Ver Mesas',        'group' => 'Mesas de Votación'],
        ['name' => 'create_mesa',    'display_name' => 'Crear Mesa',       'group' => 'Mesas de Votación'],
        ['name' => 'edit_mesa',      'display_name' => 'Editar Mesa',      'group' => 'Mesas de Votación'],
        ['name' => 'delete_mesa',    'display_name' => 'Eliminar Mesa',    'group' => 'Mesas de Votación'],
        ['name' => 'configure_mesa', 'display_name' => 'Configurar Mesa',  'group' => 'Mesas de Votación'],
        ['name' => 'close_table',    'display_name' => 'Cerrar Mesa',      'group' => 'Mesas de Votación'],
        ['name' => 'reopen_table',   'display_name' => 'Reabrir Mesa',     'group' => 'Mesas de Votación'],

        // ── Votos ─────────────────────────────────────────────────────────────
        ['name' => 'view_votes',     'display_name' => 'Ver Votos',       'group' => 'Votos'],
        ['name' => 'register_votes', 'display_name' => 'Registrar Votos', 'group' => 'Votos'],
        ['name' => 'observe_votes',  'display_name' => 'Observar Votos',  'group' => 'Votos'],
        ['name' => 'correct_votes',  'display_name' => 'Corregir Votos',  'group' => 'Votos'],
        ['name' => 'validate_votes', 'display_name' => 'Validar Votos',   'group' => 'Votos'],
        ['name' => 'export_votes',   'display_name' => 'Exportar Votos',  'group' => 'Votos'],

        // ── Actas ─────────────────────────────────────────────────────────────
        ['name' => 'view_actas',    'display_name' => 'Ver Actas',       'group' => 'Actas'],
        ['name' => 'upload_acta',   'display_name' => 'Subir Acta',      'group' => 'Actas'],
        ['name' => 'verify_actas',  'display_name' => 'Verificar Actas', 'group' => 'Actas'],
        ['name' => 'approve_actas', 'display_name' => 'Aprobar Actas',   'group' => 'Actas'],

        // ── Observaciones ─────────────────────────────────────────────────────
        ['name' => 'view_observations',   'display_name' => 'Ver Observaciones',    'group' => 'Observaciones'],
        ['name' => 'create_observation',  'display_name' => 'Crear Observación',    'group' => 'Observaciones'],
        ['name' => 'resolve_observation', 'display_name' => 'Resolver Observación', 'group' => 'Observaciones'],

        // ── Delegaciones ──────────────────────────────────────────────────────
        ['name' => 'view_assignments',   'display_name' => 'Ver Delegaciones',       'group' => 'Delegaciones'],
        ['name' => 'assign_delegates',   'display_name' => 'Asignar Delegados',      'group' => 'Delegaciones'],
        ['name' => 'manage_assignments', 'display_name' => 'Gestionar Delegaciones', 'group' => 'Delegaciones'],

        // ── Auditoría ─────────────────────────────────────────────────────────
        ['name' => 'view_audit_logs',         'display_name' => 'Ver Logs de Auditoría',        'group' => 'Auditoría'],
        ['name' => 'view_validation_history', 'display_name' => 'Ver Historial de Validaciones','group' => 'Auditoría'],

        // ── Configuración ─────────────────────────────────────────────────────
        ['name' => 'view_settings',         'display_name' => 'Ver Configuración',           'group' => 'Configuración'],
        ['name' => 'manage_settings',       'display_name' => 'Gestionar Configuración',     'group' => 'Configuración'],
        ['name' => 'manage_election_types', 'display_name' => 'Gestionar Tipos de Elección', 'group' => 'Configuración'],
        ['name' => 'manage_categories',     'display_name' => 'Gestionar Categorías',        'group' => 'Configuración'],

        // ── Dashboard ─────────────────────────────────────────────────────────
        ['name' => 'view_dashboard', 'display_name' => 'Ver Dashboard', 'group' => 'Dashboard'],
    ];
    protected array $roles = [
        // ── Administrador ─────────────────────────────────────────────────────
        'administrador' => [
            'display_name'  => 'Administrador del Sistema',
            'description'   => 'Control total del sistema sin restricciones',
            'permissions'   => 'ALL',
        ],
        // ── Supervisor ────────────────────────────────────────────────────────
        'supervisor' => [
            'display_name'  => 'Supervisor Electoral',
            'description'   => 'Valida votos, aprueba actas y resuelve observaciones',
            'permissions'   => [
                'view_recintos', 'view_mesas',
                'view_votes',    'validate_votes', 'observe_votes',
                'view_actas',    'verify_actas',   'approve_actas',
                'view_observations', 'create_observation', 'resolve_observation',
                'view_assignments',
                'close_table', 'reopen_table',
                'view_audit_logs', 'view_validation_history',
            ],
        ],
        // ── Registrador ───────────────────────────────────────────────────────
        'registrador' => [
            'display_name'  => 'Registrador de Votos',
            'description'   => 'Ingresa resultados de votación y sube actas',
            'permissions'   => [
                'view_recintos', 'view_mesas',
                'view_votes',    'register_votes', 'observe_votes',
                'view_actas',    'upload_acta',
                'view_observations', 'create_observation',
                'close_table',
            ],
        ],
        // ── Modificador ───────────────────────────────────────────────────────
        'modificador' => [
            'display_name'  => 'Modificador de Votos',
            'description'   => 'Corrige votos observados o impugnados',
            'permissions'   => [
                'view_recintos', 'view_mesas',
                'view_votes',    'correct_votes', 'observe_votes',
                'view_actas',
                'view_observations',
            ],
        ],

        // ── Observador ────────────────────────────────────────────────────────
        'observador' => [
            'display_name'  => 'Observador Electoral',
            'description'   => 'Solo puede ver resultados, sin modificar datos',
            'permissions'   => [
                'view_recintos', 'view_mesas',
                'view_votes',    'view_actas',
                'view_observations',
                'view_assignments',
            ],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedPermissions();
            $this->seedRoles();
        });
    }

    private function seedPermissions(): void
    {
        foreach ($this->permissions as $data) {
            Permission::updateOrCreate(
                ['name' => $data['name']],
                [
                    'display_name' => $data['display_name'],
                    'group'        => $data['group'],
                    'description'  => "Permiso para {$data['display_name']}",
                ]
            );
        }
        $validNames = array_column($this->permissions, 'name');
        $removed    = Permission::whereNotIn('name', $validNames)->get();
        foreach ($removed as $perm) {
            $this->command?->warn("  🗑  Removing obsolete permission: {$perm->name}");
            $perm->roles()->detach();
            DB::table('permission_user')->where('permission_id', $perm->id)->delete();
            $perm->delete();
        }
    }

    private function seedRoles(): void
    {
        foreach ($this->roles as $name => $data) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                [
                    'display_name'  => $data['display_name'],
                    'description'   => $data['description'],
                ]
            );

            if ($data['permissions'] === 'ALL') {
                $role->permissions()->sync(Permission::pluck('id'));
                $this->command?->info("  ✓  Role [{$name}] synced with ALL " . Permission::count() . " permissions");
            } else {
                $permIds  = Permission::whereIn('name', $data['permissions'])->pluck('id');
                $found    = Permission::whereIn('name', $data['permissions'])->pluck('name')->toArray();
                $missing  = array_diff($data['permissions'], $found);

                if (!empty($missing)) {
                    $this->command?->warn("  ⚠  Role [{$name}] missing: " . implode(', ', $missing));
                }

                $role->permissions()->sync($permIds);
                $this->command?->info("  ✓  Role [{$name}] synced with {$permIds->count()} permissions");
            }
        }

        $validNames = array_keys($this->roles);
        $removed    = Role::whereNotIn('name', $validNames)->get();
        foreach ($removed as $role) {
            $this->command?->warn("  🗑  Removing obsolete role: {$role->name}");
            $roleUserIds = DB::table('role_user')->where('role_id', $role->id)->pluck('id');
            DB::table('permission_user')->whereIn('role_user_id', $roleUserIds)->delete();
            DB::table('role_user')->where('role_id', $role->id)->delete();
            $role->permissions()->detach();
            $role->delete();
        }
    }
}
