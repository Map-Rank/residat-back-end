@extends('layouts.app')

@section('title')
    List of Companies
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h1 class="my-0">List of Companies</h1>
                </div>
            </div>
            
            <div class="content">
                <div class="">
                        @csrf
                    <div class="mb-3">
                        <table class="table border mb-0">
                            <thead class="fw-semibold text-nowrap">
                                <tr class="">
                                    <th class="bg-body-secondary">company_name</th>
                                    <th class="bg-body-secondary">owner_name</th>
                                    <th class="bg-body-secondary">email</th>
                                    <th class="bg-body-secondary">zone</th>
                                    <th class="bg-body-secondary">created_at</th>
                                    <th class="bg-body-secondary"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                    <tr class="align-middle">
                                        <td class="">
                                            <div class="text-nowrap">{{ $company->company_name }}</div>
                                        </td>
                                        <td class="">
                                            <div class="text-nowrap">{{ $company->owner_name }}</div>
                                        </td>
                                        <td class="">
                                            <div class="text-nowrap">{{ $company->email }}</div>
                                        </td>
                                        <td class="">
                                            <div class="text-nowrap">
                                                <div class="text-nowrap">{{ $company->zone->name }}</div>
                                            </div>
                                        </td>
                                        <td class="">
                                            <div class="text-nowrap">{{ $company->created_at }}</div>
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
                                                    <a class="dropdown-item" href="{{ route('companies.show', $company->id) }}" >View</a>
                                                    <button type="button" class="dropdown-item text-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal-{{ $company->id }}" data-coreui-whatever="@getbootstrap">Delete</button>
                                                    {{-- <a class="dropdown-item text-danger" href="#">Delete</a> --}}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <!-- Lien vers la page précédente -->
                            @if ($companies->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $companies->previousPageUrl() }}">Previous</a></li>
                            @endif
                    
                            <!-- Liens vers chaque page -->
                            @foreach ($companies->getUrlRange(1, $companies->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $companies->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                            @endforeach
                    
                            <!-- Lien vers la page suivante -->
                            @if ($companies->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $companies->nextPageUrl() }}">Next</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Next</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal delete Event -->
    @foreach ($companies as $company)
        <div class="modal fade" id="deleteModal-{{$company->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{-- <i class="icon icon-xxl mt-5 mb-2 cil-warning"></i> --}}
                        Delete company
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('companies.destroy', $company->id)}}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <div class="mb-3">
                            <p>
                                <h4 for="recipient-name" class="col-form-label"> FROM <strong> {{ $company->company_name }}</strong></h4>
                            </p>
                            <p>
                                <h4 for="recipient-name" class="col-form-label"> <strong>Title :</strong> </br> {{ $company->owner_name }}</h4>
                            </p>
                            <p>
                                <h4 for="recipient-name" class="col-form-label"> <strong>Description :</strong> </br> {{ $company->email }}</h4>
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