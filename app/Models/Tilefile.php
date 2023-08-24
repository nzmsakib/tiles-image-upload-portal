<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tilefile extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', // This is the user who uploaded the file
        'assigned_to', // This is the user who is assigned to process the file
        'name',
        'path',
        'uid',
        'status',
        'reference',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tiles()
    {
        return $this->hasMany(Tile::class);
    }
}
