@extends('layouts.app')

@section('title')
    User Detail
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Gallery Section -->
            <section class="my-4">
                <h2>Gallery</h2>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <img src="path/to/image1.jpg" class="img-fluid" alt="Image 1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="path/to/image2.jpg" class="img-fluid" alt="Image 2">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="path/to/image3.jpg" class="img-fluid" alt="Image 3">
                    </div>
                    <!-- Add more images as needed -->
                </div>
            </section>

            <!-- Liste des Posts effectués Section -->
            <section class="my-4">
                <h2>Liste des Posts effectués</h2>
            
                <ul class="list-group">
                    <li class="list-group-item">Post 1 - Contenu du post</li>
                    <li class="list-group-item">Post 2 - Contenu du post</li>
                    <li class="list-group-item">Post 3 - Contenu du post</li>
                    <!-- Add more posts as needed -->
                </ul>
            </section>

            <!-- Informations Personnelles Section -->
            <section class="my-4">
                <h2>Informations Personnelles</h2>
            
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nom:</strong> John Doe</p>
                        <p><strong>Email:</strong> john@example.com</p>
                        <!-- Add more personal information as needed -->
                    </div>
                    <div class="col-md-6">
                        <p><strong>Ville:</strong> Ville X</p>
                        <p><strong>Pays:</strong> Pays Y</p>
                        <!-- Add more personal information as needed -->
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>
@endsection