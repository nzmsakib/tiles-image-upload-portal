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

                    
                    <table id="tilePhotoUploadTable" class="table table-hover text-nowrap nowrap cell-border table-bordered">
                        <thead>
                            @foreach ($columnNames as $item)
                                <th scope="col">
                                    {{ $item }}
                                </th>
                            @endforeach
                        </thead>
                        <tbody>
                            @forelse ($data as $tilePhotoUpload)
                                <tr>
                                    @foreach ($columnNames as $item)
                                        <td>
                                            {{ $tilePhotoUpload[$loop->index] }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columnNames) }}">
                                        No records found.
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
