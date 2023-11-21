{{-- <x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

@extends('layouts.auth-styles')

@section('title')
    Reset Passwoard
@endsection

@section('auth-content')
    <div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-7 p-4 mb-0">
                            <div class="card-body">
                                <h1>Reset password</h1>
                                <p class="text-body-secondary">Fill your informations</p>
                                <x-auth-session-status class="mb-4" :status="session('status')" />
                                <form method="POST" action="{{ route('password.store') }}">
                                    @csrf
                                    <div class="input-group mb-3"><span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-user"></use>
                                            </svg></span>
                                        <input class="form-control" name="email" type="text" placeholder="email" id="email" :value="old('email')" required>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                    <div class="input-group mb-4"><span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-lock-locked">
                                                </use>
                                            </svg></span>
                                        <input class="form-control" name="password" type="password" placeholder="Password" :value="old('password')">
                                    </div>
                                    <div class="input-group mb-4"><span class="input-group-text">
                                        <svg class="icon">
                                            <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-lock-locked">
                                            </use>
                                        </svg></span>
                                        <input class="form-control" name="password_confirmation" type="password" placeholder="Confirm Password" :value="old('password_confirmation')">
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="submit" class="btn btn-primary px-4" value="Reset Password"></input>
                                        </div>
                                        {{-- <div class="col-6 text-end">
                                            <button class="btn btn-link px-0" type="button">Reset Password ?</button>
                                        </div> --}}
                                    </div>
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                </form>
                            </div>
                        </div>
                        <div class="card col-md-5 text-white bg-primary py-5">
                            <div class="card-body text-center">
                                <div>
                                    <h2>Important</h2>
                                    <p>Please fill in your information so you can change your password.</p>
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
