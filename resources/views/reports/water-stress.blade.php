@extends('layouts.app')

@section('title')
    Create Water Stress report
@endsection

@section('content')

<nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
    <ol class="breadcrumb breadc    rumb-style2">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Water Stress Report Creation</li>
    </ol>
</nav>

<div class="body flex-grow-1 bg-light" id="elt">
    <div class="container px-4 my-4">

        <form action="{{ route('water.stress.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <h3>Create Water Stress Report </h3>

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
                    <label for="desc_report_health_case" class="col-form-label">Select type</label>
                    <select class="form-select" autofocus name="selectedType" id="selectedType">
                        <option value=""> --- Select type --- </option>
                        <option value="current-level">Current level</option>
                        <option value="crisis-level">Crisis level</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="low_water_stress" class="col-form-label">Low water stress</label>
                    <input type="number" class="form-control" id="low_water_stress" name="low_water_stress" required>
                </div>
                <div class="mb-3">
                    <label for="desc_low_water_stress" class="col-form-label">Description Low water stress</label>
                    <textarea type="text" class="form-control" id="desc_low_water_stress" name="desc_low_water_stress"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="high_water_stressed" class="col-form-label">High water stressed</label>
                    <input type="number" class="form-control" id="high_water_stressed" name="high_water_stressed" required>
                </div>
                <div class="mb-3">
                    <label for="desc_high_water_stressed" class="col-form-label">Description High water stressed</label>
                    <textarea type="text" class="form-control" id="desc_high_water_stressed" name="desc_high_water_stressed"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="severe_water_stress" class="col-form-label">Severe water stress</label>
                    <input type="number" class="form-control" id="severe_water_stress" name="severe_water_stress" required>
                </div>
                <div class="mb-3">
                    <label for="desc_severe_water_stress" class="col-form-label">Description Severe water stress</label>
                    <textarea type="text" class="form-control" id="desc_severe_water_stress" name="desc_severe_water_stress"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Water Stress report</button>
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
