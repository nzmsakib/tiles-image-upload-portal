<?php

namespace App\Http\Controllers;

use App\Models\Tilefile;
use Illuminate\Http\Request;
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
        $tilefiles = Tilefile::all();

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
        // Validate an excel file is uploaded
        $request->validate([
            'tilefile' => 'required|file|mimes:xlsx,xls',
        ]);

        // Get the file name
        $filename = $request->file('tilefile')->getClientOriginalName();
        $fileUid = uniqid();
        $fileExt = $request->file('tilefile')->getClientOriginalExtension();

        // Store the file
        $path = $request->file('tilefile')->storeAs('public/tilefiles/' . $fileUid, $filename);

        // Create a new tilefile record
        $tilefile = Tilefile::create([
            'name' => $filename,
            'uid' => $fileUid,
            'path' => $path,
            'user_id' => auth()->user()->id,
        ]);

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

        // Download the zip file
        return response()->download($zipPath);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tilefile  $tilefile
     * @return \Illuminate\Http\Response
     */
    public function upload(Tilefile $tilefile)
    {
        //
        // read the excel file
        $path = storage_path('app/' . $tilefile->path);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the column names
        $columnNames = [];
        foreach ($worksheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $columnNames[] = $cell->getValue();
            }
        }

        // Get the data
        $data = [];
        foreach ($worksheet->getRowIterator(2) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
        }

        // Get the number of rows and columns
        $numRows = count($data);
        $numCols = count($columnNames);


        return view('tilefiles.upload', compact('tilefile', 'columnNames', 'data', 'numRows', 'numCols'));
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
        }

        return redirect()->route('tilefiles.index')->with('status', 'Tile photos uploaded successfully');
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
        // Remove the excel file from storage first
        unlink(storage_path('app/' . $tilefile->path));

        // Delete the tilefile record
        $tilefile->delete();

        return redirect()->route('tilefiles.index')->with('status', 'Tilefile deleted successfully');
    }
}
