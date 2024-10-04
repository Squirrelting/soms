<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'lrn',
        'firstname',
        'lastname',
        'sex',
        'grade_id',
        'section_id',
        'email',
    ];

    // Define relationship with Grade
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id', 'id');
    }

    // Define relationship with Section
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }

    // Define relationship with SubmittedMinorOffense
    public function submittedMinorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMinorOffense::class, 'lrn', 'lrn');
    }

    // Define relationship with SubmittedMajorOffense
    public function submittedMajorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMajorOffense::class, 'lrn', 'lrn');
    }
}
