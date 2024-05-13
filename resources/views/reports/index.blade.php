@extends('layouts.app')

@section('title')
    List of zones
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}">
@endsection
@section('content')

<div class="body flex-grow-1">
    <nav aria-label="breadcrumb" class="main-breadcrumb pl-3 py-2">
        <ol class="breadcrumb breadcrumb-style2">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reports</li>
        </ol>
    </nav>
    <div class="container px-4 ">
        <div class="row mb-3 card card-style-1">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                        {{ $reports->appends(request()->query())->render("pagination::bootstrap-4") }}
                        <a href="{{ route('reports.create') }}" class="btn btn-info text-white" > Add a report</a>
                </div>
            </div>

            <div class="card-body">
                <table id="example" class="table table-striped table-bordered table-sm dt-responsive nowrap w-100" >

                    <thead class="fw-semibold text-nowrap">
                        <tr class="align-middle">
                            <th>Zone</th>
                            <th>Creator</th>
                            <th>Type</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Vector</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr class="align-middle">
                            <td>{{ $report->zone->name ?? ''}}</td>
                            <td>{{ $report->creator->first_name. ' ' .$report->creator->last_name }}</td>
                            <td>{{ $report->type }}</td>
                            <td>{{ $report->start_date }}</td>
                            <td>{{ $report->end_date }}</td>
                            <td><img src="{{ env('APP_URL').'/storage/'.$report->vector?->path  }}" height="50" width="50"/></td>
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
                                        <a class="dropdown-item btn {{ $report->active ? 'btn-warning' : 'btn-success' }}" href="#" data-coreui-toggle="modal" data-coreui-target="#activatePostModal-{{$report->id}}" data-coreui-whatever="@mdo">
                                            {{ $report->active ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <a class="dropdown-item" href="{{route('reports.show',$report->id)}}" >View</a>
                                        <a class="dropdown-item" href="{{route('reports.edit',$report->id)}}">Edit</a>
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
</div>

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
