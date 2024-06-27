@extends('layouts.app')

@section('title')
    Create Ressource completion report
@endsection

@section('content')

<nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
    <ol class="breadcrumb breadc    rumb-style2">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ressource completion Report Creation</li>
    </ol>
</nav>

<div class="body flex-grow-1 bg-light" id="elt">
    <div class="container px-4 my-4">

        <form action="{{ route('ressource.completion.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <h3>Create Ressource completion Report</h3>
                <div class="form-group {!! $errors->has('division') ? 'has-error' : '' !!}">
                    {!! Form::label('Sub Division', null, ['class' => '']) !!}
                    <input v-model="division_name" onfocusout="hidePanel" type="text" class="form-control"
                        placeholder="Filter sub division name" />
                    <input type="hidden" v-model="selected_division_id" name="zone_id" />
                    <ul v-if="show_division_list"
                        style="max-height: 300px; padding: 10px; margin: 10px; border: 1px solid #CCCCCC; border-radius: 10px;
                            overflow-y: scroll; overflow-x: hidden">
                        <li @click="selectDivision(division)" style="cursor: pointer; "
                            v-for="division in filtered_divisions">
                            @{{ division.name }} </li>
                    </ul>
                </div>
                <div class="form-group">
                    <label for="desc_report_health_case" class="col-form-label">Select level</label>
                    <select class="form-select" autofocus name="selectedType" id="selectedType">
                        <option value=""> --- Select type --- </option>
                        <option value="Current">Current</option>
                        <option value="projected">Projected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="farmer_grazer_conflicts" class="col-form-label">Farmer-Grazer conflicts</label>
                    <input type="number" class="form-control" id="farmer_grazer_conflicts" name="farmer_grazer_conflicts" required>
                </div>
                <div class="mb-3">
                    <label for="desc_farmer_grazer_conflicts" class="col-form-label">Description Farmer-Grazer conflicts</label>
                    <textarea type="text" class="form-control" id="desc_farmer_grazer_conflicts" name="desc_farmer_grazer_conflicts"></textarea>
                </div>
                <div class="mb-3">
                    <label for="water_conflicts" class="col-form-label">Water conflicts</label>
                    <input type="number" class="form-control" id="water_conflicts" name="water_conflicts" required>
                </div>
                <div class="mb-3">
                    <label for="desc_water_conflicts" class="col-form-label">Description Water conflicts</label>
                    <textarea type="text" class="form-control" id="desc_water_conflicts" name="desc_water_conflicts"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="human_wildlife_conflicts" class="col-form-label">Human-Wildlife conflicts</label>
                    <input type="number" class="form-control" id="human_wildlife_conflicts" name="human_wildlife_conflicts" required>
                </div>
                <div class="mb-3">
                    <label for="desc_human_wildlife_conflicts" class="col-form-label">Description Human-Wildlife conflicts</label>
                    <textarea type="text" class="form-control" id="desc_human_wildlife_conflicts" name="desc_human_wildlife_conflicts"></textarea>
                </div>
                <div class="mb-3">
                    <label for="land_conflicts" class="col-form-label">Land conflicts</label>
                    <input type="number" class="form-control" id="land_conflicts" name="land_conflicts" required>
                </div>
                <div class="mb-3">
                    <label for="desc_land_conflicts" class="col-form-label">Description Land conflicts</label>
                    <textarea type="text" class="form-control" id="desc_land_conflicts" name="desc_land_conflicts"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Ressource completion report</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')

    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>


    <script>

        // const health = ref();

        var app = new Vue({
            el: '#elt',
            data: {
                description: '',
                updateMetricIndex: null,
                token: '',
                formErrors: {
                    vectorType: '',
                    vectorValue: '',
                    vectorName: '',
                    metricType: '',
                    metricValue: '',
                },
                selected_division: '',
                selected_division_id: 0,
                division_name: '',
                divisions: @json($zones),
                filtered_divisions: @json($zones),
                show_division_list: true,
            },

            mounted() {


            },
            methods: {

                resetMetricForm() {
                    event.preventDefault();

                    this.metricType = '';
                    this.metricValue = '';
                },
                selectDivision: function(division) {
                    console.log(division.name);
                    this.selected_division = division;
                    this.division_name = division.name;
                    this.show_division_list = false;
                    this.selected_division_id = division.id;
                },
            },

            watch: {
                division_name: function(division_name) {
                    console.log('new value ' + division_name);
                    this.show_division_list = true;
                    if (division_name.length > 0) {
                        this.filtered_divisions = [];
                        for (let i = 0; i < this.divisions.length; i++) {
                            let full_name = this.divisions[i].name;
                            if (this.divisions[i].name.toLowerCase().includes(this.division_name
                                    .toLowerCase())) {
                                this.filtered_divisions.push(this.divisions[i]);
                            }
                        }
                    } else {
                        this.filtered_divisions = @json($zones);
                    }
                }
            },

        })
    </script>

@endsection
