@extends('layouts.app')

@section('title')
    List of Permissions
@endsection

@section('content')

<!-- /.row-->
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0">List of roles</h1>
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#exampleModal" data-coreui-whatever="@getbootstrap">Add role</button>
            </div>
        </div>
        <table class="table border mb-0">
            <thead class="fw-semibold text-nowrap">
                <tr class="">
                    <th class="bg-body-secondary">Roles</th>
                    <th class="bg-body-secondary">Number of Users</th>
                    {{-- <th class="bg-body-secondary">Permission</th> --}}
                    <th class="bg-body-secondary"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr class="align-middle">
                        <td class="">
                            <div class="text-nowrap">{{ $role->name }}</div>
                        </td>
                        <td>
                            <div class="text-nowrap">{{ $role->users->count() }}</div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-transparent p-0" type="button"
                                    data-coreui-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-options') }}"></use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('role.show', ['id' => $role->id]) }}">View</a>  
                                    <button type="button" class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModal-{{ $role->id }}" data-coreui-whatever="@getbootstrap">Edit</button>
                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal create rôle -->
        <div class="modal modal-xl fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new rôle</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('create.role')}}">
                    <div class="modal-body">
                        
                            @csrf
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="recipient-name">
                        </div>
                        <div class="mb-3">
                            <h3 class="form-label mb-3">Permissions</h3>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-2 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                            <label class="form-check-label">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send message</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    <!-- End Modal -->

    <!-- Modal edit role -->
        @foreach($roles as $role)
        <div class="modal modal-xl fade" id="editModal-{{ $role->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $role->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $role->id }}">Edit role</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('update.role', $role->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Champ caché pour l'ID du rôle -->
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <div class="mb-3">
                                <label for="role_name" class="col-form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $role->name }}">
                            </div>
                            <div class="mb-3">
                                <h3 class="form-label mb-3">Permissions</h3>
                                <div class="row">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-2 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $permission->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    <!-- End Modal -->
</div>

<!-- Modal -->

<!-- /.row-->
  
<style>
    .modal-body {
        max-height: calc(100vh - 200px); /* Limite la hauteur du corps du modal */
    }
    .modal-content {
        right: 40% !important;
        width: 200% !important;
    }
</style>
@endsection