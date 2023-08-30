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

    public function requiredImageCount($type = 'image')
    {
        $typeColumns = [
            'image' => 'tile_image_needed',
            'carving_map' => 'carving_map_needed',
            'bump_map' => 'bump_map_needed',
        ];
        return $this->tiles()->where($typeColumns[$type], true)->count();
    }

    public function completedImageCount($type = 'image')
    {
        $typeColumns = [
            'image' => 'tile_image_needed',
            'carving_map' => 'carving_map_needed',
            'bump_map' => 'bump_map_needed',
        ];
        return $this->tiles()->where($typeColumns[$type], true)->whereHas('files', function ($query) use ($type) {
            $query->where('type', $type);
        })->count();
    }
}
