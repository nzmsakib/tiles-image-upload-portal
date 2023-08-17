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
                            <div class="table-responsive">
                                <table id="tilePhotoUploadTable"
                                    class="table table-hover text-nowrap nowrap cell-border table-bordered">
                                    <thead>
                                        @foreach ($columnNames as $item)
                                            <th scope="col">
                                                {{ $item }}
                                            </th>
                                        @endforeach
                                        <th scope="col">
                                            Tile Image
                                        </th>
                                        <th scope="col">
                                            Map Image
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
                                                @if ($tilePhotoUpload[count($columnNames)])
                                                    <td class="text-bg-success bg-opacity-25">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between gap-2">
                                                            @if ($tilePhotoUpload[count($columnNames) - 2] == 'Yes')
                                                                <a href="{{ $tilePhotoUpload[count($columnNames)] }}"
                                                                    target="_blank">
                                                                    <img src="{{ $tilePhotoUpload[count($columnNames)] }}"
                                                                        alt="tileimage" height="60" class="preview">
                                                                </a>

                                                                <a href="{{ asset('images/upload.png') }}"
                                                                    class="upload-link">
                                                                    <img src="{{ asset('images/upload.png') }}"
                                                                        alt="upload btn" width="30">
                                                                </a>
                                                                <input type="file" accept=".jpg"
                                                                    name="tileimage-{{ $tilePhotoUpload[0] }}"
                                                                    class="form-control form-control-sm d-none">
                                                            @endif
                                                        </div>
                                                    </td>
                                                @else
                                                    <td @class(['position-relative', 'text-bg-danger bg-opacity-25' => $tilePhotoUpload[count($columnNames) - 2] == 'Yes'])>
                                                        <div
                                                            class="d-flex align-items-center justify-content-center gap-2">
                                                            @if ($tilePhotoUpload[count($columnNames) - 2] == 'Yes')
                                                                <a href="#" target="_blank">
                                                                    <img src="#" alt="tileimage" height="60" class="preview">
                                                                </a>

                                                                <a href="{{ asset('images/upload.png') }}"
                                                                    class="upload-link stretched-link">
                                                                    <img src="{{ asset('images/upload.png') }}"
                                                                        alt="upload btn" width="30">
                                                                </a>
                                                                <input type="file" accept=".jpg"
                                                                    name="tileimage-{{ $tilePhotoUpload[0] }}"
                                                                    class="form-control form-control-sm d-none">
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif

                                                @if ($tilePhotoUpload[count($columnNames) + 1])
                                                    <td class="text-bg-success bg-opacity-25">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between gap-2">
                                                            @if ($tilePhotoUpload[count($columnNames) - 1] == 'Yes')
                                                                <a href="{{ $tilePhotoUpload[count($columnNames) + 1] }}"
                                                                    target="_blank">
                                                                    <img src="{{ $tilePhotoUpload[count($columnNames) + 1] }}"
                                                                        alt="tilemap" height="60" class="preview">
                                                                </a>

                                                                <a href="{{ asset('images/upload.png') }}"
                                                                    class="upload-link">
                                                                    <img src="{{ asset('images/upload.png') }}"
                                                                        alt="upload btn" width="30">
                                                                </a>
                                                                <input type="file" accept=".jpg"
                                                                    name="tilemap-{{ $tilePhotoUpload[0] }}"
                                                                    class="form-control form-control-sm d-none">
                                                            @endif
                                                        </div>
                                                    </td>
                                                @else
                                                    <td @class(['position-relative', 'text-bg-danger bg-opacity-25' => $tilePhotoUpload[count($columnNames) - 1] == 'Yes'])>
                                                        <div
                                                            class="d-flex align-items-center justify-content-center gap-2">
                                                            @if ($tilePhotoUpload[count($columnNames) - 1] == 'Yes')
                                                                <a href="#" target="_blank">
                                                                    <img src="#" alt="tilemap" height="60" class="preview">
                                                                </a>

                                                                <a href="{{ asset('images/upload.png') }}"
                                                                    class="upload-link stretched-link">
                                                                    <img src="{{ asset('images/upload.png') }}"
                                                                        alt="upload btn" width="30">
                                                                </a>
                                                                <input type="file" accept=".jpg"
                                                                    name="tilemap-{{ $tilePhotoUpload[0] }}"
                                                                    class="form-control form-control-sm d-none">
                                                            @endif
                                                        </div>
                                                    </td>
                                                @endif
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
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                            {{ $data->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
