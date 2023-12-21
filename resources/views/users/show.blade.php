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
                        <img src="https://via.placeholder.com/150x150" alt="John Doe" class="img-fluid rounded-circle mb-3">
                        <h2 class="card-title">John Doe</h2>
                        <p class="card-subtitle mb-2 text-muted">Software Engineer</p>
                        <p class="card-subtitle text-muted">Yaoundé, Cameroon</p>
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
                                    Experienced software engineer with a passion for building innovative solutions.
                                    Proven track record of success in leading and delivering complex projects.
                                    Strong technical skills in web development, machine learning, and data science.
                                </p>
                                <p class="card-text">
                                    Experienced software engineer with a passion for building innovative solutions.
                                    Proven track record of success in leading and delivering complex projects.
                                    Strong technical skills in web development, machine learning, and data science.
                                </p>
                                <p class="card-text">
                                    Experienced software engineer with a passion for building innovative solutions.
                                    Proven track record of success in leading and delivering complex projects.
                                    Strong technical skills in web development, machine learning, and data science.
                                </p>
                            </div>
                        </div>
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Complementary Informations</h3>
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <h4 class="mb-1">Software Engineer</h4>
                                        <span class="company">Acme Corporation</span>
                                        <span class="location">Yaoundé, Cameroon</span>
                                        <span class="dates">2022 - Present</span>
                                        <p>
                                            Led the development of a new web application for managing customer accounts.
                                            Developed a machine learning model to predict customer churn.
                                            Built a data pipeline to collect and analyze customer data.
                                        </p>
                                    </li>
                                    <li class="mb-3">
                                        <h4 class="mb-1">Software Engineer</h4>
                                        <span class="company">Acme Corporation</span>
                                        <span class="location">Yaoundé, Cameroon</span>
                                        <span class="dates">2022 - Present</span>
                                        <p>
                                            Led the development of a new web application for managing customer accounts.
                                            Developed a machine learning model to predict customer churn.
                                            Built a data pipeline to collect and analyze customer data.
                                        </p>
                                    </li>
                                    <li class="mb-3">
                                        <h4 class="mb-1">Software Engineer</h4>
                                        <span class="company">Acme Corporation</span>
                                        <span class="location">Yaoundé, Cameroon</span>
                                        <span class="dates">2022 - Present</span>
                                        <p>
                                            Led the development of a new web application for managing customer accounts.
                                            Developed a machine learning model to predict customer churn.
                                            Built a data pipeline to collect and analyze customer data.
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        {{-- <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Education</h3>
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <h4 class="mb-1">Bachelor of Science in Computer Science</h4>
                                        <span class="school">University of Yaoundé</span>
                                        <span class="location">Yaoundé, Cameroon</span>
                                        <span class="dates">2018 - 2022</span>
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
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
                                    <div class="col-md-2 mb-3">
                                        <img src="https://via.placeholder.com/150x100" class="img-fluid rounded" alt="Image 1">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <img src="https://via.placeholder.com/150x100" class="img-fluid rounded" alt="Image 2">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <img src="https://via.placeholder.com/150x100" class="img-fluid rounded" alt="Image 3">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <img src="https://via.placeholder.com/150x100" class="img-fluid rounded" alt="Image 4">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <img src="https://via.placeholder.com/150x100" class="img-fluid rounded" alt="Image 5">
                                    </div>
                                    <!-- Ajoutez plus d'images au besoin -->
                                </div>
                            </div>
                        </div>
                        <!-- Ajoutez d'autres contenus pour l'onglet Gallery au besoin -->
                      </div>
                      <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-posts-tab">
                        <!-- Contenu de l'onglet Posts -->
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Posts</h3>

                                <!-- Section 1 -->
                                <div class="media mb-4">
                                    <img src="https://via.placeholder.com/64" class="mr-3 rounded-circle" alt="User Image">
                                    <div class="media-body">
                                        <h5 class="mt-0">Post Title 1</h5>
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
                                <div class="media mb-4">
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
                                </div>

                                <!-- Ajoutez plus de sections au besoin -->
                            </div>
                        </div>
                        <!-- Ajoutez d'autres contenus pour l'onglet Posts au besoin -->
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