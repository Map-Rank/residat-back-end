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
            <div class="col-sm-12">
                {{-- {{dd($types)}} --}}
                <div class="card card-style-1">
                    <div class="card-header">
                        <h5>Add a report</h5>
                    </div>
                    <div class="card-body">
                        {!! Form::open([
                            'route' => 'reports.store',
                            'files' => true,
                            'class' => 'form-horizontal panel',
                            'enctype ' => 'multipart/form-data',
                        ]) !!}
                        @csrf
                        <div class="form-group {!! $errors->has('type') ? 'has-error' : '' !!}">
                            {!! Form::label('Report Type', null, ['class' => '']) !!}
                            <select class="form-control" required autofocus name="type">
                                <option value="">Select the type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('type', '<small class="help-block">:message</small>') !!}
                        </div>

                        <div class="form-group {!! $errors->has('start_date') ? 'has-error' : '' !!}">
                            {!! Form::label('Starting period', null, ['class' => '']) !!}
                            {!! Form::date('start_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            {!! $errors->first('start_date', '<small class="help-block">:message</small>') !!}
                        </div>

                        <div class="form-group {!! $errors->has('end_date') ? 'has-error' : '' !!}">
                            {!! Form::label('End period', null, ['class' => '']) !!}
                            {!! Form::date('end_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            {!! $errors->first('end_date', '<small class="help-block">:message</small>') !!}
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group {!! $errors->has('data') ? 'has-error' : '' !!}">
                                <img src="../../image/image-.png"
                                    style="width: 200px; height : 200px; border: 1px #ccc solid" />
                                {!! Form::label('Graphic', null, ['class' => 'd-block']) !!}
                                {!! Form::file('data', ['place_holder' => 'Drop the file here', 'accept' => 'image/*']) !!}
                                {!! $errors->first('data', '<small class="help-block">:message</small>') !!}
                            </div>

                            <div class="form-group {!! $errors->has('data') ? 'has-error' : '' !!}">
                                {!! Form::label('Detected keys on the map', null, ['class' => 'd-block']) !!}
                                <span class="badge badge-sm bg-info ms-auto">Key 1</span> <span
                                    class="badge badge-sm bg-warning ms-auto">Key 2</span>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class=" col-sm-5 pt-4 pb-4" style="border: 1px solid #ccc">
                                <fieldset>
                                    Vector keys
                                </fieldset>
                                <div class="form-group {!! $errors->has('type') ? 'has-error' : '' !!}">
                                    {!! Form::label('Key Type', null, ['class' => '']) !!}
                                    <select class="form-select" required autofocus name="key" ref="vectorKeyType">
                                        <option value="">Select key type</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('type', '<small class="help-block">:message</small>') !!}
                                </div>

                               

                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <input type="text" class="form-control" name="value" ref="vectorkeyValue">
                                </div>

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" ref="vectorkeyName">
                                </div>

                                <button class="btn  btn-success" @click="addVectorKey">Add vector key</button>
                            </div>

                            <table id="example"
                                class="col-sm-6 table table-striped table-bordered table-sm dt-responsive nowrap w-100">

                                <thead class="fw-semibold text-nowrap">
                                    <tr class="column-filter dt-column-filter">
                                        <th>
                                            <input type="text" class="form-control" placeholder="">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" placeholder="">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" placeholder="">
                                        </th>

                                    </tr>
                                    <tr class="align-middle">
                                        <th>Vector type</th>
                                        <th>Value</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                {{-- where data are loaded --}}
                                <tbody>
                                    <tr v-for="(key, index) in vectorKeys" :key="index">
                                        <td>@{{ key.type }}</td>
                                        <td>@{{ key.value }}</td>
                                        <td>@{{ key.name }}</td>
                                        <td>
                                            <button class="btn btn-success">Edit/delete</button>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-12 mt-4">
                            <div class=" col-sm-5 pt-4 pb-4" style="border: 1px solid #ccc">
                                <fieldset>
                                    Report data
                                </fieldset>
                                <div class="form-group {!! $errors->has('type') ? 'has-error' : '' !!}">
                                    {!! Form::label('Metric Type', null, ['class' => '']) !!}
                                    <select class="form-select" required autofocus name="type">
                                        <option value="">Select the metric type</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('type', '<small class="help-block">:message</small>') !!}
                                </div>

                                <div class="form-group {!! $errors->has('value') ? 'has-error' : '' !!}">
                                    {!! Form::label('Value', null, ['class' => '']) !!}
                                    {!! Form::text('value', null, ['class' => 'form-control']) !!}
                                    {!! $errors->first('type', '<small class="help-block">:message</small>') !!}
                                </div>

                                <button class="btn  btn-success">Add report data</button>
                            </div>

                            <table id="example1"
                                class="col-sm-7 table table-striped table-bordered table-sm dt-responsive nowrap">

                                <thead class="fw-semibold text-nowrap">
                                    <tr class="column-filter dt-column-filter">
                                        <th>
                                            <input type="text" class="form-control" placeholder="">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" placeholder="">
                                        </th>

                                    </tr>
                                    <tr class="align-middle">
                                        <th>Metric type</th>
                                        <th>Value</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>



                        {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top:10px; width:100%;']) !!}
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
            $('[rel="tooltip"]').tooltip({
                trigger: "hover"
            });

            // App.checkAll()

            // Run datatable
            var table = $('#example').DataTable({
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-sm') // make pagination small
                }
            })
            // Apply column filter
            $('#example .dt-column-filter th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw()
                    }
                })
            })
            // Toggle Column filter function
            var responsiveFilter = function(table, index, val) {
                var th = $(table).find('.dt-column-filter th').eq(index)
                val === true ? th.removeClass('d-none') : th.addClass('d-none')
            }
            // Run Toggle Column filter at first
            $.each(table.columns().responsiveHidden(), function(index, val) {
                responsiveFilter('#example', index, val)
            })
            // Run Toggle Column filter on responsive-resize event
            table.on('responsive-resize', function(e, datatable, columns) {
                $.each(columns, function(index, val) {
                    responsiveFilter('#example', index, val)
                })
            })

        })
    </script>

    <script>
        $(() => {
            $('[rel="tooltip"]').tooltip({
                trigger: "hover"
            });

            // App.checkAll()

            // Run datatable
            var table = $('#example1').DataTable({
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-sm') // make pagination small
                }
            })
            // Apply column filter
            $('#example1 .dt-column-filter th').each(function(i) {
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw()
                    }
                })
            })
            // Toggle Column filter function
            var responsiveFilter = function(table, index, val) {
                var th = $(table).find('.dt-column-filter th').eq(index)
                val === true ? th.removeClass('d-none') : th.addClass('d-none')
            }
            // Run Toggle Column filter at first
            $.each(table.columns().responsiveHidden(), function(index, val) {
                responsiveFilter('#example1', index, val)
            })
            // Run Toggle Column filter on responsive-resize event
            table.on('responsive-resize', function(e, datatable, columns) {
                $.each(columns, function(index, val) {
                    responsiveFilter('#example1', index, val)
                })
            })

        })
    </script>

    <script>
        var app = new Vue({
            el: '#elt',
            data: {
                message: 'Hello Vue!',
                keyType: '',
                keyValue: '',
                keyName: '',
                vectorKeys: []


            },
            methods: {
                addVectorKey(event) {
                    console.log('enter')
                    event.preventDefault();

                    // const keyType = this.$refs.vectorKeyType.value;
                    const keyValue = this.$refs.vectorKeyValue.value;
                    const keyName = this.$refs.vectorKeyName.value;

                    this.vectorKeys.push({
                        type: keyType,
                        value: keyValue,
                        name: keyName,
                    });

                    // Clear the input fields
                    this.$refs.vectorKeyType.value = '';
                    this.$refs.vectorKeyValue.value = '';
                    this.$refs.vectorKeyName.value = '';
                },
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
