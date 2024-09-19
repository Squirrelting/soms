<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinorOffense extends Model
{
    use HasFactory;

    protected $table = 'minor_offenses';

    protected $fillable = [
        'minor_offenses'
    ];

    public function submittedMinorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMinorOffenses::class, 'minor_offense_id', 'id');
    }

}
