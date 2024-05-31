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

<div class="body flex-grow-1 bg-light">
    <div class="container px-4 my-4">

        <form action="{{ route('social.report.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="percentage_health_vulnerability" class="col-form-label">High risk social group</label>
                    <input type="number" class="form-control" id="percentage_vulnerability" name="percentage_health_vulnerability" required>
                </div>
                <div class="mb-3">
                    <label for="desc_report_health_case" class="col-form-label">Description</label>
                    <textarea type="text" class="form-control" id="desc_report_health_case" name="desc_report_health_case"></textarea>
                </div>

                <div class="mb-3">
                    <label for="desc_health_vulnerability" class="col-form-label">Local climate  literacy</label>
                    <textarea type="text" class="form-control" id="desc_health_vulnerability" name="desc_health_vulnerability"></textarea>
                </div>
                <div class="mb-3">
                    <label for="desc_report_health_case" class="col-form-label">Description</label>
                    <textarea type="text" class="form-control" id="desc_report_health_case" name="desc_report_health_case"></textarea>
                </div>

                <div class="mb-3">
                    <label for="report_health_case" class="col-form-label">Social stability</label>
                    <input type="number" class="form-control" id="report_health_case" name="report_health_case" required>
                </div>
                <div class="mb-3">
                    <label for="desc_report_health_case" class="col-form-label">Description</label>
                    <textarea type="text" class="form-control" id="desc_report_health_case" name="desc_report_health_case"></textarea>
                </div>
                <div class="mb-3">
                    <label for="desc_report_health_case" class="col-form-label">Poverty index</label>
                    <textarea type="text" class="form-control" id="desc_report_health_case" name="desc_report_health_case"></textarea>
                </div>
                <div class="mb-3">
                    <label for="desc_report_health_case" class="col-form-label">Description</label>
                    <textarea type="text" class="form-control" id="desc_report_health_case" name="desc_report_health_case"></textarea>
                </div>

                {{-- <div class="mb-3">
                    <label for="doc_to_patient_ratio" class="col-form-label">Doctor to patient ratio</label>
                    <input type="number" class="form-control" id="doc_to_patient_ratio" name="doc_to_patient_ratio" required>
                </div>
                <div class="mb-3">
                    <label for="desc_doc_to_patient_ratio" class="col-form-label">Description doctor to patient ratio</label>
                    <textarea type="text" class="form-control" id="last_name" name="desc_doc_to_patient_ratio"></textarea>
                </div>

                <div class="mb-3">
                    <label for="total_health_unit" class="col-form-label">Total number of health units</label>
                    <input type="number" class="form-control" id="total_health_unit" name="total_health_unit" required>
                </div>
                <div class="mb-3">
                    <label for="desc_total_health_unit" class="col-form-label">Description number of health units</label>
                    <textarea type="text" class="form-control" id="last_name" name="desc_total_health_unit"></textarea>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add Social report</button>
            </div>
        </form>
    </div>
</div>
@endsection
