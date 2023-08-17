<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tilefile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // This is the user who uploaded the file
        'name',
        'path',
        'uid',
        'status',
        'reference',
    ];
}
