@extends('layouts.app')

@section('title')
    List of council users
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h1 class="my-0">List of users</h1>
                    <a type="button" class="btn btn-info text-white" href="{{route('users.create')}}"><span class="cil-contrast"></span> Create User</a>
                </div>
            </div>
            {{-- {{ $users->appends(request()->query())->render("pagination::bootstrap-5") }} --}}
            <table id="example" class="table table-striped table-bordered table-sm dt-responsive nowrap w-100" >
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
                        <th class="bg-body-secondary">Type</th>
                        <th class="bg-body-secondary">Zone ID</th>
                        <th class="bg-body-secondary">Post count</th>
                        <th class="bg-body-secondary"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="align-middle">
                            <td class="text-center">
                                <div class="avatar avatar-md">
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
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->type }}</td>
                            <td>{{ $user->zone->name }}</td>
                            <td>{{ $user->postCount->first()->count ?? 0 }}</td>
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
                                        {{-- <a class="dropdown-item btn btn-info" href="#" data-coreui-toggle="modal" data-coreui-target="#bannedModal-{{$user->id}}" data-coreui-whatever="@mdo">Bannish</a> --}}
                                        {{-- <a class="dropdown-item btn btn-info" href="#" data-coreui-toggle="modal" data-coreui-target="#activateModal-{{$user->id}}" data-coreui-whatever="@mdo">Activate</a> --}}
                                        <a class="dropdown-item" href="{{route('user.detail',$user->id)}}" >View</a>
                                        {{-- <a class="dropdown-item" href="{{route('users.edit',$user->id)}}" >Edit</a> --}}
                                        <button type="button" class="dropdown-item text-success" data-coreui-toggle="modal" data-coreui-target="#validateModal-{{ $user->id }}" data-coreui-whatever="@getbootstrap">Validate</button>
                                        <button type="button" class="dropdown-item text-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal-{{ $user->id }}" data-coreui-whatever="@getbootstrap">Delete</button>
                                        
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <!-- Lien vers la page précédente -->
                    @if ($users->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() }}">Previous</a></li>
                    @endif
            
                    <!-- Liens vers chaque page -->
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endforeach
            
                    <!-- Lien vers la page suivante -->
                    @if ($users->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() }}">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>

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
                        <div class="mb-3">
                            <label for="password" class="col-form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="col-form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="zone_id" class="col-form-label">Zone ID</label>
                            <input type="text" class="form-control" id="zone_id" name="zone_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
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

    <!-- Modal delete user -->
    @foreach ($users as $user)
        <div class="modal fade" id="deleteModal-{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{-- <i class="icon icon-xxl mt-5 mb-2 cil-warning"></i> --}}
                        Delete a user
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('users.delete',$user->id)}}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="mb-3">
                            <h3>
                                Do you want to delete this user?
                            </h3>
                            <p>
                                <h4 for="recipient-name" class="col-form-label">{{ $user->first_name }}  {{ $user->last_name }}</h4>
                            </p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary text-white" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach

    <!-- Modal validate user -->
    @foreach ($users as $user)
        <div class="modal fade" id="validateModal-{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{-- <i class="icon icon-xxl mt-5 mb-2 cil-warning"></i> --}}
                        Validate institution accouunt
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('validate.council',$user->id)}}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <h3 class="text-success text-center">
                                Do you want to validate this institution accouunt ?
                            </h3>
                            <p class="text-center">
                                <h4 for="recipient-name" class="col-form-label">{{ $user->first_name }}  {{ $user->last_name }}</h4>
                            </p>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary text-white" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Validate</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach

    <!-- Modal end -->
@endsection

@section('script')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js') }}"></script>
    <script>
        $(() => {
            $('[rel="tooltip"]').tooltip({trigger: "hover"});

            // App.checkAll()

            // Run datatable
            var table = $('#example').DataTable({
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm') // make pagination small
                }
            })
            // Apply column filter
            $('#example .dt-column-filter th').each(function (i) {
                $('input', this).on('keyup change', function () {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw()
                    }
                })
            })
            // Toggle Column filter function
            var responsiveFilter = function (table, index, val) {
                var th = $(table).find('.dt-column-filter th').eq(index)
                val === true ? th.removeClass('d-none') : th.addClass('d-none')
            }
            // Run Toggle Column filter at first
            $.each(table.columns().responsiveHidden(), function (index, val) {
                responsiveFilter('#example', index, val)
            })
            // Run Toggle Column filter on responsive-resize event
            table.on('responsive-resize', function (e, datatable, columns) {
                $.each(columns, function (index, val) {
                    responsiveFilter('#example', index, val)
                })
            })

        })
    </script>
@endsection

@section('error')
@endsection
