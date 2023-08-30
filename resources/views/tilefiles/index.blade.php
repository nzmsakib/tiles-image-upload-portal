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
                        <div class="table-responsive">
                            <table id="tileListTable" class="table table-hover text-nowrap nowrap cell-border table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            ID
                                        </th>
                                        <th scope="col">
                                            UID
                                        </th>
                                        <th scope="col">
                                            File Name
                                        </th>
                                        <th scope="col">
                                            Images Uploaded
                                        </th>
                                        <th scope="col">
                                            Carving Maps Uploaded
                                        </th>
                                        <th scope="col">
                                            Bump Maps Uploaded
                                        </th>
                                        <th scope="col">
                                            Reference
                                        </th>
                                        <th scope="col">
                                            Date Created
                                        </th>
                                        <th scope="col">
                                            Created By
                                        </th>
                                        @role('admin')
                                            <th scope="col">
                                                Assigned To
                                            </th>
                                        @endrole
                                        <th scope="col">
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
                                            <td @class([
                                                'text-bg-danger' =>
                                                    $tilefile->requiredImageCount() > 0 &&
                                                    $tilefile->completedImageCount() == 0,
                                                'text-bg-warning' =>
                                                    $tilefile->requiredImageCount() > 0 &&
                                                    $tilefile->completedImageCount() > 0 &&
                                                    $tilefile->completedImageCount() < $tilefile->requiredImageCount(),
                                                'text-bg-success' =>
                                                    $tilefile->requiredImageCount() > 0 &&
                                                    $tilefile->completedImageCount() == $tilefile->requiredImageCount(),
                                            ])>
                                                {{ $tilefile->completedImageCount() }} out of
                                                {{ $tilefile->requiredImageCount() }}
                                            </td>
                                            <td @class([
                                                'text-bg-danger' =>
                                                    $tilefile->requiredImageCount('carving_map') > 0 &&
                                                    $tilefile->completedImageCount('carving_map') == 0,
                                                'text-bg-warning' =>
                                                    $tilefile->requiredImageCount('carving_map') > 0 &&
                                                    $tilefile->completedImageCount('carving_map') > 0 &&
                                                    $tilefile->completedImageCount('carving_map') < $tilefile->requiredImageCount('carving_map'),
                                                'text-bg-success' =>
                                                    $tilefile->requiredImageCount('carving_map') > 0 &&
                                                    $tilefile->completedImageCount('carving_map') == $tilefile->requiredImageCount('carving_map'),
                                            ])>
                                                {{ $tilefile->completedImageCount('carving_map') }} out of
                                                {{ $tilefile->requiredImageCount('carving_map') }}
                                            </td>
                                            <td @class([
                                                'text-bg-danger' =>
                                                    $tilefile->requiredImageCount('bump_map') > 0 &&
                                                    $tilefile->completedImageCount('bump_map') == 0,
                                                'text-bg-warning' =>
                                                    $tilefile->requiredImageCount('bump_map') > 0 &&
                                                    $tilefile->completedImageCount('bump_map') > 0 &&
                                                    $tilefile->completedImageCount('bump_map') < $tilefile->requiredImageCount('bump_map'),
                                                'text-bg-success' =>
                                                    $tilefile->requiredImageCount('bump_map') > 0 &&
                                                    $tilefile->completedImageCount('bump_map') == $tilefile->requiredImageCount('bump_map'),
                                            ])>
                                                {{ $tilefile->completedImageCount('bump_map') }} out of
                                                {{ $tilefile->requiredImageCount('bump_map') }}
                                            </td>
                                            <td>
                                                {{ $tilefile->reference }}
                                            </td>
                                            <td>
                                                {{ $tilefile->created_at }}
                                            </td>
                                            <td>
                                                {{ $tilefile->creator->name ?? '' }}
                                            </td>
                                            @role('admin')
                                                <td>
                                                    {{ $tilefile->assignee->name ?? 'Not Assigned' }}
                                                </td>
                                            @endrole
                                            <td>
                                                <a href="{{ route('tilefiles.upload', $tilefile) }}"
                                                    class="btn btn-sm btn-primary" target="_blank">
                                                    Upload Tile Photos
                                                </a>

                                                @role('admin')
                                                    <a href="{{ route('tilefiles.zip', $tilefile) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Create ZIP
                                                    </a>

                                                    <form action="{{ route('tilefiles.destroy', $tilefile->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                @endrole
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
                        {{ $tilefiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
