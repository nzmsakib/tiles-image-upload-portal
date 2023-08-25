<?php

namespace App\Http\Controllers;

use App\Models\Tile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function show(Tile $tile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function edit(Tile $tile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tile $tile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function updateImages(Request $request, Tile $tile)
    {
        //
        $tile_images = $request->file('tile_images') ?? [];

        foreach ($tile_images as $tile_image) {
            $ext = $tile_image->getClientOriginalExtension();
            $uniqueName = uniqid('img-', true);
            $tile_image->storeAs('public/files', $uniqueName . '.' . $ext);
            $originalNameWithoutExtension = pathinfo($tile_image->getClientOriginalName(), PATHINFO_FILENAME);
            $tile->files()->create([
                'name' => $originalNameWithoutExtension,
                'type' => 'image',
                'path' => $uniqueName . '.' . $ext,
            ]);
        }
        return response()->json([
            'message' => 'Images updated successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function updateMaps(Request $request, Tile $tile)
    {
        //
        $tile_maps = $request->file('tile_maps') ?? [];

        foreach ($tile_maps as $tile_map) {
            $ext = $tile_map->getClientOriginalExtension();
            $uniqueName = uniqid('img-', true);
            $tile_map->storeAs('public/files', $uniqueName . '.' . $ext);
            $originalNameWithoutExtension = pathinfo($tile_map->getClientOriginalName(), PATHINFO_FILENAME);
            $tile->files()->create([
                'name' => $originalNameWithoutExtension,
                'type' => 'map',
                'path' => $uniqueName . '.' . $ext,
            ]);
        }
        return response()->json([
            'message' => 'Images updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tile $tile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tile  $tile
     * @return \Illuminate\Http\Response
     */
    public function destroyImageMap(Request $request, Tile $tile)
    {
        //
        $file_id = $request->get('key');
        $file = $tile->files()->find($file_id);
        if ($file) {
            Storage::delete('public/files/' . $file->path);
            $file->delete();
        } else {
            return response()->json([
                'error' => 'Image not found',
            ]);
        }

        return response()->json([
            'success' => 'Image deleted successfully',
        ]);
    }
}
