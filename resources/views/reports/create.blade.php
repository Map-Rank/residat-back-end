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
                        <div class="form-group">
                            <label for="type">Report Type</label>
                            <select class="form-control" required autofocus name="type" v-model="reportType">
                                <option value="">Select the type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            <small class="help-block" v-if="reportType === ''">Please select a report type</small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea required type="text" v-model="description" v-validate="'required'" name="description" class="form-control"></textarea>
                            <span class="text-danger">@{{ errors.first('description') }}</span>
                        </div>



                        <div class="form-group">
                            <label for="start_date">Starting Period</label>
                            <input type="date" class="form-control" name="start_date" v-model="startDate">
                            <small class="help-block" v-if="startDate === ''">Please select a start date</small>
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Period</label>
                            <input type="date" class="form-control" name="end_date" v-model="endDate">
                            <small class="help-block" v-if="endDate === ''">Please select an end date</small>
                        </div>

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
                                        <span class="badge badge-sm mx-2 p-2"
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
                                        <label for="vectorType">Code/Image</label>
                                        <div class="form-group">
                                            <input type="color" style="height: 50px;pointer-events: none;" v-model="vectorType"
                                                name="vectorType" class="form-control ">

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
                                        <input type="text" v-model="vectorName" v-validate="'required'" name="vectorName"
                                            class="form-control">
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

                        <div class="col-sm-12 mt-4">
                            <div class=" col-sm-5 pt-4 pb-4" style="border: 1px solid #ccc">
                                <fieldset>
                                    Report data
                                </fieldset>
                                {{-- {{dd($types)}} --}}
                                <div class="form-group {!! $errors->has('type') ? 'has-error' : '' !!}">
                                    {!! Form::label('Metric Type', null, ['class' => '']) !!}
                                    <select class="form-select mb-3" autofocus name="metricType" v-model="metricType" v-validate="'required'"
                                            @change="toggleInput">
                                        <option value="">Select the metric type</option>
                                        @foreach ($metricTypes as $metricType)
                                            <option value="{{ $metricType->id }}">{{ $metricType->name }}</option>
                                        @endforeach
                                    </select>
                                    <span v-show="errors.has('metricType')" class="text-danger">@{{ errors.first('metricType') }}</span>
                                    <div v-if="health">
                                        <div class="form-group">
                                            <label for="metricValue">% Vulnerable to climate health risks</label>
                                            <input type="text" name="vulnerable_to_climate_health_risks" class="form-control" placeholder="Additional Field" v-model="vulnerable_to_climate_health_risks">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Reported cases in last 30 days</label>
                                            <input type="text" name="reported_cases_in_last_30_days" class="form-control" placeholder="Additional Field" v-model="reported_cases_in_last_30_days">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Doctor to patient ratio</label>
                                            <input type="text" name="doctor_to_patient_ratio" class="form-control" placeholder="Additional Field" v-model="doctor_to_patient_ratio">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Total number of health units</label>
                                            <input type="text" name="total_number_of_health_units" class="form-control" placeholder="Additional Field" v-model="total_number_of_health_units">
                                        </div>
                                    </div>
                                    <div v-if="agriculture">
                                        <div class="form-group" >
                                            <label for="metricValue">% Population Vulnerable</label>
                                            <input type="text" name="percent_population_vulnerable" class="form-control" placeholder="Additional Field" v-model="percent_population_vulnerable">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Last annual output</label>
                                            <input type="text" name="last_annual_output_agriculture" class="form-control" placeholder="Additional Field" v-model="last_annual_output_agriculture">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Number of farmers</label>
                                            <input type="text" name="number_farmers" class="form-control" placeholder="Additional Field" v-model="number_farmers">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Contribution to local economy</label>
                                            <input type="text" name="contribution_local_economy" class="form-control" placeholder="Additional Field" v-model="contribution_local_economy">
                                        </div>
                                    </div>
                                    <div v-if="infrastructure">
                                        <div class="form-group" >
                                            <label for="metricValue">Percentage exposure</label>
                                            <input type="text" name="percentage_exposure" class="form-control" placeholder="Additional Field" v-model="percentage_exposure">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">At risk Critical infrastructure</label>
                                            <input type="text" name="at_risk_Critical_infrastructure" class="form-control" placeholder="Additional Field" v-model="at_risk_Critical_infrastructure">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">At risk Social infrastructure
                                                </label>
                                            <input type="text" name="at_risk_social_infrastructure" class="form-control" placeholder="Additional Field" v-model="at_risk_social_infrastructure">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Number of Evacuation sites</label>
                                            <input type="text" name="number_of_evacuation_sites" class="form-control" placeholder="Additional Field" v-model="number_of_evacuation_sites">
                                        </div>
                                    </div>
                                    <div v-if="fishing">
                                        <div class="form-group" >
                                            <label for="metricValue">% Population Vulnerable</label>
                                            <input type="text" name="additionalField" class="form-control" placeholder="Additional Field">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Last annual output</label>
                                            <input type="text" name="last_annual_output_fishing" class="form-control" placeholder="Last annual output" v-model="last_annual_output_fishing">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Number of fishermen</label>
                                            <input type="text" name="number_of_fishermen" class="form-control" placeholder="Number of fishermen" v-model="number_of_fishermen">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Contribution to local economy</label>
                                            <input type="text" name="contribution_to_local_economy_fishing" class="form-control" placeholder="Contribution to local economy" v-model="contribution_to_local_economy_fishing">
                                        </div>
                                    </div>
                                    <div v-if="social">
                                        <div class="form-group" >
                                            <label for="metricValue">High risk social group</label>
                                            <input type="text" name="high_risk_social_group" class="form-control" placeholder="Additional Field" v-model="high_risk_social_group">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Local climate  literacy</label>
                                            <input type="text" name="local_climate_literacy" class="form-control" placeholder="Additional Field" v-model="local_climate_literacy">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Social stability</label>
                                            <input type="text" name="social_stability" class="form-control" placeholder="Additional Field" v-model="social_stability">
                                        </div>
                                        <div class="form-group">
                                            <label for="metricValue">Poverty index</label>
                                            <input type="text" name="poverty_index" class="form-control" placeholder="Additional Field" v-model="poverty_index">
                                        </div>
                                    </div>
                                    <div v-if="floodSecurity">
                                        <div class="form-group">
                                            <select class="form-select" autofocus name="floodSecurity" id="floodSecurity" v-model="selectedSecurity">
                                                <option value="">--- Select level ---</option>
                                                <option value="CurrentLevel">Current level</option>
                                                <option value="CrisisLevel">Crisis level</option>
                                            </select>
                                        </div>
                                        <div v-if="selectedSecurity === 'CurrentLevel'">
                                            <!-- Affiche les champs si CurrentLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="highRiskSocialGroup">Food secure households</label>
                                                <input type="text" name="food_secure_households" class="form-control" placeholder="High risk social group" v-model="food_secure_households">
                                            </div>
                                            <div class="form-group">
                                                <label for="localClimateLiteracy">Highly food insecure households</label>
                                                <input type="text" name="highly_food_insecure_households" class="form-control" placeholder="Local climate literacy" v-model="highly_food_insecure_households">
                                            </div>
                                            <div class="form-group">
                                                <label for="socialStability">Low food secure households</label>
                                                <input type="text" name="low_food_secure_households" class="form-control" placeholder="Social stability" v-model="low_food_secure_households">
                                            </div>
                                        </div>

                                        <div v-else-if="selectedSecurity === 'CrisisLevel'">
                                            <!-- Affiche les champs si CrisisLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="highRiskSocialGroup">Food secure households</label>
                                                <input type="text" name="food_secure_households" class="form-control" placeholder="High risk social group" v-model="food_secure_households">
                                            </div>
                                            <div class="form-group">
                                                <label for="localClimateLiteracy">Low food secure households</label>
                                                <input type="text" name="low_food_secure_households" class="form-control" placeholder="Local climate literacy" v-model="low_food_secure_households">
                                            </div>
                                            <div class="form-group">
                                                <label for="socialStability">Highly food insecure households</label>
                                                <input type="text" name="highly_food_insecure_households" class="form-control" placeholder="Social stability" v-model="highly_food_insecure_households">
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="waterStress">
                                        <div class="form-group">
                                            <select class="form-select" autofocus name="waterStress" id="waterStress" v-model="selectedFoodSecurity">
                                                <option value="">--- Select level ---</option>
                                                <option value="CurrentLevel">Current level</option>
                                                <option value="CrisisLevel">Crisis level</option>
                                            </select>
                                        </div>
                                        <div v-if="selectedFoodSecurity === 'CurrentLevel'">
                                            <!-- Affiche les champs si CurrentLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="highRiskSocialGroup">Low water stress</label>
                                                <input type="text" name="low_water_stress" class="form-control" placeholder="Low water stress" v-model="low_water_stress">
                                            </div>
                                            <div class="form-group">
                                                <label for="localClimateLiteracy">High water stressed</label>
                                                <input type="text" name="high_water_stressed" class="form-control" placeholder="High water stressed" v-model="high_water_stressed">
                                            </div>
                                            <div class="form-group">
                                                <label for="socialStability">Severe water stress</label>
                                                <input type="text" name="severe_water_stress" class="form-control" placeholder="Severe water stress" v-model="severe_water_stress">
                                            </div>
                                            <div class="form-group">
                                                <label for="CurrentLevel">Description</label>
                                                <textarea class="form-control" name="water_stress_description" id="" cols="30" rows="5" v-model="water_stress_description"></textarea>
                                            </div>
                                        </div>

                                        <div v-else-if="selectedFoodSecurity === 'CrisisLevel'">
                                            <!-- Affiche les champs si CrisisLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="highRiskSocialGroup">Low water stress</label>
                                                <input type="text" name="low_water_stress" class="form-control" placeholder="High risk social group" v-model="low_water_stress">
                                            </div>
                                            <div class="form-group">
                                                <label for="localClimateLiteracy">High water stressed</label>
                                                <input type="text" name="high_water_stressed" class="form-control" placeholder="Local climate literacy" v-model="high_water_stressed">
                                            </div>
                                            <div class="form-group">
                                                <label for="socialStability">Severe water stress</label>
                                                <input type="text" name="severe_water_stress" class="form-control" placeholder="Social stability" v-model="severe_water_stress">
                                            </div>
                                            <div class="form-group">
                                                <label for="CrisisLevel">Description</label>
                                                <textarea class="form-control" name="water_stress_description" id="" cols="30" rows="5" v-model="water_stress_description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="migration">
                                        <div class="form-group">
                                            <select class="form-select" autofocus name="waterStress" id="waterStress" v-model="selectedMigration">
                                                <option value="">--- Select migration ---</option>
                                                <option value="UrbanToRural">Urban to Rural</option>
                                                <option value="RuralToUrban">Rutal to urban</option>
                                            </select>
                                        </div>
                                        <div v-if="selectedMigration === 'UrbanToRural'">
                                            <!-- Affiche les champs si CurrentLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="adult">Adult</label>
                                                <input type="text" name="adult" class="form-control" placeholder="adult" v-model="adult">
                                            </div>
                                            <div class="form-group">
                                                <label for="youth">Youth</label>
                                                <input type="text" name="youth" class="form-control" placeholder="youth" v-model="youth">
                                            </div>
                                            <div class="form-group">
                                                <label for="children">Children</label>
                                                <input type="text" name="children" class="form-control" placeholder="children" v-model="children">
                                            </div>
                                            <div class="form-group">
                                                <label for="CurrentLevel">Description</label>
                                                <textarea class="form-control" name="migration_description" id="" cols="30" rows="5" v-model="migration_description"></textarea>
                                            </div>
                                        </div>

                                        <div v-else-if="selectedMigration === 'RuralToUrban'">
                                            <!-- Affiche les champs si CrisisLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="highRiskSocialGroup">Adult</label>
                                                <input type="text" name="adult" class="form-control" placeholder="High risk social group" v-model="adult">
                                            </div>
                                            <div class="form-group">
                                                <label for="localClimateLiteracy">Youth</label>
                                                <input type="text" name="youth" class="form-control" placeholder="Local climate literacy" v-model="youth">
                                            </div>
                                            <div class="form-group">
                                                <label for="socialStability">Children</label>
                                                <input type="text" name="children" class="form-control" placeholder="Social stability" v-model="children">
                                            </div>
                                            <div class="form-group">
                                                <label for="CrisisLevel">Description</label>
                                                <textarea class="form-control" name="migration_description" id="" cols="30" rows="5" v-model="migration_description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="ressourceCompletion">
                                        <div class="form-group">
                                            <select class="form-select" autofocus name="waterStress" id="waterStress" v-model="selectedRessourceCompletion">
                                                <option value="">--- Select ressource completion ---</option>
                                                <option value="Current">Current</option>
                                                <option value="Projected">Projected</option>
                                            </select>
                                        </div>
                                        <div v-if="selectedRessourceCompletion === 'Current'">
                                            <!-- Affiche les champs si CurrentLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="farmer_grazer_conflicts">Farmer-Grazer conflicts</label>
                                                <input type="text" name="farmer_grazer_conflicts" class="form-control" placeholder="Farmer-Grazer conflicts" v-model="farmer_grazer_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="water_conflicts">Water conflicts</label>
                                                <input type="text" name="water_conflicts" class="form-control" placeholder="Water conflicts" v-model="water_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="human_wildlife_conflicts">Human-Wildlife conflicts</label>
                                                <input type="text" name="human_wildlife_conflicts" class="form-control" placeholder="Human-Wildlife conflicts" v-model="human_wildlife_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="land_conflicts">Land conflicts</label>
                                                <input type="text" name="land_conflicts" class="form-control" placeholder="Land conflicts" v-model="land_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="resource_completion_description">Description</label>
                                                <textarea class="form-control" name="resource_completion_description" id="" cols="30" rows="5" v-model="resource_completion_description"></textarea>
                                            </div>
                                        </div>

                                        <div v-else-if="selectedRessourceCompletion === 'Projected'">
                                            <!-- Affiche les champs si CrisisLevel est sélectionné -->
                                            <div class="form-group">
                                                <label for="farmer_grazer_conflicts">Farmer-Grazer conflicts</label>
                                                <input type="text" name="farmer_grazer_conflicts" class="form-control" placeholder="Farmer-Grazer conflicts" v-model="farmer_grazer_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="water_conflicts">Water conflicts</label>
                                                <input type="text" name="water_conflicts" class="form-control" placeholder="Water conflicts" v-model="water_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="human_wildlife_conflicts">Human-Wildlife conflicts</label>
                                                <input type="text" name="human_wildlife_conflicts" class="form-control" placeholder="Human-Wildlife conflicts" v-model="human_wildlife_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="land_conflicts">Land conflicts</label>
                                                <input type="text" name="land_conflicts" class="form-control" placeholder="Land conflicts" v-model="land_conflicts">
                                            </div>
                                            <div class="form-group">
                                                <label for="resource_completion_description">Description</label>
                                                <textarea class="form-control" name="resource_completion_description" id="" cols="30" rows="5" v-model="resource_completion_description"></textarea>
                                            </div>
                                        </div>
                                    </div>







                                </div>


                                {{-- <div class="form-group">
                                    <label for="metricValue">Value</label>
                                    <input type="number" class="form-control" name="metricValue" v-model="metricValue"
                                        v-validate="'required'">
                                    <span v-show="errors.has('metricValue')"
                                        class="text-danger">@{{ errors.first('metricValue') }}</span>
                                </div> --}}

                                <button type="button" class="btn btn-success"
                                    @click.prevent="submitForm">Add Metric Data</button>

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

                                    <tr v-for=" (metric,index) in metricTypes ">
                                        <td><input type="text" v-model="metric.name"
                                                :name="'report_items[' + index + '][name]'"
                                                style="border: none; width: 100%" />
                                            <input type="hidden" v-model="metric.type"
                                                :name="'report_items[' + index + '][metric_type_id]'" />
                                        </td>
                                        <td><input type="text" v-model="metric.value"
                                                :name="'report_items[' + index + '][value]'"
                                                style="border: none; width: 100%" /> </td>
                                        <td>

                                            <div style="display: flex; justify-content: space-between;">
                                                <button @click.prevent='prepareUpdateMetricType(index)'
                                                    class="btn btn-success" style="width: 40%;">

                                                    <img src="https://img.icons8.com/metro/26/000000/edit.png"
                                                        alt="edit" style="vertical-align: middle;" />
                                                </button>

                                                <button @click.prevent='deleteSpecificMetricType(index)'
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
                        {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top:10px; width:100%;']) !!}
                        {!! Form::close() !!}
                        {{-- <button type="submit" class="btn btn-primary pull-right" style="width: 100%;"
                            >Save</button> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/vee-validate@2.2.15"></script>
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
        Vue.use(VeeValidate);
        // const health = ref();

        var app = new Vue({
            el: '#elt',
            data: {
                reportType: '',
                reported_cases_in_last_30_days:'',
                vulnerable_to_climate_health_risks: '',
                at_risk_Critical_infrastructure:'',
                at_risk_social_infrastructure:'',
                doctor_to_patient_ratio: '',
                total_number_of_health_units: '',
                percent_population_vulnerable: '',
                last_annual_output_agriculture: '',
                number_farmers: '',
                contribution_local_economy: '',
                percentage_exposure: '',
                at_risk_Critical: '',
                at_risk_social: '',
                number_of_evacuation_sites: '',
                percent_population_vulnerable_fishing: '',
                last_annual_output_fishing: '',
                number_of_fishermen: '',
                contribution_to_local_economy_fishing: '',
                high_risk_social_group: '',
                local_climate_literacy: '',
                social_stability: '',
                poverty_index: '',
                food_secure_households: '',
                highly_food_insecure_households: '',
                low_food_secure_households: '',
                food_secure_households: '',
                low_food_secure_households: '',
                highly_food_insecure_households: '',
                low_water_stress: '',
                high_water_stressed: '',
                severe_water_stress: '',
                water_stress_description: '',
                adult: '',
                youth: '',
                children: '',
                migration_description: '',
                farmer_grazer_conflicts: '',
                water_conflicts: '',
                human_wildlife_conflicts: '',
                land_conflicts: '',
                resource_completion_description: '',


                description: '',
                startDate: '2023-01-15',
                endDate: '2023-01-31',
                imageFile: null,
                vectorKeys: [],
                metricTypes: [],
                metrics: @json($metricTypes),
                vectorType: '',
                vectorValue: '',
                vectorName: '',
                vectorColor: '',
                metricType: '',
                selectedSecurity:'',
                selectedFoodSecurity:'',
                selectedMigration:'',
                selectedRessourceCompletion:'',
                health: false,
                agriculture: false,
                fishing:false,
                social:false,
                floodSecurity:false,
                waterStress:false,
                infrastructure:false,
                ImpactDegree: false,
                CurrentLevel:false,
                CrisisLevel:false,
                migration:false,
                ressourceCompletion:false,
                metricValue: '',
                metricName: '',
                updateIndex: null,
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
                axios.get('/get-token-from-session')
                    .then(response => {
                        this.token = response.data.token.plainTextToken
                    })
                    .catch(error => {
                        console.error('Error retrieving token:', error);
                    });
            },
            methods: {
                toggleInput() {
                    console.log(this.metricType)
                    if (this.metricType == 2) {
                        this.ImpactDegree = false;
                        this.health = true;
                        this.agriculture = false;
                        this.fishing = false;
                        this.infrastructure = false;
                        this.social = false;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.CurrentLevel = false;
                        this.CrisisLevel = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    } else if(this.metricType == 3) {
                        this.health = false;
                        this.ImpactDegree = false;
                        this.agriculture = true;
                        this.fishing = false;
                        this.infrastructure = false;
                        this.social = false;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.CurrentLevel = false;
                        this.CrisisLevel = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    } else if(this.metricType == 4) {
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.fishing = false;
                        this.infrastructure = true;
                        this.waterStress = false;
                        this.social = false;
                        this.floodSecurity = false;
                        this.CurrentLevel = false;
                        this.CrisisLevel = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    } else if(this.metricType == 5){
                        this.fishing = true;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.social = false;
                        this.CurrentLevel = false;
                        this.CrisisLevel = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    }else if(this.metricType == 6){
                        this.social = true;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.CurrentLevel = false;
                        this.CrisisLevel = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    }else if(this.metricType == 7){
                        this.floodSecurity = true;
                        this.social = false;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.waterStress = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    }else if(this.metricType == 8){
                        this.waterStress = true;
                        this.floodSecurity = false;
                        this.social = false;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    }else if(this.metricType == 10){
                        this.migration = true;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.social = false;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.ressourceCompletion = false;
                    }else if(this.metricType == 9){
                        this.ressourceCompletion = true;
                        this.migration = false;
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.social = false;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                    }else{
                        this.waterStress = false;
                        this.floodSecurity = false;
                        this.social = false;
                        this.fishing = false;
                        this.agriculture = false;
                        this.ImpactDegree = false;
                        this.health = false;
                        this.infrastructure = false;
                        this.migration = false;
                        this.ressourceCompletion = false;
                    }

                    // if(){
                        // waterStress

                    // }
                },

                submitForm() {
                    // Créer un nouvel objet FormData pour stocker les données du formulaire
                    let formData = new FormData();

                    // Ajouter les champs communs non conditionnels
                    formData.append('metricType', this.metricType);

                    // Ajouter les champs conditionnels en fonction de leur état
                    if (this.health) {
                        formData.append('vulnerable_to_climate_health_risks', this.vulnerable_to_climate_health_risks);
                        formData.append('reported_cases_in_last_30_days', this.reported_cases_in_last_30_days);
                        formData.append('doctor_to_patient_ratio', this.doctor_to_patient_ratio);
                        formData.append('total_number_of_health_units', this.total_number_of_health_units);
                    }
                    if (this.agriculture) {
                        formData.append('percent_population_vulnerable', this.percent_population_vulnerable);
                        formData.append('last_annual_output', this.last_annual_output_agriculture);
                        formData.append('number_of_farmers', this.number_of_farmers);
                        formData.append('contribution_to_local_economy', this.contribution_to_local_economy);
                    }
                    if (this.infrastructure) {
                        formData.append('percentage_exposure', this.percentage_exposure);
                        formData.append('at_risk_critical_infrastructure', this.at_risk_critical_infrastructure);
                        formData.append('at_risk_social_infrastructure', this.at_risk_social_infrastructure);
                        formData.append('number_of_evacuation_sites', this.number_of_evacuation_sites);
                    }
                    if (this.fishing) {
                        formData.append('percent_population_vulnerable_fishing', this.percent_population_vulnerable_fishing);
                        formData.append('last_annual_output_fishing', this.last_annual_output_fishing);
                        formData.append('number_of_fishermen', this.number_of_fishermen);
                        formData.append('contribution_to_local_economy_fishing', this.contribution_to_local_economy_fishing);
                    }
                    if (this.social) {
                        formData.append('high_risk_social_group', this.high_risk_social_group);
                        formData.append('local_climate_literacy', this.local_climate_literacy);
                        formData.append('social_stability', this.social_stability);
                        formData.append('poverty_index', this.poverty_index);
                    }
                    if (this.floodSecurity) {
                        formData.append('floodSecurity', this.selectedSecurity);
                        formData.append('food_secure_households', this.food_secure_households);
                        formData.append('highly_food_insecure_households', this.highly_food_insecure_households);
                        formData.append('low_food_secure_households', this.low_food_secure_households);
                        // Ajouter les autres champs spécifiques à 'floodSecurity' ici
                    }
                    if (this.waterStress) {
                        formData.append('waterStress', this.selectedFoodSecurity);
                        formData.append('low_water_stress', this.low_water_stress);
                        formData.append('high_water_stressed', this.high_water_stressed);
                        formData.append('severe_water_stress', this.severe_water_stress);
                        // Ajouter les autres champs spécifiques à 'waterStress' ici
                    }
                    if (this.migration) {
                        formData.append('selectedMigration', this.selectedMigration);
                        formData.append('adult', this.adult);
                        formData.append('youth', this.youth);
                        formData.append('children', this.children);
                        // Ajouter les autres champs spécifiques à 'migration' ici
                    }
                    if (this.ressourceCompletion) {
                        formData.append('selectedRessourceCompletion', this.selectedRessourceCompletion);
                        formData.append('farmer_grazer_conflicts', this.farmer_grazer_conflicts);
                        formData.append('water_conflicts', this.water_conflicts);
                        formData.append('human_wildlife_conflicts', this.human_wildlife_conflicts);
                        formData.append('land_conflicts', this.land_conflicts);
                        // Ajouter les autres champs spécifiques à 'ressourceCompletion' ici
                    }

                    // console.log(this.vulnerable_to_climate_health_risks)
                    // console.log(this.reported_cases_in_last_30_days)
                    // console.log(this.doctor_to_patient_ratio)
                    // console.log(this.total_number_of_health_units)
                    // for (var pair of formData.entries()) {
                    //     console.log(pair[0] + ': ' + pair[1]);
                    // }

                    // Envoyer le formulaire via une requête HTTP ou effectuer d'autres actions nécessaires
                    // Exemple de requête Axios (assurez-vous d'importer Axios si vous l'utilisez) :
                    axios.post('/reports', formData)
                        .then(response => {
                            // Traitement de la réponse
                        })
                        .catch(error => {
                            // Gestion des erreurs
                        });
                },


                processSVGFile(event) {
                    /*
                    const file = event.target.files[0];
                    if (!file) {
                        this.imageFile = null;
                        return;
                    }

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
                        console.log(this.vectorKeys)
                    };
                    reader.readAsText(file);
                    */
                },

                extractColor(styleString) {
                    const match = styleString.match(/fill: (\#[0-9a-fA-F]{6})/);
                    return match ? match[1] : 'DefaultColor'; // Return a default color or null if no match
                },


                onFileChange(event) {
                    const files = event.target.files;
                    if (files.length > 0) {
                        this.imageFile = files[0]; // Stocke le premier fichier sélectionné dans this.imageFile
                        console.log(this.imageFile)
                    }
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


                validateMetricFormBeforeSubmit(event) {
                    event.preventDefault(); // Prevent default form submission

                    let metricFieldsToValidate = ['metricType', 'metricValue'];
                    Promise.all(metricFieldsToValidate.map(field => this.$validator.validate(field))).then(
                        results => {
                            let allValid = results.every(valid => valid);
                            if (allValid) {
                                this.submitMetricType();
                            } else {
                                console.log('Metric form is invalid!');
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

                submitMetricType() {

                    // console.log(this.updateMetricIndex)
                    if (this.updateMetricIndex !== null) {
                        this.updateMetricType()
                    } else {
                        this.addMetricType()
                    }

                    this.resetMetricForm()
                },

                addMetricType() {
                    event.preventDefault();

                    console.log("the type  : " + this.metricType);
                    var metricName = '';
                    for (let i = 0; i < this.metrics.length; i++) {
                        if (this.metrics[i].id == this.metricType) {
                            metricName = this.metrics[i].name;
                        }
                    }
                    this.metricTypes.push({
                        type: this.metricType,
                        name: metricName,
                        value: this.metricValue,
                    });

                    this.resetForm()
                },

                prepareUpdateMetricType(index) {
                    const metricType = this.metricTypes[index];
                    this.metricType = metricType.type;
                    this.metricValue = metricType.value;

                    this.updateMetricIndex = index;
                    console.log(this.updateMetricIndex)
                },

                updateMetricType() {
                    event.preventDefault();
                    this.metricTypes[this.updateMetricIndex] = {
                        type: this.metricType,
                        name: this.metricName,
                        value: this.metricValue,
                    };

                    this.resetMetricForm();
                    this.updateMetricIndex = null;
                },


                deleteSpecificMetricType(index) {
                    this.metricTypes.splice(index, 1);
                },


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

@section('error')
@endsection
