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
        'middlename',
        'lastname',
        'sex',
        'schoolyear',
        'quarter',
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


public function submittedMinorOffenses()
{
    return $this->hasMany(SubmittedMinorOffense::class, 'lrn', 'lrn');
}

public function submittedMinorOffensesWithNoSanction()
{
    return $this->submittedMinorOffenses()->where('sanction', 0);
}
public function submittedMinorOffensesWithSanction()
{
    return $this->submittedMinorOffenses()->where('sanction', 1);
}

public function submittedMajorOffenses()
{
    return $this->hasMany(SubmittedMajorOffense::class, 'lrn', 'lrn');
}

public function submittedMajorOffensesWithNoSanction()
{
    return $this->submittedMajorOffenses()->where('sanction', 0);
}
public function submittedMajorOffensesWithSanction()
{
    return $this->submittedMajorOffenses()->where('sanction', 1);
}

}
