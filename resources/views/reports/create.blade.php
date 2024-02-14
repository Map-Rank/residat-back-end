@extends('layouts.app')

@section('title', 'Report - Create')

@section('sidebar')
    @parent
    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')


    <nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
        <ol class="breadcrumb breadc    rumb-style2">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">Report Creation</li>
        </ol>
    </nav>

    <div class="container px-4 " id="elt">
        <div class="row">
            <div class="col-sm-7">

                <div class="card card-style-1">
                    <div class="card-header">
                        <h5>Add a report</h5>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'report.store','files' => true, 'class' => 'form-horizontal panel', 'enctype '=> "multipart/form-data"]) !!}
                        @csrf

                        <div class="form-group {!! $errors->has('level_id') ? 'has-error' : '' !!}">
                            {!! Form::label('Level', null, ['class' => '',]) !!}
                            <select class="form-control" required autofocus name="level_id"  v-model="selected_level_id">
                                <option value="">Select the level</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('level_id', '<small class="help-block">:message</small>') !!}
                        </div>

                        {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right','style' => 'margin-top:10px; width:100%;']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
    <script>
        var app = new Vue({
            el: '#elt',
            data: {
                message: 'Hello Vue!',

            },
            methods: {

            },
            watch: {

            },
            mounted() {

            }
        })
    </script>

@endsection

@section('error')
@endsection
