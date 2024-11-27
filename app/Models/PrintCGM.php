<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintCGM extends Model
{
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('F d, Y');
    }
    
    use HasFactory;

    protected $table = 'print_cgm';

    protected $fillable = [
        'lrn',
        'firstname',
        'middlename',
        'lastname',
        'generated_by',
        'signatory',
        'position',
    ];

}
