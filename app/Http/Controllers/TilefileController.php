<?php

namespace App\Http\Controllers;

use App\Models\Tilefile;
use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use League\Flysystem\ZipArchive\FilesystemZipArchiveProvider;
use ZipArchive;

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
                    'carving_map_needed' => $rowData[5] == 'Yes' ? true : false,
                    'bump_map_needed' => $rowData[6] == 'Yes' ? true : false,
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
        // Initialize S3 client
        $s3 = new S3Client([
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID', 'your-aws-key'),
                'secret' => env('AWS_SECRET_ACCESS_KEY', 'your-aws-secret'),
            ],
        ]);

        // Fetch list of objects (files) in the S3 bucket and prefix
        $objects = $s3->listObjects([
            'Bucket' => env('AWS_BUCKET', 'your-bucket-name'),
            'Prefix' => 'tiles/tile_portal/' . $tilefile->assignee->cid . '/' . $tilefile->uid . '/',
        ]);

        // Create a temporary zip file
        $zipFileName = "{$tilefile->uid}.zip";
        $zipFilePath = storage_path("app/{$zipFileName}");
        $zip = new ZipArchive;
        $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($objects['Contents'] as $object) {
            // Get the relative path within the bucket
            $relativePath = substr($object['Key'], strlen("tiles/tile_portal/{$tilefile->assignee->cid}/{$tilefile->uid}/"));

            // Get the file content from S3
            $fileContent = $s3->getObject([
                'Bucket' => env('AWS_BUCKET', 'your-bucket-name'),
                'Key' => $object['Key'],
            ])['Body'];

            // Add the file to the zip archive with its relative path
            $zip->addFromString($relativePath, $fileContent);
        }

        $zip->close();

        // Send the zip file as a response
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
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
