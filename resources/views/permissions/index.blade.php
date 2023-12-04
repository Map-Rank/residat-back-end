@extends('layouts.app')

@section('title')
    List of Permissions
@endsection

@section('content')

{{-- {{dd($roles)}} --}}

<!-- /.row-->
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0">List of roles</h1>
                <button type="button" class="btn btn-info text-white" data-coreui-toggle="modal" data-coreui-target="#exampleModal" data-coreui-whatever="@mdo"><span class="cil-contrast"></span> Add roles</button>
            </div>
        </div>
        <table class="table border mb-0">
            <thead class="fw-semibold text-nowrap">
                <tr class="">
                    <th class="bg-body-secondary">Roles</th>
                    <th class="bg-body-secondary">Number of Users</th>
                    <th class="bg-body-secondary">Permission</th>
                    <th class="bg-body-secondary"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                {{-- onclick="window.location='{{ route('role.show', ['id' => $role->id]) }}';" style="cursor: pointer;" --}}
                    <tr class="align-middle">
                        <td class="">
                            <div class="text-nowrap">{{ $role->name }}</div>
                        </td>
                        <td>
                            <div class="text-nowrap">{{ $role->users->count() }}</div>
                        </td>
                        {{-- <td>
                            @foreach($role->permissions as $permission)
                                <span class="badge me-1 rounded-pill bg-info">{{ $permission->name }}</span>
                            @endforeach
                        </td> --}}
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
                                    <a class="dropdown-item" href="#">Edit</a>
                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /.row-->
  

@endsection