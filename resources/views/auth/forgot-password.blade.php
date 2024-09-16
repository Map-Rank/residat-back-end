{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
@extends('layouts.app')

@section('title')
    Forgot Password
@endsection

@section('content')
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-7 p-4 mb-0">
                            <div class="card-body">
                                <h1>Email Password Reset</h1>
                                <p class="text-body-secondary">Sign In to your account</p>
                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="input-group mb-3"><span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="{{ mix('@coreui/icons/sprites/free.svg#cil-user') }}"></use>
                                            </svg></span>
                                        <input class="form-control" name="email" type="text" placeholder="email" id="email" :value="old('email')" required>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>  
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="submit" class="btn btn-primary px-4" value="Email Password Reset Link"></input>
                                        </div>
                                        {{-- <div class="col-6 text-end">
                                            <button class="btn btn-link px-0" type="button">Forgot password?</button>
                                        </div> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card col-md-5 text-white bg-primary py-5">
                            <div class="card-body text-center">
                                <div>
                                    <h2>Send Email link</h2>
                                    <p>Sent a reset email to be able to change your password.</p>
                                    {{-- <button class="btn btn-lg btn-outline-light mt-3" type="button">Register Now!</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

