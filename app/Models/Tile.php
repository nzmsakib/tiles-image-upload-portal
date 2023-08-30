<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tile extends Model
{
    use HasFactory;

    protected $fillable = [
        'tilefile_id',
        'serial',
        'tilename',
        'size',
        'finish',
        'tile_image_needed',
        'carving_map_needed',
        'bump_map_needed',
    ];

    public function tilefile()
    {
        return $this->belongsTo(Tilefile::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function initialPreview($type = 'image')
    {
        $paths = $this->files()->where('type', $type)->get()->map(function ($file) {
            return $file->path;
        });
        return $paths;
    }

    public function initialPreviewConfig($type = 'image')
    {
        $configs = $this->files()->where('type', $type)->get()->map(function ($file) {
            return [
                'caption' => $file->name,
                'size' => $file->size,
                'url' => route('tiles.destroy.images', $this),
                'key' => $file->id,
            ];
        });
        return $configs;
    }

    public function imageCount($type = 'image')
    {
        return $this->files()->where('type', $type)->count();
    }
}
