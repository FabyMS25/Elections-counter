<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locality  extends Model
{
    protected $fillable = ['name', 'municipality_id', 'latitude', 'longitude'];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }
    
    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }
}

