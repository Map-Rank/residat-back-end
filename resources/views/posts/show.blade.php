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
                        <img src="{{$post->creator[0]->avatar}}" alt="User Image" class="img-fluid rounded-circle mb-3">
                        <p class="card-subtitle text-muted"><strong>Creator</strong> </p>
                        <h2 class="card-title">{{$post->creator[0]->first_name}}</h2>
                        <p class="card-subtitle mb-2 text-muted">{{$post->creator[0]->last_name}}</p>
                        <p class="card-subtitle text-muted">{{$post->creator[0]->zone->name}}</p>
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
                                <strong>Created at:</strong> {{$post->created_at->format('d/m/Y')}}
                            </p>
                            <p class="card-text">
                                <strong>Last Modified:</strong> {{$post->updated_at->format('d/m/Y')}}
                            </p>
                            <p class="card-text">
                                <strong>Sectors:</strong> 
                                @foreach($post->sectors as $sector)
                                <ul>
                                    <li>
                                        {{$sector->name}}
                                    </li>
                                </ul>
                                   
                                @endforeach
                            </p>
                        </div>
                    </div>
                        <!-- Contenu de l'onglet Posts -->
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