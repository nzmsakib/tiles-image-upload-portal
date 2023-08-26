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

                        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="companyfile" class="col-sm-auto col-form-label">
                                    Company User Data File
                                </label>
                                <div class="col-sm">
                                    <input class="form-control" type="file" id="companyfile" name="companyfile" required
                                        accept=".xlsx">
                                </div>
                                <div class="col-sm-auto">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table id="tileListTable"
                                class="table table-hover text-nowrap nowrap cell-border table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            ID
                                        </th>
                                        <th scope="col">
                                            Company ID
                                        </th>
                                        <th scope="col">
                                            Name
                                        </th>
                                        <th scope="col">
                                            Email
                                        </th>
                                        <th scope="col">
                                            Tilefiles
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($companies as $company)
                                        <tr>
                                            <td>
                                                {{ $company->id }}
                                            </td>
                                            <td>
                                                {{ $company->cid }}
                                            </td>
                                            <td>
                                                {{ $company->name }}
                                            </td>
                                            <td>
                                                {{ $company->email }}
                                            </td>
                                            <td>
                                                <a href="{{ route('tilefiles.index', ['assignee' => $company->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    View {{ $company->assigned_tilefiles()->count() }} Tilefiles
                                                </a>

                                                <a href="#" class="btn btn-sm btn-primary"
                                                    onclick="event.preventDefault(); document.getElementById('tilefiles-{{ $company->id }}').click();">
                                                    Upload Tilefile
                                                </a>
                                                <form action="{{ route('tilefiles.store') }}" method="POST"
                                                    enctype="multipart/form-data" class="d-none"
                                                    id="upload-form-{{ $company->id }}">
                                                    @csrf
                                                    <input type="hidden" name="user" value="{{ $company->id }}">
                                                    <input class="form-control" type="file"
                                                        id="tilefiles-{{ $company->id }}" name="tilefiles[]" required
                                                        accept=".xlsx" multiple
                                                        onchange="document.getElementById('upload-form-{{ $company->id }}').submit();">
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                No companies found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
