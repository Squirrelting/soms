<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signatory extends Model
{
    use HasFactory;

    protected $table = 'signatory';

    protected $fillable = [
        'name',
        'position'
    ];

}
