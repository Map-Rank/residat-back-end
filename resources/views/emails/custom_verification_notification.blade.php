@extends('layouts.app')
@section('title')
    Email de vérification du compte
@endsection

@section('content')
    <h2>Vérification d'Email</h2>
    <p>Nous vous remercions de vous être inscrit sur notre plateforme. </p> 
    {{-- Pour vérifier votre adresse e-mail, veuillez utiliser le code OTP suivant si vous consultez cet e-mail sur votre appareil mobile --}}
    {{-- <p class="otp-code"><strong>{{ $otpCode }}</strong></p> --}}
    <p>Si vous consultez cet e-mail sur un ordinateur, cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail :</p>
    <div>
        <a class="btn btn-primary" href="{{ config('app.front_url') . '/verify-email/' . $data['id'] . '/' . $data['hash'] }}">Vérifier l'Email</a>
    </div>
@endsection