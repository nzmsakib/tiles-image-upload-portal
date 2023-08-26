<?php

namespace App\Http\Controllers;

use App\Models\Tilefile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class TilefileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = User::find(auth()->user()->id);
        $tilefiles = $user->created_tilefiles();
        if ($user->hasRole('company')) {
            $tilefiles = $user->assigned_tilefiles();
        }

        if (request()->has('assignee')) {
            $tilefiles = $tilefiles->where('assigned_to', request()->assignee);
        }

        if (request()->has('creator')) {
            $tilefiles = $tilefiles->where('created_by', request()->creator);
        }
        
        $tilefiles = $tilefiles->orderBy('created_at', 'desc')->paginate(2);

        return view('tilefiles.index', compact('tilefiles'));
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
        // dd($request);
        $assignee_id = $request->user;
        $tilefiles = $request->file('tilefiles');

        foreach ($tilefiles as $tf) {
            // Get the file name
            $filename = $tf->getClientOriginalName();
            $fileUid = uniqid();
            $fileExt = $tf->getClientOriginalExtension();

            // Store the file
            $path = $tf->storeAs('public/tilefiles/' . $fileUid, $filename);

            $user = User::find(auth()->user()->id);
            // Create a new tilefile record
            $tilefile = Tilefile::create([
                'name' => $filename,
                'uid' => $fileUid,
                'path' => $path,
                'created_by' => $user->id,
                'assigned_to' => $assignee_id,
                'reference' => $request->reference ?? null,
            ]);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($tf);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $tile = $tilefile->tiles()->create([
                    'serial' => $rowData[0],
                    'tilename' => $rowData[1],
                    'size' => $rowData[2],
                    'finish' => $rowData[3],
                    'tile_image_needed' => $rowData[4] == 'Yes' ? true : false,
                    'map_image_needed' => $rowData[5] == 'Yes' ? true : false,
                ]);
            }
        }

        return redirect()->back()->with('status', 'Tilefile uploaded successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function show(Tilefile $tilefile)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function zip(Tilefile $tilefile)
    {
        //
        // read the excel file
        // dd($tilefile);
        $path = storage_path('app/' . $tilefile->path);
        $dir = dirname($path);
        $zipPath = $dir . '/' . $tilefile->uid . '.zip';

        // Create a zip archive
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $tiles = $tilefile->tiles()->get();

            foreach ($tiles as $tile) {
                $tilePath = $dir . '/' . $tile->size . '/' . $tile->finish . '/' . $tile->tilename;
                $zip->addEmptyDir($tile->size . '/' . $tile->finish . '/' . $tile->tilename);
                $tileImages = $tile->files()->where('type', 'image')->get();
                $n = 1;
                foreach ($tileImages as $tileImage) {
                    $imgFile = storage_path('app/public/files/' . $tileImage->path);
                    $zip->addFile($imgFile, $tile->size . '/' . $tile->finish . '/' . $tile->tilename . '/' . $tile->tilename . ' F' . $n . '.' . $tileImage->extension);
                    $n++;
                }
                $mapImages = $tile->files()->where('type', 'map')->get();
                $n = 1;
                foreach ($mapImages as $mapImage) {
                    $mapFile = storage_path('app/public/files/' . $mapImage->path);
                    $zip->addFile($mapFile, $tile->size . '/' . $tile->finish . '/' . $tile->tilename . '/MAPS/' . $tile->tilename . ' F' . $n . '.' . $mapImage->extension);
                    $n++;
                }
            }

            $zip->close();
        }

        return redirect()->back()->with('status', 'Tilefile ZIP created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, Tilefile $tilefile)
    {
        //
        $perPage = 10;

        $tiles = $tilefile->tiles()->paginate($perPage);

        return view('tilefiles.upload', compact('tilefile', 'tiles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function edit(Tilefile $tilefile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tilefile $tilefile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tilefile $tilefile)
    {
        //
        // Delete the directory created for this tilefile
        Storage::deleteDirectory('public/tilefiles/' . $tilefile->uid);

        // Delete the tilefile record
        $tilefile->delete();

        return redirect()->route('tilefiles.index')->with('status', 'Tilefile deleted successfully');
    }
}
