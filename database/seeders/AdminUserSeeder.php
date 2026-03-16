<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $adminRole = Role::where('name', 'administrador')->firstOrFail();
            $admins = [
                [
                    'name'              => 'Admin',
                    'last_name'         => 'User',
                    'email'             => 'admin@gmail.com',
                    'password'          => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    'avatar'            => 'avatar-admin-w.png',
                    'is_active'         => true,
                ],
                [
                    'name'              => 'Faby',
                    'last_name'         => 'Morales',
                    'email'             => 'moralessfaby.dev@gmail.com',
                    'password'          => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    'avatar'            => 'avatar-admin-w.png',
                    'is_active'         => true,
                ],
            ];

            $permissionIds = $adminRole->permissions()->pluck('permissions.id');
            $now           = now();

            foreach ($admins as $adminData) {
                $user = User::updateOrCreate(
                    ['email' => $adminData['email']],
                    array_merge($adminData, ['created_by' => null])
                );
                DB::table('role_user')->insertOrIgnore([
                    'role_id'    => $adminRole->id,
                    'user_id'    => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $rows = $permissionIds->map(fn ($permId) => [
                    'permission_id' => $permId,
                    'user_id'       => $user->id,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ])->values()->all();
                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table('permission_user')->insertOrIgnore($chunk);
                }
            }
        });
    }
}
