@extends('layouts.app')

@section('title')
    Create user
@endsection

@section('content')
<div class="body flex-grow-1 bg-light">
    <div class="container px-4 my-4">
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="mb-3">
                    <label for="first_name" class="col-form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="col-form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="col-form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="col-form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                </div>
                <div class="mb-3">
                    <label for="date_of_birth" class="col-form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ $user->date_of_birth }}" required>
                </div>
                <div class="mb-3">
                    <label for="avatar" class="col-form-label">Avatar</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                </div>
                <div class="mb-3">
                    <label for="gender" class="col-form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="male" @if ($user->gender == 'male') selected @endif>Male</option>
                        <option value="female" @if ($user->gender == 'female') selected @endif>Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="zone_id" class="form-label">Zone</label>
                    <select class="form-select select2" data-coreui-search="true" id="zone_id" name="zone_id">
                        <option value="">Please select zone...</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" @if ($user->zone_id == $zone->id) selected @endif>{{ $zone->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">Role</label>
                    <select class="form-select select2" data-coreui-search="true" id="role_id" name="role_id">
                        <option value="">Please select role...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @if ($user->hasRole($role->name)) selected @endif>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection