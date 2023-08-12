@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('tilefiles.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="tilefile" class="col-sm-auto col-form-label">Tiles Data File</label>
                                <div class="col-sm">
                                    <input class="form-control" type="file" id="tilefile" name="tilefile">
                                </div>
                                <div class="col-sm-auto">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </form>
                        <table id="tileListTable" class="table table-hover text-nowrap nowrap cell-border table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="border-start-0">
                                        ID
                                    </th>
                                    <th scope="col">
                                        UID
                                    </th>
                                    <th scope="col">
                                        File Name
                                    </th>
                                    <th scope="col">
                                        Status
                                    </th>
                                    <th scope="col">
                                        Date Created
                                    </th>
                                    <th scope="col" class="border-end-0">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tilefiles as $tilefile)
                                    <tr>
                                        <td>
                                            {{ $tilefile->id }}
                                        </td>
                                        <td>
                                            {{ $tilefile->uid }}
                                        </td>
                                        <td>
                                            {{ $tilefile->name }}
                                        </td>
                                        <td>
                                            {{ $tilefile->status }}
                                        </td>
                                        <td>
                                            {{ $tilefile->created_at }}
                                        </td>
                                        <td>
                                            <a href="{{ route('tilefiles.upload', $tilefile) }}" class="btn btn-sm btn-primary" target="_blank">
                                                Upload Tile Photos
                                            </a>

                                            <a href="{{ route('tilefiles.download', $tilefile) }}" class="btn btn-sm btn-primary">
                                                Download
                                            </a>
                                            
                                            <form action="{{ route('tilefiles.destroy', $tilefile->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">
                                            No tilefiles found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
