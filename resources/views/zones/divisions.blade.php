@extends('layouts.app')

@section('title')
    List of divisions
@endsection

@section('content')
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0 mb-3">List of divisions</h1>
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#staticBackdrop">
                    Add divisions
                </button>
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
                    
                    @foreach ($divisions as $key => $division)
                    {{-- {{dd($post->creator[0]->avatar)}} --}}
                        <tr class="align-middle">
                            <td class="">
                                {{ $key+1 }}
                            </td>
                            <td>{{ $division->name }}</td>
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
                                        <a class="dropdown-item" href="{{route('region.division.subdivisions',$division->id)}}" >View SubDivision</a>
                                        {{-- <a class="dropdown-item" href="#">Edit</a>
                                        <a class="dropdown-item text-danger" href="#">Delete</a> --}}
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

<!-- Modal add divisions -->
<div class="modal fade" id="staticBackdrop" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
          <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Understood</button>
        </div>
      </div>
    </div>
  </div>
<!-- Modal End -->

@endsection