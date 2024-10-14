<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SubmittedMinorOffense extends Model
{
    use HasFactory;

    protected $table = 'submitted_minor_offenses';

    protected $fillable = [
        'lrn',
        'student_firstname',
        'student_lastname',
        'student_grade',
        'student_section',
        'student_sex',
        'sanction',
        'cleansed_date',
        'minor_offense',
        'minor_penalty',

    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }
}
