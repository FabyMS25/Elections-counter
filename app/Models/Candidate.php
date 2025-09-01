<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'party',
        'position',
        'party_logo',
        'photo',
        'color'
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('build/images/default-candidate.jpg');
    }

    public function getPartyLogoUrlAttribute()
    {
        return $this->party_logo ? asset('storage/' . $this->party_logo) : asset('build/images/default-party.png');
    }
}