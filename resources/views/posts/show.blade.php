@extends('layouts.app')

@section('title')
    Post Detail
@endsection

@section('content')
<div class="body flex-grow-1 bg-light">
    <div class="container px-4 my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <!-- Utiliser les informations de l'utilisateur qui a créé le post -->
                        <img src="https://via.placeholder.com/150x150" alt="User Image" class="img-fluid rounded-circle mb-3">
                        <p class="card-subtitle text-muted"><strong>Creator</strong> </p>
                        <h2 class="card-title">John Doe</h2>
                        <p class="card-subtitle mb-2 text-muted">Software Engineer</p>
                        <p class="card-subtitle text-muted">Yaoundé, Cameroon</p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1016">
                    <!-- Utiliser les informations de création et de modification du post -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h3 class="card-title">Post Information</h3>
                            <p class="card-text">
                                <strong>Created at:</strong> January 1, 2023
                            </p>
                            <p class="card-text">
                                <strong>Last Modified:</strong> January 2, 2023
                            </p>
                        </div>
                    </div>
                    {{-- <div class="tab-content" id="nav-tabContent"> --}}
                        <!-- Utiliser la même structure que la page de détails de l'utilisateur -->
                        <!-- ... (restez le même) ... -->
                      {{-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-posts-tab"> --}}
                        <!-- Contenu de l'onglet Posts -->
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Posts</h3>

                                <!-- Section 1 -->
                                <div class="media mb-4">
                                    {{-- <img src="https://via.placeholder.com/64" class="mr-3 rounded-circle" alt="User Image"> --}}
                                    <div class="media-body">
                                        {{-- <h5 class="mt-0">Post Title 1</h5> --}}
                                        <p>Post content goes here...</p>
                                        <div class="row">
                                            <div class="image-group">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 1">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 2">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 3">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 4">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2 (Ajoutez plus de sections au besoin) -->
                                {{-- <div class="media mb-4">
                                    <img src="https://via.placeholder.com/64" class="mr-3 rounded-circle" alt="User Image">
                                    <div class="media-body">
                                        <h5 class="mt-0">Post Title 2</h5>
                                        <p>Post content goes here...</p>
                                        <div class="row">
                                            <div class="image-group">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 1">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 2">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 3">
                                                <img src="https://via.placeholder.com/150x150" alt="Image 4">
                                              </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Ajoutez plus de sections au besoin -->
                            </div>
                        </div>
                        <!-- Ajoutez d'autres contenus pour l'onglet Posts au besoin -->
                      {{-- </div> --}}
                    {{-- </div> --}}
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.image-group {
  display: contents;
  flex-wrap: wrap;
}

.image-group img {
    width: 49%;
    object-fit: cover;
    margin: 3px;
}
</style>
@endsection