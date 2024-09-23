<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MajorPenalty extends Model
{
    use HasFactory;

    protected $table = 'major_penalties';

    protected $fillable = [
        'major_penalties'
    ];

    public function submittedMajorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMajorOffense::class, 'major_penalty_id', 'id');
    }
}
