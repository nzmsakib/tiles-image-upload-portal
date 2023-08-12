<?php

namespace App\Http\Controllers;

use App\Models\Tilefile;
use Illuminate\Http\Request;

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
        $path = $request->file('tilefile')->storeAs('public/tilefiles', $fileUid . '.' . $fileExt);

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
