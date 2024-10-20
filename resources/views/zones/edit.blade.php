@extends('layouts.app')

@section('title', 'Zone - Edit')

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
            <div class="col-sm-11">

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
                                placeholder="Name of the zone" value="{{ $zone->name ?? '' }}"/>
                            {!! $errors->first('name', '<small class="help-block">:message</small>') !!}
                        </div>
                        <div class="mb-3">
                            <div class="form-group">
                                <label for="" class="d-block">Code</label>
                                <input class="form-control" type="text" name="code" value="{{ $zone->code }}" placeholder="Enter code of zone" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-group">
                                <label for="" class="d-block">Latitude</label>
                                <input class="form-control" type="number" value="{{ $zone->latitude }}" step="any" name="latitude" placeholder="Enter latitude" required>
                            </div>
                            <div  class="form-group">
                                <label for="" class="d-block">Longitude</label>
                                <input class="form-control" type="number" value="{{ $zone->longitude }}" step="any" name="longitude" placeholder="Enter longitude" required>
                            </div>
                            <div  class="form-group">
                                <label for="" class="d-block">Select geojson file</label>
                                <input class="form-control" type="file" name="geojson" placeholder="Enter geojson">
                            </div>
                        </div>

                        <div class="form-group {!! $errors->has('data') ? 'has-error' : '' !!}">
                            {!! Form::label('Banner image', null, ['class' => '',])     !!}
                            {!! Form::file('data', ['place_holder'=> 'Drop the file here', 'accept'=> 'image/*'])!!}
                            {!! $errors->first('data', '<small class="help-block">:message</small>') !!}
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <img :src="imageFile ?? '../../image/image-.png'"
                                    style="width: 200px; height : 200px; border: 1px #ccc solid" />
                                <label for="graphic" class="d-block">Graphic</label>
                                <input type="file" name="image" accept=".svg" @change="processSVGFile">


                                <small class="help-block" v-if="!imageFile">Please upload a graphic file</small>
                            </div>

                            <div class="form-group">
                                <label for="detected_keys">Detected keys on the map</label>
                                <div style="display: flex; flex-direction: row;">

                                    <div v-for="(key, index) in vectorKeys" :key="index">
                                        <span class="badge badge-sm mx-2 "
                                            :style="{ backgroundColor: key.color }">@{{ key.id }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class=" col-sm-5 pt-4 pb-4" style="border: 1px solid #ccc">
                                <fieldset>
                                    Vector keys
                                </fieldset>
                                <div id="elt">
                                    <div class="form-group">
                                        <label for="vectorType">Key Type</label>
                                        <div class="form-group">

                                            <select class="form-control" v-validate="'required'" autofocus name="vectorType"
                                                v-model="vectorType">
                                                <option value="">Select Key Type</option>
                                                <option v-for="(keyType, index) in keyTypes" :value="keyType">
                                                    @{{ keyType }}</option>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="vectorValue">Value</label>
                                        <input type="text" v-model="vectorValue" v-validate="'required'"
                                            name="vectorValue" class="form-control">
                                        <span class="text-danger">@{{ errors.first('vectorValue') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="vectorName">Name</label>
                                        <input type="text" v-model="vectorName" v-validate="'required'"
                                            name="vectorName" class="form-control">
                                        <span class="text-danger">@{{ errors.first('vectorName') }}</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="vectorColor">Color</label>
                                        <input type="color" style="height: 50px" v-model="vectorColor"
                                            name="vectorColor" class="form-control" readonly>

                                    </div>

                                    <button type="submit" class="btn btn-success"
                                        @click.prevent='validateVectorFormBeforeSubmit'>Submit</button>


                                </div>
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
                                        <th>Code/Image</th>
                                        <th>Value</th>
                                        <th>Name</th>
                                        <th>color</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                {{-- where data are loaded --}}
                                <tbody>
                                    <tr v-for=" (key,index) in vectorKeys ">
                                        <td><input type="text" v-model="key.type"
                                                :name="'vector_keys[' + index + '][type]'"
                                                style="border: none; width: 100%" /></td>
                                        <td><input type="text" v-model="key.value"
                                                :name="'vector_keys[' + index + '][value]'"
                                                style="border: none; width: 100%" /></td>
                                        <td><input type="text" v-model="key.name"
                                                :name="'vector_keys[' + index + '][name]'"
                                                style="border: none; width: 100%" /></td>
                                        <td>
                                            <input type="color" v-model="key.color"
                                                :name="'vector_keys[' + index + '][color]'"
                                                style="border: none; width: 100%; pointer-events: none;" readonly />

                                        </td>

                                        <td>
                                            <div style="display: flex; justify-content: space-between;">
                                                <button @click.prevent='prepareUpdateVectorKey(index)'
                                                    class="btn btn-success" style="width: 40%;">

                                                    <img src="https://img.icons8.com/metro/26/000000/edit.png"
                                                        alt="edit" style="vertical-align: middle;" />
                                                </button>

                                                <button @click.prevent='deleteSpecificVectrKey(index)'
                                                    class="btn btn-danger" style="width: 40%;">
                                                    <img src="https://img.icons8.com/material-outlined/24/000000/trash--v1.png"
                                                        alt="delete" style="vertical-align: middle;">
                                                </button>
                                            </div>

                                        </td>
                                    </tr>

                                </tbody>
                            </table>
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
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@2.2.15"></script>

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

        Vue.use(VeeValidate);
        var app = new Vue({
            el: '#elt',
            data: {
                message: 'Hello Vue!',
                zones : @json($zones),
                show_zone_list : false,
                zone_name: @json($zone->parent->name ?? ''),
                selected_zone : @json($zone->parent ?? ''),
                selected_zone_id : @json($zone->parent->id ?? 0),
                latitude: '',
                longitude: '',
                geojson: '',
                vectorType: '',
                vectorValue: '',
                vectorName: '',
                vectorColor: '',
                vectorKeys: [],
                imageFile: null,
                formErrors: {
                    vectorType: '',
                    vectorValue: '',
                    vectorName: '',
                    metricType: '',
                    metricValue: '',
                },
                keyTypes: [
                    'image',
                    'code'
                ]
            },
            methods: {
                selectZone: function(zone){
                    console.log(zone.name);
                    this.selected_zone = zone;
                    this.zone_name = zone.name;
                    this.show_zone_list = false;
                    this.selected_zone_id = zone.id;
                },
                processSVGFile(event) {
                    this.vectorKeys.splice(0, this.vectorKeys.length)

                    const file = event.target.files[0];
                    if (!file) {
                        this.imageFile = null;
                        return;
                    }

                    this.isSvg = file.type === 'image/svg+xml';
                    this.imageFile = URL.createObjectURL(file);
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const parser = new DOMParser();
                        const svgDoc = parser.parseFromString(e.target.result, "image/svg+xml");
                        const paths = svgDoc.querySelectorAll('path');

                        const extractedData = Array.from(paths).map(path => ({

                            id: path.getAttribute('data-id'),
                            value: path.getAttribute('fill'),
                            type: this.isSvg ? 'color' : 'image',
                            name: path.getAttribute('data-name'),
                            color: path.getAttribute('fill'),
                            //this will be us if the color code in the svg is in the style attribute 🚀 @konnofuente
                            // color: this.extractColor(path.getAttribute('style'))
                        }));

                        this.vectorKeys.push(...extractedData)
                    };
                    reader.readAsText(file);
                },
                extractColor(styleString) {
                    const match = styleString.match(/fill: (\#[0-9a-fA-F]{6})/);
                    return match ? match[1] : 'DefaultColor'; // Return a default color or null if no match
                },
                validateVectorFormBeforeSubmit(event) {
                    event.preventDefault(); // Prevent default form submission

                    let fieldsToValidate = ['vectorType', 'vectorValue', 'vectorName'];
                    Promise.all(fieldsToValidate.map(field => this.$validator.validate(field))).then(results => {
                        let allValid = results.every(valid => valid);
                        if (allValid) {
                            this.submitVectorKey();
                        } else {
                            console.log('Vector form is invalid!');
                        }
                    });
                },
                submitVectorKey() {
                    if (this.updateIndex !== null) {
                        this.updateVectorKey()
                    } else {
                        this.addVectorKey()
                    }
                },
                addVectorKey() {
                    event.preventDefault();

                    this.vectorKeys.push({
                        type: this.vectorType,
                        value: this.vectorValue,
                        name: this.vectorName,
                        color: this.vectorColor
                    });

                    this.resetForm()
                },
                prepareUpdateVectorKey(index) {
                    const vectorKey = this.vectorKeys[index];
                    this.vectorType = vectorKey.type;
                    this.vectorValue = vectorKey.value;
                    this.vectorName = vectorKey.name;
                    this.vectorColor = vectorKey.color
                    this.updateIndex = index;
                },
                updateVectorKey() {
                    event.preventDefault();
                    this.vectorKeys[this.updateIndex] = {
                        type: this.vectorType,
                        value: this.vectorValue,
                        name: this.vectorName,
                        color: this.vectorColor
                    };

                    this.resetForm();
                    this.updateIndex = null;
                },
                deleteSpecificVectrKey(index) {
                    this.vectorKeys.splice(index, 1);
                },
                resetForm() {
                    event.preventDefault();

                    this.vectorType = '';
                    this.vectorValue = '';
                    this.vectorName = '';
                    this.vectorColor = '';
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
