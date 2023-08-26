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
                        <div class="table-responsive">
                            <table id="tilePhotoUploadTable"
                                class="table table-hover text-nowrap nowrap cell-border table-bordered caption-top">
                                <caption>
                                    <div class="text-center">
                                        Completed {{ $tilefile->completedImageCount() }} out of
                                        {{ $tilefile->requiredImageCount() }} Image(s)
                                    </div>
                                    <div class="text-center">
                                        Completed {{ $tilefile->completedMapCount() }} out of
                                        {{ $tilefile->requiredMapCount() }} Map(s)
                                    </div>
                                </caption>
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
                                            <td @class([
                                                'text-bg-danger' => $tile->tile_image_needed && $tile->imageCount() == 0,
                                                'text-bg-success' => $tile->tile_image_needed && $tile->imageCount() > 0,
                                            ])>
                                                {{ $tile->tile_image_needed ? 'Yes' : 'No' }}
                                            </td>
                                            <td @class([
                                                'text-bg-danger' => $tile->map_image_needed && $tile->mapCount() == 0,
                                                'text-bg-success' => $tile->map_image_needed && $tile->mapCount() > 0,
                                            ])>
                                                {{ $tile->map_image_needed ? 'Yes' : 'No' }}
                                                <a class="stretched-link" data-bs-toggle="collapse"
                                                    href="#row-collapse-{{ $tile->id }}" role="button"
                                                    aria-expanded="false"
                                                    aria-controls="row-collapse-{{ $tile->id }}"></a>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="row-collapse-{{ $tile->id }}">
                                            @if ($tile->tile_image_needed && $tile->map_image_needed)
                                                <td colspan="3">
                                                    <div class="text-center">
                                                        Upload Tile Image
                                                    </div>
                                                    <input type="file" class="bs-fileinput" multiple name="tile_images[]"
                                                        data-upload-url="{{ route('tiles.update.images', $tile) }}"
                                                        data-delete-url="{{ route('tiles.destroy.imagemap', $tile) }}"
                                                        data-initial-preview="{{ $tile->initialPreview() }}"
                                                        data-initial-preview-config="{{ $tile->initialPreviewConfig() }}" />
                                                </td>
                                                <td colspan="3">
                                                    <div class="text-center">
                                                        Upload Map Image
                                                    </div>
                                                    <input type="file" class="bs-fileinput" multiple name="tile_maps[]"
                                                        data-upload-url="{{ route('tiles.update.maps', $tile) }}"
                                                        data-delete-url="{{ route('tiles.destroy.imagemap', $tile) }}"
                                                        data-initial-preview="{{ $tile->initialPreview('map') }}"
                                                        data-initial-preview-config="{{ $tile->initialPreviewConfig('map') }}" />
                                                </td>
                                            @elseif ($tile->tile_image_needed)
                                                <td colspan="100%">
                                                    <div class="text-center">
                                                        Upload Tile Image
                                                    </div>
                                                    <input type="file" class="bs-fileinput" multiple name="tile_images[]"
                                                        data-upload-url="{{ route('tiles.update.images', $tile) }}"
                                                        data-delete-url="{{ route('tiles.destroy.imagemap', $tile) }}"
                                                        data-initial-preview="{{ $tile->initialPreview() }}"
                                                        data-initial-preview-config="{{ $tile->initialPreviewConfig() }}" />
                                                </td>
                                            @elseif ($tile->map_image_needed)
                                                <td colspan="100%">
                                                    <div class="text-center">
                                                        Upload Map Image
                                                    </div>
                                                    <input type="file" class="bs-fileinput" multiple name="tile_maps[]"
                                                        data-upload-url="{{ route('tiles.update.maps', $tile) }}"
                                                        data-delete-url="{{ route('tiles.destroy.imagemap', $tile) }}"
                                                        data-initial-preview="{{ $tile->initialPreview('map') }}"
                                                        data-initial-preview-config="{{ $tile->initialPreviewConfig('map') }}" />
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushOnce('foot')
    <x-scripts.bs-fileinput-common />
@endPushOnce
