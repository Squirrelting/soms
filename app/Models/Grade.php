<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'grade'; // Ensure this is plural if following Laravel's convention.

    protected $fillable = [
        'grade',
    ];

    // Define relationship with Section
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'grade_id', 'id');
    }
}
