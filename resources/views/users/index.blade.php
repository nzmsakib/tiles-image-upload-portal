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

                        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="userfile" class="col-sm-auto col-form-label">
                                    User Excel File
                                </label>
                                <div class="col-sm">
                                    <input class="form-control" type="file" id="userfile" name="userfile" required
                                        accept=".xlsx">
                                </div>
                                <div class="col-sm-auto">
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </div>
                        </form>

                        <form action="{{ route('users.index') }}" method="GET">
                            {{-- Filter by roles --}}
                            <div class="row mb-3">
                                <label for="role" class="col-sm-auto col-form-label">
                                    Role
                                </label>
                                <div class="col-sm">
                                    <input type="radio" class="btn-check" name="role" id="role-all" autocomplete="off"
                                        value="all" @checked(!request()->has('role') || request()->get('role') == '' || request()->get('role') == 'all') onchange="this.form.submit();">
                                    <label class="btn btn-outline-success" for="role-all">All</label>

                                    @foreach ($roles as $role)
                                        <input type="radio" class="btn-check" name="role" id="role-{{ $role->name }}"
                                            autocomplete="off" value="{{ $role->name }}" @checked(request()->get('role') == $role->name)
                                            onchange="this.form.submit();">
                                        <label class="btn btn-outline-success"
                                            for="role-{{ $role->name }}">{{ $role->name }}</label>
                                    @endforeach
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
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>
                                                {{ $user->id }}
                                            </td>
                                            <td>
                                                {{ $user->cid }}
                                            </td>
                                            <td>
                                                {{ $user->name }}
                                            </td>
                                            <td>
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Options
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a href="{{ route('tilefiles.index', ['assignee' => $user->id]) }}"
                                                                class="dropdown-item">
                                                                View {{ $user->assigned_tilefiles()->count() }}
                                                                Tilefiles
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="dropdown-item"
                                                                onclick="event.preventDefault(); document.getElementById('tilefiles-{{ $user->id }}').click();">
                                                                Upload Tilefile
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('users.edit', $user) }}"
                                                                class="dropdown-item">
                                                                Edit User
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <form action="{{ route('tilefiles.store') }}" method="POST"
                                                    enctype="multipart/form-data" class="d-none"
                                                    id="upload-form-{{ $user->id }}">
                                                    @csrf
                                                    <input type="hidden" name="user" value="{{ $user->id }}">
                                                    <input class="form-control" type="file"
                                                        id="tilefiles-{{ $user->id }}" name="tilefiles[]" required
                                                        accept=".xlsx" multiple
                                                        onchange="document.getElementById('upload-form-{{ $user->id }}').submit();">
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                No users found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
