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
            <li class="breadcrumb-item active" aria-current="page">Administrative zones</li>
        </ol>
    </nav>
    <div class="container px-4 ">
        <div class="row mb-3 card card-style-1">
            <div class="card-header">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                        {{ $zones->appends(request()->query())->render("pagination::bootstrap-5") }}
                        <a href="{{ route('zone.create') }}" class="btn btn-info text-white" > Add a zone</a>
                </div>
            </div>

            <div class="card-body">
                <table id="example" class="table table-striped table-bordered table-sm dt-responsive nowrap w-100" >

                    <thead class="fw-semibold text-nowrap">
                        <tr class="column-filter dt-column-filter">
                            <th></th>
                            <th>
                                <input type="text" class="form-control" placeholder="">
                            </th>
                            <th>
                                <input type="text" class="form-control" placeholder="">
                            </th>
                            <th>
                                <input type="text" class="form-control" placeholder="">
                            </th>
                            <th>
                                <input type="text" class="form-control" placeholder="">
                            </th>
                            <th></th>
                        </tr>
                        <tr class="align-middle">
                            <th>#</th>
                            <th>Name</th>
                            <th>Banner</th>
                            <th>Vector</th>
                            <th>Administrative unit</th>
                            <th>Parent</th>
                            <th>#Children</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($zones as $key => $zone)
                            <tr class="align-middle">
                                <td class="">{{ $key+1 }}</td>
                                <td>{{ $zone->name }}</td>
                                <td><img src="{{ env('FRONT_URL').$zone->banner }}" height="50" width="50"/></td>
                                <td><img src="{{ env('FRONT_URL').$zone->vector?->path  }}" height="50" width="50"/></td>
                                <td>{{ $zone->level->name }}</td>
                                <td>{{ $zone->parent->name ?? '' }}</td>
                                <td>{{ $zone->children->count() }}</td>
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
                                            <a class="dropdown-item" href="{{route('region.division',$zone->id)}}" >View Division</a>
                                            <a class="dropdown-item" href="{{ route('zone.edit', $zone->id) }}">Edit</a>
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

@section('error')
@endsection
