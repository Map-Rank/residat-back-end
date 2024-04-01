@extends('layouts.app')

@section('title')
    List of Permissions
@endsection

@section('content')

<!-- /.row-->
<div class="body flex-grow-1">
    <div class="container px-4">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center mb-3">
                <h1 class="my-0">List of permissions</h1>
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#exampleModal">
                    Add permissions
                </button>
            </div>
        </div>
        <div class="content">
            <div class="">
                    @csrf
                <div class="mb-3">
                    <table class="table border mb-0">
                        <thead class="fw-semibold text-nowrap">
                            <tr class="">
                                <th class="bg-body-secondary">Permissions</th>
                                <th class="bg-body-secondary">Number of Users</th>
                                {{-- <th class="bg-body-secondary">Permission</th> --}}
                                <th class="bg-body-secondary"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr class="align-middle">
                                    <td class="">
                                        <div class="text-nowrap">{{ $permission->name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-nowrap">{{ $permission->users->count() }}</div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-transparent p-0" type="button"
                                                data-coreui-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <svg class="icon">
                                                    <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-options') }}"></use>
                                                </svg>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                {{-- <a class="dropdown-item" href="{{ route('role.show', ['id' => $permission->id]) }}">View</a>   --}}
                                                <button type="button" class="dropdown-item" data-coreui-toggle="modal" data-coreui-target="#editModal-{{ $permission    ->id }}" data-coreui-whatever="@getbootstrap">Edit</button>
                                                <a class="dropdown-item text-danger" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <!-- Lien vers la page précédente -->
                        @if ($permissions->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $permissions->previousPageUrl() }}">Previous</a></li>
                        @endif
                
                        <!-- Liens vers chaque page -->
                        @foreach ($permissions->getUrlRange(1, $permissions->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $permissions->currentPage() ? 'active' : '' }}"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endforeach
                
                        <!-- Lien vers la page suivante -->
                        @if ($permissions->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $permissions->nextPageUrl() }}">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal create rôle -->
        {{-- <div class="modal modal-xl fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new permission</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('create-permissions')}}">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="recipient-name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send message</button>
                    </div>
                </form>
                </div>
            </div>
        </div> --}}
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new permission</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{route('create.permissions')}}">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="recipient-name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send message</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    <!-- End Modal -->

    <!-- Modal edit role -->
        @foreach($permissions as $permission)
        <div class="modal modal-xl fade" id="editModal-{{ $permission->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $permission->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel-{{ $permission->id }}">Edit permissions</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('update.uniq.permissions', $permission->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Champ caché pour l'ID du rôle -->
                            <input type="hidden" name="role_id" value="{{ $permission->id }}">
                            <div class="mb-3">
                                <label for="role_name" class="col-form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $permission->name }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    <!-- End Modal -->
</div>

<!-- Modal -->

<!-- /.row-->
  
<style>
    .modal-body {
        max-height: calc(100vh - 200px); /* Limite la hauteur du corps du modal */
    }
    .modal-content {
        right: 0% !important;
        width: 100% !important;
    }
</style>
@endsection