<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $table = 'section'; // Ensure this is plural if following Laravel's convention.

    protected $fillable = [
        'section',
        'grade_id',
    ];

    // Define relationship with Grade
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }

    // Define relationship with Student
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'section_id', 'id');
    }
}
