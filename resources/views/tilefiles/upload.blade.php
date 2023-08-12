@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('tilefiles.upload.store', $tilefile) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <table id="tilePhotoUploadTable"
                                class="table table-hover text-nowrap nowrap cell-border table-bordered">
                                <thead>
                                    @foreach ($columnNames as $item)
                                        <th scope="col">
                                            {{ $item }}
                                        </th>
                                    @endforeach
                                    <th scope="col">
                                        Actions
                                    </th>
                                </thead>
                                <tbody>
                                    @forelse ($data as $tilePhotoUpload)
                                        <tr>
                                            @foreach ($columnNames as $item)
                                                <td>
                                                    {{ $tilePhotoUpload[$loop->index] }}
                                                </td>
                                            @endforeach
                                            <td class="d-flex gap-2">
                                                <input type="file" name="tileimage-{{ $tilePhotoUpload[0] }}" class="form-control form-control-sm">
                                                <input type="file" name="tilemap-{{ $tilePhotoUpload[0] }}" class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                No records found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
