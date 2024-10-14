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
        'student_firstname',
        'student_lastname',
        'student_grade',
        'student_section',
        'student_sex',
        'sanction',
        'cleansed_date',
        'major_offense',
        'major_penalty', 
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }
}
