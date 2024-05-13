<div class="modal fade" tabindex="-1" aria-hidden="true" id="deleteModal-{{ $report->id }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-exclamation-circle fs-5x text-warning"></i>
                    <h4 class="p-2">Do you want to delete the report :
                        <span class="text-danger text-decoration-underline">{{ $report->name }}</span>?
                    </h4>
                    <form action="{{ route('reports.destroy',$report->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex justify-content-center mx-auto">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger ml-2">Yes, delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
