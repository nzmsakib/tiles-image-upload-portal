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
        
        $tilefiles = $tilefiles->paginate(2);

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
        $assinee_id = $request->user;
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
                'assigned_to' => $assinee_id,
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

        return redirect()->route('tilefiles.index')->with('status', 'Tilefile uploaded successfully');
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
    public function download(Tilefile $tilefile)
    {
        //
        // read the excel file
        $path = storage_path('app/' . $tilefile->path);
        $dir = dirname($path);
        $zipPath = $dir . '/' . $tilefile->uid . '.zip';

        // Create a zip archive
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

            // Add all the files in the directory
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();

                    // do not add any files inside the root directory
                    if (realpath(dirname($filePath)) == realpath(dirname($zipPath))) {
                        continue;
                    }

                    $relativePath = substr($filePath, strlen($dir) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
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
        $perPage = 2;

        $tiles = $tilefile->tiles()->paginate($perPage);

        return view('tilefiles.upload', compact('tilefile', 'tiles'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function uploadStore(Request $request, Tilefile $tilefile)
    {
        //
        // read the excel file
        $path = storage_path('app/' . $tilefile->path);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $totalRequired = 0;
        $totalUploaded = 0;

        // Get the data
        foreach ($worksheet->getRowIterator(2) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            if ($request->hasFile('tileimage-' . $rowData[0])) {
                $ext = $request->file('tileimage-' . $rowData[0])->getClientOriginalExtension();
                $request->file('tileimage-' . $rowData[0])->storeAs('public/tilefiles/' . $tilefile->uid . '/' . $tilefile->uid . '/' . $rowData[2] . '/' . $rowData[3] . '/' . $rowData[1], $rowData[1] . '.' . $ext);
            }
            if ($request->hasFile('tilemap-' . $rowData[0])) {
                $ext = $request->file('tilemap-' . $rowData[0])->getClientOriginalExtension();
                $request->file('tilemap-' . $rowData[0])->storeAs('public/tilefiles/' . $tilefile->uid . '/' . $tilefile->uid . '/' . $rowData[2] . '/' . $rowData[3] . '/' . $rowData[1] . '/MAPS', $rowData[1] . '.' . $ext);
            }
            $tileImagePath = storage_path('app/public/tilefiles/' . $tilefile->uid . '/' . $tilefile->uid . '/' . $rowData[2] . '/' . $rowData[3] . '/' . $rowData[1] . '/' . $rowData[1] . '.jpg');
            $tileMapsPath = storage_path('app/public/tilefiles/' . $tilefile->uid . '/' . $tilefile->uid . '/' . $rowData[2] . '/' . $rowData[3] . '/' . $rowData[1] . '/MAPS/' . $rowData[1] . '.jpg');
            if (file_exists($tileImagePath)) {
                $totalUploaded++;
            }
            if (file_exists($tileMapsPath)) {
                $totalUploaded++;
            }
            if ($rowData[count($rowData)-2] == 'Yes') {
                $totalRequired++;
            }
            if ($rowData[count($rowData)-1] == 'Yes') {
                $totalRequired++;
            }
        }

        if ($totalUploaded < $totalRequired) {
            $tilefile->status = 'processing';
        } else {
            $tilefile->status = 'completed';
        }
        $tilefile->save();

        return redirect()->back()->with('status', 'Tile photos uploaded successfully');
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
