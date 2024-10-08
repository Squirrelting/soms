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
        'minor_offense_id',
        'minor_penalty_id', // Include the penalty in the fillable fields

    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }

    /**
     * Get the minor offense that owns the SubmittedMinorOffenses.
     */
    public function minorOffense(): BelongsTo
    {
        return $this->belongsTo(MinorOffense::class, 'minor_offense_id', 'id');
    }

    public function minorPenalty(): BelongsTo
    {
        return $this->belongsTo(MinorPenalty::class, 'minor_penalty_id', 'id');
    }
}
