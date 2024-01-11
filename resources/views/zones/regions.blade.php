@extends('layouts.app')

@section('title')
    List of zones
@endsection

@section('content')
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0 mb-3">List of zones</h1>
                {{-- <button type="button" class="btn btn-info text-white" data-coreui-toggle="modal" data-coreui-target="#exampleModal" data-coreui-whatever="@mdo"><span class="cil-contrast"></span> Add User</button> --}}
            </div>
            <table class="table border mb-0">
                <thead class="fw-semibold text-nowrap">
                    <tr class="align-middle">
                        <th class="bg-body-secondary">#</th>
                        <th class="bg-body-secondary">Regions</th>
                        <th class="bg-body-secondary"></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($zones as $key => $zone)
                    {{-- {{dd($post->creator[0]->avatar)}} --}}
                        <tr class="align-middle">
                            <td class="">
                                {{ $key+1 }}
                            </td>
                            <td>{{ $zone->name }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-options') }}">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        {{-- <a class="dropdown-item btn btn-info" href="#" data-coreui-toggle="modal" data-coreui-target="#activatePostModal-{{$post->id}}" data-coreui-whatever="@mdo">Active</a> --}}
                                        {{-- <a class="dropdown-item btn {{ $post->active ? 'btn-warning' : 'btn-success' }}" href="#" data-coreui-toggle="modal" data-coreui-target="#activatePostModal-{{$post->id}}" data-coreui-whatever="@mdo">
                                            {{ $post->active ? 'Deactivate' : 'Activate' }}
                                        </a> --}}
                                        <a class="dropdown-item" href="{{route('region.division',$zone->id)}}" >View Division</a>
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
</div>

@endsection