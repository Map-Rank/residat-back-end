@extends('layouts.app')

@section('title')
    Create Social report
@endsection

@section('content')

<nav aria-label="breadcrumb" class="main-breadcrumb pl-3">
    <ol class="breadcrumb breadc    rumb-style2">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Social Report Creation</li>
    </ol>
</nav>

<div class="body flex-grow-1 bg-light" id="elt">
    <div class="container px-4 my-4">

        <form action="{{ route('social.report.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <h3>Create Social Report</h3>
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
                    <label for="high_risk_social_group" class="col-form-label">High risk social group</label>
                    <input type="number" class="form-control" id="high_risk_social_group" name="high_risk_social_group" required>
                </div>
                <div class="mb-3">
                    <label for="desc_high_risk_social_group" class="col-form-label">Description High risk social group</label>
                    <textarea type="text" class="form-control" id="desc_high_risk_social_group" name="desc_high_risk_social_group"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="local_climate_literacy" class="col-form-label">Local climate literacy</label>
                    <input type="number" class="form-control" id="local_climate_literacy" name="local_climate_literacy" required>
                </div>
                <div class="mb-3">
                    <label for="desc_local_climate_literacy" class="col-form-label">Description Local climate literacy</label>
                    <textarea type="text" class="form-control" id="desc_local_climate_literacy" name="desc_local_climate_literacy"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="social_stability" class="col-form-label">Social stability</label>
                    <input type="number" class="form-control" id="social_stability" name="social_stability" required>
                </div>
                <div class="mb-3">
                    <label for="desc_social_stability" class="col-form-label">Description Social stability</label>
                    <textarea type="text" class="form-control" id="desc_social_stability" name="desc_social_stability"></textarea>
                </div>
                <div class="mb-3">
                    <label for="poverty_index" class="col-form-label">Poverty index</label>
                    <input type="number" class="form-control" id="poverty_index" name="poverty_index" required>
                </div>
                <div class="mb-3">
                    <label for="desc_poverty_index" class="col-form-label">Description Poverty index</label>
                    <textarea type="text" class="form-control" id="desc_poverty_index" name="desc_poverty_index"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Social report</button>
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
