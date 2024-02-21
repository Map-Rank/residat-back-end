@extends('layouts.app')

@section('title')
    User Profil
@endsection

@section('content')
<div class="body flex-grow-1 bg-light">
    <div class="container px-4 my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <img src="{{$user->avatar}}" alt="John Doe" class="img-fluid rounded-circle mb-3">
                        <h2 class="card-title">{{$user->first_name}}</h2>
                        <h2 class="card-title">{{$user->last_name}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1016">
                    <nav>
                      <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-profile-tab" data-coreui-toggle="tab" data-coreui-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Profile</button>
                        <button class="nav-link" id="nav-gallery-tab" data-coreui-toggle="tab" data-coreui-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false" tabindex="-1">Gallery</button>
                        <button class="nav-link" id="nav-posts-tab" data-coreui-toggle="tab" data-coreui-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false" tabindex="-1">Posts</button>
                      </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                      <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <!-- Contenu de l'onglet Profile -->
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Personnels Informations</h3>
                                <p class="card-text">
                                    Email: <strong>{{$user->date_of_birth}}</strong> 
                                </p>
                                <p class="card-text">
                                    Phone: <strong>{{$user->phone}}</strong> 
                                </p>
                                <p class="card-text">
                                    Zone: <strong>{{$user->zone->name}}</strong> 
                                </p>
                                <p class="card-text">
                                    Gender: <strong>{{$user->gender}}</strong> 
                                </p>
                            </div>
                        </div>
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Sectors</h3>
                                <ul class="list-inline">
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">Web Development</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">Machine Learning</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">Data Science</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">PHP</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">Laravel</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">Python</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">TensorFlow</span></li>
                                    <li class="list-inline-item mb-2"><span class="badge bg-primary">SQL</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card shadow">
                            <div class="card-body">
                                <h3 class="card-title">Connections</h3>
                                <p class="card-text">123</p>
                            </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-gallery-tab">
                        <!-- Contenu de l'onglet Gallery -->
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Gallery</h3>
                                <div class="row">
                                    @foreach($user->myPosts as $post)
                                        @if (!empty($post->medias))
                                            @foreach($post->medias as $image)
                                                <div class="col-md-2 mb-3">
                                                    <img src="{{ asset($image['url']) }}" class="img-fluid rounded" alt="Image 5">
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-posts-tab">
                        <!-- Contenu de l'onglet Posts -->
                        @foreach($user->myPosts as $post)
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Posts</h3>

                                <!-- Section 1 -->  
                                <div class="media mb-4">
                                    <div class="media-body">
                                        <p>{{$post->content}}</p>
                                        <div class="row">
                                            @if (!empty($post->medias))
                                                @foreach($post->medias as $image)
                                                <div class="image-group">
                                                    <img src="{{ asset($image['url']) }}" alt="Image 1">
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                      </div>
                    </div>
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