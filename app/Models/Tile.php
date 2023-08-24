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
        'tile_images',
        'map_image_needed',
        'map_images',
    ];

    public function tilefile()
    {
        return $this->belongsTo(Tilefile::class);
    }
}
