<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'province_id',
        'municipality_id',
        'district_id',
        'zone_id',
        'active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode()
    {
        $prefix = 'INST';
        $maxId = static::max('id') ?? 0;
        $nextId = $maxId + 1;
        return $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function votingTables()
    {
        return $this->hasMany(VotingTable::class);
    }


    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}