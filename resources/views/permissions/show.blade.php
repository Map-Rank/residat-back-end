@extends('layouts.app')

@section('title')
    List of Permissions
@endsection

@section('content')
<div class="body flex-grow-1">
    <div class="container px-4">
        <h1 class="mb-3">Role: {{ $role->name }} Details</h1>

        <form action="{{ route('permissions.update', ['id' => $role->id]) }}" method="post">
            @csrf
            @method('PUT')

            {{-- <div class="mb-3">
                <label class="form-label">Role Name</label>
                <input type="text" class="form-control" value="{{ $role->name }}" readonly>
            </div> --}}

            <div class="mb-3">
                <h3 class="form-label mb-3">Permissions</h3>
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-2 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $permission->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Role</button>
        </form>
    </div>
</div>
{{-- {{dd($role)}} --}}

@endsection 