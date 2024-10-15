@extends('layouts.app')

@section('title')
    Create Disaster
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h1 class="my-0">Create Disaster</h1>
                </div>
            </div>
            
            <div class="content" id="elt">
                {!! Form::open([
                    'route' => 'disasters.store',
                    'files' => true,
                    'class' => 'form-horizontal panel',
                    'enctype ' => 'multipart/form-data',
                ]) !!}
                @csrf

                <!-- Champ de sélection de la zone -->
                <div class="form-group {!! $errors->has('zone_id') ? 'has-error' : '' !!}">
                    {!! Form::label('Zone', null, ['class' => '',]) !!}
                    <input v-model="zone_name" @input="filterZones" @focus="show_zone_list = true" @focusout="hidePanel" type="text" class="form-control" placeholder="Filter zone name" />
                    <input type="hidden" v-model="selected_zone_id" name="zone_id" />
                    <ul v-show="show_zone_list" style="max-height: 300px; padding: 10px; margin: 10px; border: 1px solid #CCCCCC; border-radius: 10px; overflow-y: scroll; overflow-x: hidden">
                        <li @click="selectZone(zone)" style="cursor: pointer;" v-for="zone in filtered_zones" :key="zone.id">
                            @{{ zone.name }}
                        </li>
                    </ul>
                </div>

                <!-- Autres champs -->
                <div class="form-group">
                    <label for="latitude">Level of danger</label>
                    <input type="number" class="form-control" name="level" value="{{ old('level') }}" required>
                </div>
                <div class="form-group">
                    <label for="locality">Locality</label>
                    <input type="text" class="form-control" name="locality" value="{{ old('locality') }}">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="number" class="form-control" step="any" name="latitude" value="{{ old('latitude') }}" required>
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="number" class="form-control" step="any" name="longitude" value="{{ old('longitude') }}" required>
                </div>
                <div class="form-group">
                    <label for="start_period">Start Period</label>
                    <input type="date" class="form-control" name="start_period" value="{{ old('start_period') }}" required>
                </div>
                <div class="form-group">
                    <label for="end_period">End Period</label>
                    <input type="date" class="form-control" name="end_period" value="{{ old('end_period') }}" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select class="form-control" name="type" required>
                        <option value="">Select a Type</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                
                {!! Form::submit('Save', ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top:10px; width:100%;']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        var app = new Vue({
            el: '#elt',
            data: {
                zones: @json($zones), // Liste des zones
                zone_name: '', // Nom de la zone sélectionnée
                selected_zone_id: '', // ID de la zone sélectionnée
                filtered_zones: [], // Zones filtrées
                show_zone_list: false, // Affichage de la liste des zones
            },
            methods: {
                filterZones: function() {
                    const searchTerm = this.zone_name.toLowerCase();
                    this.filtered_zones = this.zones.filter(zone => 
                        zone.name.toLowerCase().includes(searchTerm)
                    );
                },
                selectZone: function(zone) {
                    this.zone_name = zone.name;
                    this.selected_zone_id = zone.id;
                    this.show_zone_list = false;
                },
                hidePanel: function() {
                    setTimeout(() => { this.show_zone_list = false }, 200); // Retarder la fermeture
                }
            },
            mounted: function() {
                this.filtered_zones = this.zones; // Initialiser avec toutes les zones
            }
        });
    </script>
@endsection
