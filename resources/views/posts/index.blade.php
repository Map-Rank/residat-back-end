@extends('layouts.app')

@section('title')
    List of posts
@endsection

@section('content')
    <div class="body flex-grow-1">
        <div class="container px-4">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <h1 class="my-0">List of posts</h1>
                </div>
            </div>
            <table class="table border mb-0">
                <thead class="fw-semibold text-nowrap">
                    <tr class="align-middle">
                        <th class="bg-body-secondary text-center">
                            <svg class="icon">
                                <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-people') }}"></use>
                            </svg>
                        </th>
                        <th class="bg-body-secondary">First name</th>
                        <th class="bg-body-secondary">Last name</th>
                        <th class="bg-body-secondary">Content</th>
                        <th class="bg-body-secondary">number of likes</th>
                        <th class="bg-body-secondary">number of comment </th>
                        <th class="bg-body-secondary">number of sharing </th>
                        <th class="bg-body-secondary"></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($posts as $post)
                        <tr class="align-middle">
                            <td class="text-center">
                                <div class="avatar avatar-md">
                                    <img class="avatar-img"
                                        src="{{ $post->creator[0]->avatar ? asset($post->creator[0]->avatar) : asset('assets/img/avatars/1.jpg') }}"
                                        alt="{{ $post->creator[0]->email }}">
                                    <span class="avatar-status bg-success"></span>
                                </div>
                            </td>
                            <td>{{ $post->creator[0]->first_name }}</td>
                            <td>{{ $post->creator[0]->last_name }}</td>
                            <td>{{ $post->content }}</td>
                            <td>{{ $post->likes_count }}</td>
                            <td>{{ $post->comments_count }}</td>
                            <td>{{ $post->shares_count }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent p-0" type="button" data-coreui-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <svg class="icon">
                                            <use
                                                xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-options') }}">
                                            </use>
                                        </svg>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item btn {{ $post->active ? 'btn-warning' : 'btn-success' }}" href="#" data-coreui-toggle="modal" data-coreui-target="#activatePostModal-{{$post->id}}" data-coreui-whatever="@mdo">
                                            {{ $post->active ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <a class="dropdown-item" href="{{route('post.detail',$post->id)}}" >View</a>
                                        <a class="dropdown-item" href="#">Edit</a>
                                        <a class="dropdown-item text-danger" href="#">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3 d-flex flex-row-reverse">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
            
                        @for ($i = 1; $i <= $posts->lastPage(); $i++)
                            <li class="page-item {{ $posts->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ route('posts.index', ['page' => $i]) }}">{{ $i }}</a>
                            </li>
                        @endfor
            
                        <li class="page-item {{ $posts->currentPage() == $posts->lastPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('posts.index', ['page' => $posts->nextPageUrl()]) }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add Post</h5>
              <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="recipient-name" class="col-form-label">first name</label>
                  <input type="text" class="form-control" id="recipient-name">
                </div>
                <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">last name</label>
                    <input type="text" class="form-control" id="recipient-name">
                  </div>
                <div class="mb-3">
                  <label for="message-text" class="col-form-label">Message:</label>
                  <textarea class="form-control" id="message-text"></textarea>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Send message</button>
            </div>
          </div>
        </div>
      </div>

    <!-- Modal active an desactive post -->
    @foreach ($posts as $post)
    <div class="modal fade" id="activatePostModal-{{$post->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header {{ $post->active ? 'bg-warning' : 'bg-success' }}">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ $post->active ? 'Deactivate' : 'Activate' }} Post
                    </h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('allow.post', $post->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <h3>
                                Do you want to {{ $post->active ? 'deactivate' : 'activate' }} this post?
                            </h3>
                            <p>
                                <h4 for="recipient-name" class="col-form-label">{{ $post->content }}</h4>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger text-white" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $post->active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    <!-- Modal end -->
@endsection

