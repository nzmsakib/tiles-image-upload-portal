@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card border-dark">
                    <div class="card-body">
                        <!-- Form -->
                        <form action="{{ route('users.update', $user) }}" method="POST" id="editUserForm"
                            class="row g-3 preview-container needs-validation" novalidate enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ $user->name }}" placeholder="Enter full name"
                                    required autofocus>

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ $user->email }}" placeholder="Enter user email"
                                    required>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Company ID --}}
                            <div class="col-md-6">
                                <label for="cid" class="form-label">Company ID</label>
                                <input type="text" class="form-control @error('cid') is-invalid @enderror"
                                    id="cid" name="cid" value="{{ $user->cid }}" placeholder="Enter company ID" required>

                                @error('cid')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="password" class="form-label">Set New Password</label>
                                <input type="text" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" value="{{ old('password') }}"
                                    placeholder="Enter user password or generate one" required>

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-auto d-flex flex-column justify-content-end">
                                {{-- Generate password button --}}
                                <button type="button" class="btn btn-outline-secondary" id="generate-password"
                                    onclick="document.getElementById('password').value = String(Math.random()).substring(2, 10);">
                                    <i class="bi bi-key"></i> Generate Password
                                </button>
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Assign Roles</label>
                                {{-- Checkboxes --}}
                                <div id="role">
                                    @foreach ($roles as $role)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox{{ $role->id }}" value="{{ $role->id }}"
                                                name="roles[]" @if ($user->hasRole($role->name)) checked @endif
                                                @if ($role->name == 'Admin' && $user->id == auth()->user()->id) disabled @endif>
                                            <label class="form-check-label" for="inlineCheckbox{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Submit form --}}
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
