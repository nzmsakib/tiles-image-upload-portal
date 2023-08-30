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
                                        Completed {{ $tilefile->completedImageCount('carving_map') }} out of
                                        {{ $tilefile->requiredImageCount('carving_map') }} Carving Map(s)
                                    </div>
                                    <div class="text-center">
                                        Completed {{ $tilefile->completedImageCount('bump_map') }} out of
                                        {{ $tilefile->requiredImageCount('bump_map') }} Bump Map(s)
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
                                        Carving Map Needed
                                    </th>
                                    <th scope="col">
                                        Bump Map Needed
                                    </th>
                                </thead>
                                <tbody>
                                    @forelse ($tiles as $tile)
                                        <tr>
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
                                                'cursor-pointer' => $tile->tile_image_needed,
                                                'text-bg-danger' => $tile->tile_image_needed && $tile->imageCount() == 0,
                                                'text-bg-success' => $tile->tile_image_needed && $tile->imageCount() > 0,
                                            ]) data-bs-toggle="modal"
                                                data-bs-target="#imageModal-{{ $tile->id }}">
                                                {{ $tile->tile_image_needed ? 'Yes' : 'No' }}
                                            </td>
                                            <td @class([
                                                'cursor-pointer' => $tile->carving_map_needed,
                                                'text-bg-danger' => $tile->carving_map_needed && $tile->imageCount('carving_map') == 0,
                                                'text-bg-success' => $tile->carving_map_needed && $tile->imageCount('carving_map') > 0,
                                            ]) data-bs-toggle="modal"
                                                data-bs-target="#carvingMapModal-{{ $tile->id }}">
                                                {{ $tile->carving_map_needed ? 'Yes' : 'No' }}
                                            </td>
                                            <td @class([
                                                'cursor-pointer' => $tile->bump_map_needed,
                                                'text-bg-danger' => $tile->bump_map_needed && $tile->imageCount('bump_map') == 0,
                                                'text-bg-success' => $tile->bump_map_needed && $tile->imageCount('bump_map') > 0,
                                            ]) data-bs-toggle="modal"
                                                data-bs-target="#bumpMapModal-{{ $tile->id }}">
                                                {{ $tile->bump_map_needed ? 'Yes' : 'No' }}
                                            </td>
                                        </tr>

                                        <!-- Modal -->
                                        <div class="modal fade" id="imageModal-{{ $tile->id }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="imageModal-{{ $tile->id }}Label" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header justify-content-center">
                                                        <h1 class="modal-title fs-5" id="imageModal-{{ $tile->id }}Label">
                                                            Upload Image for <b>[{{ $tile->tilefile->uid }}/{{ $tile->size }}/{{ $tile->finish }}/{{ $tile->tilename }}]</b>
                                                        </h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" class="bs-fileinput" multiple
                                                            name="tile_images[]" data-type="image"
                                                            data-upload-url="{{ route('tiles.update.images', $tile) }}"
                                                            data-delete-url="{{ route('tiles.destroy.images', $tile) }}"
                                                            data-initial-preview="{{ $tile->initialPreview() }}"
                                                            data-initial-preview-config="{{ $tile->initialPreviewConfig() }}" />
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="carvingMapModal-{{ $tile->id }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="carvingMapsModal-{{ $tile->id }}Label" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header justify-content-center">
                                                        <h1 class="modal-title fs-5" id="carvingMapsModal-{{ $tile->id }}Label">
                                                            Upload Carving Map for <b>[{{ $tile->tilefile->uid }}/{{ $tile->size }}/{{ $tile->finish }}/{{ $tile->tilename }}]</b>
                                                        </h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" class="bs-fileinput" multiple
                                                            name="tile_images[]" data-type="carving_map"
                                                            data-upload-url="{{ route('tiles.update.images', $tile) }}"
                                                            data-delete-url="{{ route('tiles.destroy.images', $tile) }}"
                                                            data-initial-preview="{{ $tile->initialPreview('carving_map') }}"
                                                            data-initial-preview-config="{{ $tile->initialPreviewConfig('carving_map') }}" />
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="bumpMapModal-{{ $tile->id }}"
                                            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                            aria-labelledby="bumpMapsModal-{{ $tile->id }}Label" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header justify-content-center">
                                                        <h1 class="modal-title fs-5" id="bumpMapsModal-{{ $tile->id }}Label">
                                                            Upload Bump Map for <b>[{{ $tile->tilefile->uid }}/{{ $tile->size }}/{{ $tile->finish }}/{{ $tile->tilename }}]</b>
                                                        </h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="file" class="bs-fileinput" multiple
                                                            name="tile_images[]" data-type="bump_map"
                                                            data-upload-url="{{ route('tiles.update.images', $tile) }}"
                                                            data-delete-url="{{ route('tiles.destroy.images', $tile) }}"
                                                            data-initial-preview="{{ $tile->initialPreview('bump_map') }}"
                                                            data-initial-preview-config="{{ $tile->initialPreviewConfig('bump_map') }}" />
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
