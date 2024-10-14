@extends('layouts.app')

@section('title')
    View Disaster
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h1 class="my-0">View Disaster</h1>
                </div>
            </div>
            
            <div class="content">
                <table class="table">
                    <tr>
                        <th>Description</th>
                        <td>{{ $disaster->description }}</td>
                    </tr>
                    <tr>
                        <th>Locality</th>
                        <td>{{ $disaster->locality }}</td>
                    </tr>
                    <tr>
                        <th>Latitude</th>
                        <td>{{ $disaster->latitude }}</td>
                    </tr>
                    <tr>
                        <th>Longitude</th>
                        <td>{{ $disaster->longitude }}</td>
                    </tr>
                    <tr>
                        <th>Level</th>
                        <td>{{ $disaster->level }}</td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>{{ $disaster->type }}</td>
                    </tr>
                    <tr>
                        <th>Start Period</th>
                        <td>{{ $disaster->start_period }}</td>
                    </tr>
                    <tr>
                        <th>End Period</th>
                        <td>{{ $disaster->end_period }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
