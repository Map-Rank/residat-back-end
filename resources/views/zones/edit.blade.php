@extends('layouts.app')

@section('title', 'Zone - Create')

@section('sidebar')
    @parent
    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')


    <nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
        <ol class="breadcrumb breadc    rumb-style2">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('zones.index') }}">Zones</a></li>
            <li class="breadcrumb-item active" aria-current="page">Zone Update</li>
        </ol>
    </nav>

    <div class="container px-4 " id="elt">
        <div class="row">
            <div class="col-sm-7">

                <div class="card card-style-1">
                    <div class="card-header">
                        <h5>Update zone : {{ $zone->name }} </h5>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => ['zone.update', $zone->id],'files' => true, 'class' => 'form-horizontal panel', 'enctype '=> "multipart/form-data", 'method'=>'PUT']) !!}
                        @csrf

                        <div class="form-group {!! $errors->has('parent_id') ? 'has-error' : '' !!}">
                            {!! Form::label('Parent', null, ['class' => '',]) !!}
                            <input v-model="zone_name" onfocusout="hidePanel" type="text"
                                class="form-control" placeholder="Filter zone name"/>
                            <input type="hidden" v-model="selected_zone_id" name="parent_id"/>
                            <ul
                                style="max-height: 300px; padding: 10px; margin: 10px; border: 1px solid #CCCCCC; border-radius: 10px;
                                    overflow-y: scroll; overflow-x: hidden">
                                <li  @click="selectZone(zone)" style="cursor: pointer; " v-for="zone in zones" >
                                    @{{ zone.name }} </li>
                            </ul>
                        </div>

                        <div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
                            {!! Form::label('name', null, ['class' => '',]) !!}
                            <input  onfocusout="hidePanel" type="text" class="form-control" required name="name"
                                placeholder="Name of the zone"/>
                            {!! $errors->first('name', '<small class="help-block">:message</small>') !!}
                        </div>

                        <div class="form-group {!! $errors->has('data') ? 'has-error' : '' !!}">
                            {!! Form::label('Banner image', null, ['class' => '',])     !!}
                            {!! Form::file('data', ['place_holder'=> 'Drop the file here', 'accept'=> 'image/*'])!!}
                            {!! $errors->first('data', '<small class="help-block">:message</small>') !!}
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
                zones : @json($zones),
                show_zone_list : false,
                zone_name: '',
                selected_zone : '',
                selected_zone_id : 0,
            },
            methods: {
                selectZone: function(zone){
                    console.log(zone.name);
                    this.selected_zone = zone;
                    this.zone_name = zone.name;
                    this.show_zone_list = false;
                    this.selected_zone_id = zone.id;

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
