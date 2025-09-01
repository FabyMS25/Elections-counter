<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotingTable extends Model
{
    use HasFactory;

    protected $fillable = ['code','number','from_name','to_name','capacity','status','institution_id'];
    protected $casts = ['capacity' => 'integer','number' => 'integer'];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PENDING = 'pending';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Activa',
            self::STATUS_CLOSED => 'Cerrada',
            self::STATUS_PENDING => 'Pendiente',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($votingTable) {
            if (empty($votingTable->code)) {
                $votingTable->code = self::generateUniqueCode();
            }
        });
    }

    protected static function generateUniqueCode(): string
    {
        $prefix = 'VT';
        $number = 1;
        
        $lastCode = self::orderBy('id', 'desc')->value('code');
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, 2);
            $number = $lastNumber + 1;
        }
        
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function manager()
    {
        return $this->hasOne(Manager::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

}
