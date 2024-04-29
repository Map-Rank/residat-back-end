@extends('layouts.app')

@section('title')
    Create user
@endsection

@section('content')
<div class="body flex-grow-1 bg-light">
    <div class="container px-4 my-4">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="first_name" class="col-form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="col-form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name">
                </div>
                <div class="mb-3">
                    <label for="email" class="col-form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="col-form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="date_of_birth" class="col-form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                </div>
                <div class="mb-3">
                    <label for="avatar" class="col-form-label">Avatar</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                </div>
                <input type="hidden" class="form-control" id="password" name="password" value="password" required>
                <div class="mb-3">
                    <label for="gender" class="col-form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="zone_id" class="form-label">Zone</label>
                    <select class="form-select select2" data-coreui-search="true" id="zone_id" name="zone_id">
                        <option value="">Please select zone...</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">Role</label>
                    <select class="form-select select2" data-coreui-search="true" id="role_id" name="role_id">
                        <option value="">Please select role...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>
@endsection