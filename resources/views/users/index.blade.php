@extends('layouts.app')

@section('title')
    List of users
@endsection

@section('content')
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0">List of users</h1>
                <button type="button" class="btn btn-info text-white"><span class="cil-contrast"></span> Add User</button>
            </div>
        </div>
        <table class="table border mb-0">
            <thead class="fw-semibold text-nowrap">
                <tr class="align-middle">
                    <th class="bg-body-secondary text-center">
                        <svg class="icon">
                            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-people') }}"></use>
                        </svg>
                    </th>
                    <th class="bg-body-secondary">First Name</th>
                    <th class="bg-body-secondary">Last Name</th>
                    <th class="bg-body-secondary">Email</th>
                    <th class="bg-body-secondary">Phone</th>
                    <th class="bg-body-secondary">Gender</th>
                    <th class="bg-body-secondary">Zone ID</th>
                    <th class="bg-body-secondary"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="align-middle">
                        <td class="text-center">
                            <div class="avatar avatar-md">
                                {{-- <img class="avatar-img" src="{{ asset('assets/img/avatars/1.jpg') }}" alt="{{ $user->email }}"> --}}
                                <img class="avatar-img" src="{{ $user->avatar ? asset($user->avatar) : asset('assets/img/avatars/1.jpg') }}" alt="{{ $user->email }}">
                                <span class="avatar-status bg-success"></span>
                            </div>
                        </td>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        {{-- <td class="text-center">
                            <img src="{{ $user->avatar }}" alt="{{ $user->email }}" style="width: 50px; height: 50px; border-radius: 50%;">
                        </td> --}}
                        <td>{{ $user->gender }}</td>
                        <td>{{ $user->zone_id }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-options') }}">
                                        </use>
                                    </svg>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Info</a>
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
@endsection
