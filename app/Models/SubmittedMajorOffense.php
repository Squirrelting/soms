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
        'student_name',
        'student_grade',
        'major_offense_id',
        'major_penalty_id', // Include the penalty in the fillable fields
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'lrn', 'lrn');
    }

    /**
     * Get the major offense that owns the SubmittedMajorOffenses.
     */
    public function majorOffense(): BelongsTo
    {
        return $this->belongsTo(MajorOffense::class, 'major_offense_id', 'id');
    }

    public function majorPenalty(): BelongsTo
    {
        return $this->belongsTo(MajorPenalty::class, 'major_penalty_id', 'id');
    }
}
