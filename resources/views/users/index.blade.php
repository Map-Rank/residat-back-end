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
                    {{-- <button type="button" class="btn btn-info text-white" data-coreui-toggle="modal" data-coreui-target="#exampleModal" data-coreui-whatever="@mdo"><span class="cil-contrast"></span> Add User</button> --}}
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
                    @foreach ($users as $user)
                        <tr class="align-middle">
                            <td class="text-center">
                                <div class="avatar avatar-md">
                                    {{-- <img class="avatar-img" src="{{ asset('assets/img/avatars/1.jpg') }}" alt="{{ $user->email }}"> --}}
                                    <img class="avatar-img"
                                        src="{{ $user->avatar ? asset($user->avatar) : asset('assets/img/avatars/1.jpg') }}"
                                        alt="{{ $user->email }}">
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
                            <td>{{ $user->zone->name }}</td>
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
                                        <a class="dropdown-item btn btn-info" href="#" data-coreui-toggle="modal" data-coreui-target="#bannedModal-{{$user->id}}" data-coreui-whatever="@mdo">Bannish</a>
                                        <a class="dropdown-item btn btn-info" href="#" data-coreui-toggle="modal" data-coreui-target="#activateModal-{{$user->id}}" data-coreui-whatever="@mdo">Activate</a>
                                        <a class="dropdown-item" href="{{route('user.detail',$user->id)}}" >View</a>
                                        {{-- <a class="dropdown-item" href="#">Edit</a>
                                        <a class="dropdown-item text-danger" href="#">Delete</a> --}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 d-flex flex-row-reverse">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
            
                        @for ($i = 1; $i <= $users->lastPage(); $i++)
                            <li class="page-item {{ $users->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ route('users.index', ['page' => $i]) }}">{{ $i }}</a>
                            </li>
                        @endfor
            
                        <li class="page-item {{ $users->currentPage() == $users->lastPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('users.index', ['page' => $users->nextPageUrl()]) }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- Modal add user -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
              <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="recipient-name" class="col-form-label">first name</label>
                  <input type="text" class="form-control" id="recipient-name">
                </div>
                <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">last name</label>
                    <input type="text" class="form-control" id="recipient-name">
                  </div>
                <div class="mb-3">
                  <label for="message-text" class="col-form-label">Message:</label>
                  <textarea class="form-control" id="message-text"></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Send message</button>
            </div>
          </div>
        </div>
    </div>
    <!-- Modal End -->

    <!-- Modal ban user -->
    @foreach ($users as $user)
        <div class="modal fade" id="bannedModal-{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{-- <i class="icon icon-xxl mt-5 mb-2 cil-warning"></i> --}}
                        Ban a user
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('ban.user',$user->id)}}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <h3>
                                Do you want to ban this user?
                            </h3>
                            <p>
                                <h4 for="recipient-name" class="col-form-label">{{ $user->first_name }}  {{ $user->last_name }}</h4>
                            </p>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach
    <!-- Modal end -->

    <!-- Modal active user -->
    @foreach ($users as $user)
        <div class="modal fade" id="activateModal-{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{-- <i class="icon icon-xxl mt-5 mb-2 cil-warning"></i> --}}
                        Activate a user
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('active.user',$user->id)}}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <h3>
                                Do you want to activate this user?
                            </h3>
                            <p>
                                <h4 for="recipient-name" class="col-form-label">{{ $user->first_name }}  {{ $user->last_name }}</h4>
                            </p>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach
    <!-- Modal end -->
@endsection
