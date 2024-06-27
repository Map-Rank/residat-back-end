@extends('layouts.app')

@section('title')
    Create Agriculture report
@endsection

@section('content')

<nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
    <ol class="breadcrumb breadc    rumb-style2">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Infrastructure Report Creation</li>
    </ol>
</nav>

<div class="body flex-grow-1 bg-light" id="elt">
    <div class="container px-4 my-4">

        <form action="{{ route('infrastructure.report.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <h3>Create Infrastructure Report</h3>
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
                <div class="mb-3">
                    <label for="percentage_exposure" class="col-form-label">Percentage exposure</label>
                    <input type="number" class="form-control" id="percentage_exposure" name="percentage_exposure" required>
                </div>
                <div class="mb-3">
                    <label for="desc_percentage_exposure" class="col-form-label">Description Percentage exposure</label>
                    <textarea type="text" class="form-control" id="desc_percentage_exposure" name="desc_percentage_exposure"></textarea>
                </div>
                <div class="mb-3">
                    <label for="at_risk_critical_infrastructure" class="col-form-label">At risk Critical infrastructure</label>
                    <input type="number" class="form-control" id="at_risk_critical_infrastructure" name="at_risk_critical_infrastructure" required>
                </div>
                <div class="mb-3">
                    <label for="desc_at_risk_critical_infrastructure" class="col-form-label">Description At risk Critical infrastructure</label>
                    <textarea type="text" class="form-control" id="desc_at_risk_critical_infrastructure" name="desc_at_risk_critical_infrastructure"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="at_risk_social_infrastructure" class="col-form-label">At risk Social infrastructure</label>
                    <input type="number" class="form-control" id="at_risk_social_infrastructure" name="at_risk_social_infrastructure" required>
                </div>
                <div class="mb-3">
                    <label for="desc_at_risk_social_infrastructure" class="col-form-label">Description At risk Social infrastructure</label>
                    <textarea type="text" class="form-control" id="desc_at_risk_social_infrastructure" name="desc_at_risk_social_infrastructure"></textarea>
                </div>
                <div class="mb-3">
                    <label for="number_evacuation_sites" class="col-form-label">Number of Evacuation sites</label>
                    <input type="number" class="form-control" id="number_evacuation_sites" name="number_evacuation_sites" required>
                </div>
                <div class="mb-3">
                    <label for="desc_number_evacuation_sites" class="col-form-label">Description Number of Evacuation sites</label>
                    <textarea type="text" class="form-control" id="desc_number_evacuation_sites" name="desc_number_evacuation_sites"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Infrastructure report</button>
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
