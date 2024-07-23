@extends('layouts.app')

@section('title')
    Show Company
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            {{-- <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h1 class="my-0">Show Company</h1>
                </div>
            </div> --}}
            
            <div class="container">
                <h1>Name: <b> {{ $company->company_name }}</b></h1>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2>Company Details</h2>
                        <p><strong>Owner Name:</strong> {{ $company->owner_name }}</p>
                        <p><strong>Description:</strong> {{ $company->description }}</p>
                        <p><strong>Email:</strong> {{ $company->email }}</p>
                        <p><strong>Phone:</strong> {{ $company->phone }}</p>
                        @if ($company->profile)
                            <p><strong>Profile Picture:</strong></p>
                            <img src="{{ asset('storage/' . $company->profile) }}" alt="{{ $company->company_name }}" style="max-width: 200px;">
                        @endif
                        @if ($company->official_document)
                            <p><strong>Official Document:</strong></p>
                            <a href="{{ asset('storage/' . $company->official_document) }}" target="_blank">View Document</a>
                        @endif
                        <p><strong>Zone:</strong> {{ $company->zone ? $company->zone->name : 'N/A' }}</p>
                        <p><strong>Created At:</strong> {{ $company->created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection