<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'last_name', 'id_card', 'email', 'phone', 'address',
        'password', 'avatar', 'is_active', 'last_login_at', 'last_login_ip',
        'created_by', 'updated_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active'         => 'boolean',
        'last_login_at'     => 'datetime',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }  
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(UserAssignment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewedObservations(): HasMany
    {
        return $this->hasMany(Observation::class, 'reviewed_by');
    }

    public function resolvedObservations(): HasMany
    {
        return $this->hasMany(Observation::class, 'resolved_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function hasPermission(string $permission): bool
    {
        if (!isset($this->_permCache)) {
            $this->_permCache = DB::table('permission_user')
                ->join('permissions', 'permissions.id', '=', 'permission_user.permission_id')
                ->where('permission_user.user_id', $this->id)
                ->pluck('permissions.name')
                ->flip()
                ->toArray();
        }
        return isset($this->_permCache[$permission]);
    }

    public function hasDelegationFor(int $institutionId, ?int $votingTableId = null): bool
    {
        $query = $this->assignments()
            ->where('institution_id', $institutionId)
            ->where('status', 'activo')
            ->whereNull('deleted_at');

        if ($votingTableId) {
            $query->where('voting_table_id', $votingTableId);
        }

        return $query->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $roleName));
    }

    public function scopeByRecinto($query, int $institutionId)
    {
        return $query->whereHas('assignments', fn($q) =>
            $q->where('institution_id', $institutionId)->where('status', 'activo')
        );
    }

    public function scopeByMesa($query, int $votingTableId)
    {
        return $query->whereHas('assignments', fn($q) =>
            $q->where('voting_table_id', $votingTableId)->where('status', 'activo')
        );
    }

    protected static function booted(): void
    {
        static::saved(function ($user) {
            unset($user->_permCache);
        });
    }
}
