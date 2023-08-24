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
                                            <tr class="position-relative">
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
                                                    {{ $tile->tile_image_needed }}
                                                </td>
                                                <td>
                                                    {{ $tile->map_image_needed }}
                                                </td>
                                                <a class="stretched-link" data-bs-toggle="collapse"
                                                    href="#row-collapse-{{ $tile->id }}" role="button"
                                                    aria-expanded="false"
                                                    aria-controls="row-collapse-{{ $tile->id }}"></a>
                                            </tr>
                                            <tr class="collapse bg-success" id="row-collapse-{{ $tile->id }}">
                                                @if ($tile->tile_image_needed && $tile->map_image_needed)
                                                    <td colspan="50%">
                                                        Tile Images
                                                    </td>
                                                    <td colspan="50%">
                                                        Map Images
                                                    </td>
                                                @elseif ($tile->tile_image_needed)
                                                    <td colspan="100%">
                                                        Tile Images
                                                    </td>
                                                @elseif ($tile->map_image_needed)
                                                    <td colspan="100%">
                                                        Map Images
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
