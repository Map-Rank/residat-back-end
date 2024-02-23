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
            <li class="breadcrumb-item active" aria-current="page">Zone Creation</li>
        </ol>
    </nav>

    <div class="container px-4 " id="elt">
        <div class="row">
            <div class="col-sm-7">

                <div class="card card-style-1">
                    <div class="card-header">
                        <h5>Add a zone</h5>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'zone.store','files' => true, 'class' => 'form-horizontal panel', 'enctype '=> "multipart/form-data"]) !!}
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

                        <div v-if="show_region"  class="form-group {!! $errors->has('region') ? 'has-error' : '' !!}">
                            {!! Form::label('Region', null, ['class' => '',]) !!}
                            <input v-model="region_name" onfocusout="hidePanel" type="text"
                                class="form-control" placeholder="Filter region name"/>
                            <input type="hidden" v-model="selected_region_id" name="region_id"/>
                            <ul v-if="show_region_list"
                                style="max-height: 300px; padding: 10px; margin: 10px; border: 1px solid #CCCCCC; border-radius: 10px;
                                    overflow-y: scroll; overflow-x: hidden">
                                <li  @click="selectRegion(region)" style="cursor: pointer; " v-for="region in regions" >
                                    @{{ region.name }} </li>
                            </ul>
                        </div>

                        <div v-if="show_division"  class="form-group {!! $errors->has('division') ? 'has-error' : '' !!}">
                            {!! Form::label('Division', null, ['class' => '',]) !!}
                            <input v-model="division_name" onfocusout="hidePanel" type="text"
                                class="form-control" placeholder="Filter division name"/>
                            <input type="hidden" v-model="selected_division_id" name="division_id"/>
                            <ul v-if="show_division_list"
                                style="max-height: 300px; padding: 10px; margin: 10px; border: 1px solid #CCCCCC; border-radius: 10px;
                                    overflow-y: scroll; overflow-x: hidden">
                                <li  @click="selectDivision(division)" style="cursor: pointer; " v-for="division in divisions" >
                                    @{{ division.name }} </li>
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
                show_zone_list : true,
                zone_selected: null,
                levels : @json($levels),
                show_division : false,
                show_region : false,
                show_region_list : false,
                show_division_list : false,
                selected_level_id : 0,
                region : null,
                zones: [],
                regions: [],
                divisions: [],
                selected_region : '',
                region_name: '',
                selected_division : '',
                selected_division_id : 0,
                selected_region_id : 0,
                division_name: '',
            },
            methods: {
                loadZones: function (level_id) {
                    console.log(level_id);
                    this.show_region = (level_id >= 3);
                    this.show_region_list = (level_id >= 3);
                    this.show_division = (level_id >= 4);
                    if(this.show_region){
                        axios
                            .get('/api/zone?level_id=2' )
                            .then( response => {
                                console.log((response.data.data));
                                this.regions = response.data.data;
                            })
                            .catch(error => console.log(error))
                    }
                },
                loadRegions: function (level_id) {
                    console.log(level_id);
                    this.show_region = (level_id >= 3);
                    this.show_region_list = (level_id >= 3);
                    this.show_division = (level_id >= 4);
                    if(this.show_region){
                        axios
                            .get('/api/zone?level_id='+(level_id-1))
                            .then( response => {
                                console.log((response.data.data));
                                this.regions = response.data.data;
                            })
                            .catch(error => console.log(error))
                    }
                },
                selectRegion: function(region){
                    console.log(region.name);
                    this.selected_region = region;
                    this.region_name = region.name;
                    this.show_region_list = false;
                    this.selected_region_id = region.id;
                    axios
                        .get('/api/zone?parent_id='+(region.id))
                        .then( response => {
                            console.log((response.data.data));
                            this.divisions = response.data.data;
                            this.show_division_list = true;
                        })
                        .catch(error => console.log(error))
                },
                selectDivision: function(division){
                    console.log(division.name);
                    this.selected_division = division;
                    this.division_name = division.name;
                    this.show_division_list = false;
                    this.selected_division_id = division.id;
                    axios
                        .get('/api/zone?parent_id='+(division.id))
                        .then( response => {
                            console.log((response.data.data));
                            this.divisions = response.data.data;
                        })
                        .catch(error => console.log(error))
                },

            },
            watch: {
                selected_level_id: function (level){
                    this.loadZones(this.selected_level_id);
                },
            },
            mounted() {

            }
        })
    </script>

@endsection

@section('error')
@endsection
