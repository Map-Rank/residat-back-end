@extends('layouts.app')

@section('title')
    List of sectors
@endsection

@section('content')

<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h1 class="my-0">List of Sectors</h1>
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#exampleModal" data-coreui-whatever="@getbootstrap">Add sector</button>
            </div>
        </div>
        
        <div class="content">
            <div class="">
                    @csrf
                <div class="mb-3">
                    <table class="table border mb-0">
                        <thead class="fw-semibold text-nowrap">
                            <tr class="">
                                <th class="bg-body-secondary">Name</th>
                                <th class="bg-body-secondary">Number of Posts</th>
                                <th class="bg-body-secondary">actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sectors as $sector)
                                <tr class="align-middle">
                                    <td class="">
                                        <div class="text-nowrap">{{ $sector->name }} </div>
                                    </td>
                                    <td class="">
                                        <div class="text-nowrap">{{ $sector->posts_count }} </div>
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
                                                {{-- <a class="dropdown-item" href="{{ route('role.show', ['id' => $permission->id]) }}">View</a>   --}}
                                                <button type="button" class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModal-{{ $sector->id }}" data-coreui-whatever="@getbootstrap">Edit</button>
                                                <button type="button" class="dropdown-item text-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal-{{ $sector->id }}" data-coreui-whatever="@getbootstrap">Delete</button>
                                                {{-- <a class="dropdown-item text-danger" href="#">Delete</a> --}}
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

    </div>
</div>

<!-- Modal create sector -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add sector</h5>
            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{route('sectors.store')}}">
            <div class="modal-body">
                @csrf
                <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">Name</label>
                    <input type="text" name="name" class="form-control" id="recipient-name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- End Modal -->

<!-- Modal edit sector -->
    @foreach($sectors as $sector)
        <div class="modal modal-xl fade" id="editModal-{{ $sector->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $sector->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $sector->id }}">Edit sector</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('sectors.update', $sector->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="role_name" class="col-form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $sector->name }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
<!-- End Modal -->

<!-- Modal delete sector -->
@foreach ($sectors as $sector)
<div class="modal fade" id="deleteModal-{{$sector->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header bg-warning">
            <h5 class="modal-title" id="exampleModalLabel">
                Delete Sector
            </h5>
            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{route('sectors.destroy', $sector->id)}}">
            @csrf
            @method('DELETE')

            <div class="modal-body">
                <div class="mb-3">
                    <p>Do you want do delete this sector ?</p>
                    <p>
                        <h4 for="recipient-name" class="col-form-label"> Name: <strong> {{ $sector->name }}</strong></h4>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger text-white" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Delete</button>
            </div>
        </form>
    </div>
    </div>
</div>
@endforeach
<!-- Modal end -->

@endsection