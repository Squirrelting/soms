<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmittedMajorOffense extends Model
{
    use HasFactory;

    protected $table = 'submitted_major_offenses';

    protected $fillable = [
        'lrn',
        'student_grade',
        'student_section',
        'student_sex',
        'student_schoolyear',
        'student_quarter',
        'recorded_by',
        'committed_date',
        'sanction',
        'cleansed_by',
        'cleansed_date',
        'major_offense',
        'major_penalty', 
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }
}
