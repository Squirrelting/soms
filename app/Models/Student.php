<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'lrn',
        'name',
        'sex',
        'grade',
        'email'
    ];

    public function submittedMinorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMinorOffenses::class, 'lrn', 'lrn');
    }

    public function submittedMajorOffenses(): HasMany
    {
        return $this->hasMany(SubmittedMajorOffenses::class, 'lrn', 'lrn');
    }


}
