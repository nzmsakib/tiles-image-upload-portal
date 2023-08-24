@extends('layouts.app')

@pushOnce('head')
    <x-scripts.bs-fileinput-config />
@endPushOnce

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

                        <form action="{{ route('tilefiles.upload.store', $tilefile) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="table-responsive">
                                <table id="tilePhotoUploadTable"
                                    class="table table-hover text-nowrap nowrap cell-border table-bordered">
                                    <thead>
                                        <th scope="col">
                                            Serial Number
                                        </th>
                                        <th scope="col">
                                            Tile Name
                                        </th>
                                        <th scope="col">
                                            Tile Size
                                        </th>
                                        <th scope="col">
                                            Tile Finish
                                        </th>
                                        <th scope="col">
                                            Tile Image Needed
                                        </th>
                                        <th scope="col">
                                            Map Image Needed
                                        </th>
                                    </thead>
                                    <tbody>
                                        @forelse ($tiles as $tile)
                                            <tr @class(["position-relative", "text-bg-danger" => $tile->tile_images != ''])>
                                                <td>
                                                    {{ $tile->serial }}
                                                </td>
                                                <td>
                                                    {{ $tile->tilename }}
                                                </td>
                                                <td>
                                                    {{ $tile->size }}
                                                </td>
                                                <td>
                                                    {{ $tile->finish }}
                                                </td>
                                                <td>
                                                    {{ $tile->tile_image_needed ? 'Yes' : 'No' }}
                                                </td>
                                                <td>
                                                    {{ $tile->map_image_needed ? 'Yes' : 'No' }}
                                                    <a class="stretched-link"
                                                        data-bs-toggle="collapse" href="#row-collapse-{{ $tile->id }}"
                                                        role="button" aria-expanded="false"
                                                        aria-controls="row-collapse-{{ $tile->id }}"></a>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="row-collapse-{{ $tile->id }}">
                                                @if ($tile->tile_image_needed && $tile->map_image_needed)
                                                    <td colspan="3">
                                                        <div class="text-center">
                                                            Upload Tile Image
                                                        </div>
                                                        <input type="file" class="bs-fileinput" multiple />
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="text-center">
                                                            Upload Map Image
                                                        </div>
                                                        <input type="file" class="bs-fileinput" multiple />
                                                    </td>
                                                @elseif ($tile->tile_image_needed)
                                                    <td colspan="100%">
                                                        <div class="text-center">
                                                            Upload Tile Image
                                                        </div>
                                                        <input type="file" class="bs-fileinput" multiple />
                                                    </td>
                                                @elseif ($tile->map_image_needed)
                                                    <td colspan="100%">
                                                        <div class="text-center">
                                                            Upload Map Image
                                                        </div>
                                                        <input type="file" class="bs-fileinput" multiple />
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
                            {{ $tiles->links() }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushOnce('foot')
    <x-scripts.bs-fileinput-common />
@endPushOnce
