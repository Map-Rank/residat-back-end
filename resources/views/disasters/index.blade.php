@extends('layouts.app')

@section('title')
    List of Disasters
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h1 class="my-0">List of Disasters</h1>
                    <a type="button" class="btn btn-info text-white" href="{{ route('disasters.create') }}">Create Disaster</a>
                </div>
            </div>
            
            <div class="content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Locality</th>
                            <th>Level</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disasters as $disaster)
                            <tr>
                                <td>{{ $disaster->id }}</td>
                                <td>{{ $disaster->description }}</td>
                                <td>{{ $disaster->locality }}</td>
                                <td>{{ $disaster->level }}</td>
                                <td>{{ $disaster->type }}</td>
                                <td>
                                    <a href="{{ route('disasters.show', $disaster->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('disasters.edit', $disaster->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('disasters.destroy', $disaster->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
