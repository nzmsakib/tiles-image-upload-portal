<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = User::find(auth()->user()->id);
        $users = User::query();

        if (request()->has('role') && request('role') != '' && request('role') != 'all') {
            $users = $users->role(request('role'));
        }

        $users = $users->orderBy('id', 'desc')->paginate(10);
        
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.index', compact('users', 'roles'));
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        //
        // dd($request);
        $companyDataFile = $request->file('userfile');

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($companyDataFile);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            \App\Models\User::factory()->create([
                'name' => $rowData[2],
                'email' => $rowData[1],
                'cid' => $rowData[0],
            ])->assignRole($rowData[3] ?? 'user');
        }

        return redirect()->back()->with('status', 'Users imported successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        // dd($request->all());
        if ($request->name != $user->name) {
            $user->name = $request->name;
        }

        if ($request->email != $user->email) {
            $user->email = $request->email;
        }

        if ($request->cid != $user->cid) {
            $user->cid = $request->cid;
        }

        if ($request->password != '') {
            $user->password = Hash::make($request->password);
        }
        
        $roles = $request->roles ?? [];
        $user->syncRoles($roles);

        $user->save();
        return redirect()->back()->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
