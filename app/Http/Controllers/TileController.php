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
        $image_type = $request->get('image_type') ?? 'image';

        foreach ($tile_images as $tile_image) {
            $ext = $tile_image->getClientOriginalExtension();
            $originalNameWithoutExtension = pathinfo($tile_image->getClientOriginalName(), PATHINFO_FILENAME);
            $filePath = 'tiles/tile_portal/' . $tile->tilefile->assignee->cid . '/' . $tile->tilefile->uid . '/' . $tile->size . '/' . $tile->finish . '/' . $tile->tilename . '/' . $image_type . '/' . $tile->tilename . ' F' . ($tile->imageCount() + 1) . '.' . $ext;
            // tiles/tile_portal/10040/wdvdwjchv/60x120/glossy/odg ultra brown/image/odg ultra brown F1.jpg

            $uploadSuccess = Storage::disk('s3')->put($filePath, file_get_contents($tile_image));

            if ($uploadSuccess) {
                // $fileLink = Storage::disk('s3')->get($filePath);
                // $headers = [
                //     'Content-Type' => 'application/pdf',
                //     'Content-Disposition' => 'attachment; filename="document.pdf"',
                //     'X-Message' => 'File uploaded successfully',
                // ];
                // return Response::streamDownload(function () use ($fileLink) {
                //     echo $fileLink;
                // }, 'document.pdf', $headers);
                Storage::disk('s3')->setVisibility($filePath, 'public');
                $upoaded_file = Storage::disk('s3')->url($filePath);
                $tile->files()->create([
                    'name' => $originalNameWithoutExtension,
                    'type' => $image_type,
                    'path' => $upoaded_file,
                    'extension' => $ext,
                ]);
            } else {
                return response()->json(['error' => 'Failed to upload file']);
            }


            // $ext = $tile_image->getClientOriginalExtension();
            // $uniqueName = uniqid('img-', true);
            // $tile_image->storeAs('public/files', $uniqueName . '.' . $ext);
            // $originalNameWithoutExtension = pathinfo($tile_image->getClientOriginalName(), PATHINFO_FILENAME);
            // $tile->files()->create([
            //     'name' => $originalNameWithoutExtension,
            //     'type' => 'image',
            //     'path' => $uniqueName . '.' . $ext,
            //     'extension' => $ext,
            // ]);
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
    public function destroyImages(Request $request, Tile $tile)
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
