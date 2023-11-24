{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password"  name="password" required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
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
                                <form method="POST" action="{{ route('password.confirm') }}">
                                    @csrf
                                    <div class="input-group mb-4"><span class="input-group-text">
                                            <svg class="icon">
                                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-lock-locked">
                                                </use>
                                            </svg></span>
                                        <input class="form-control" id="password" type="password"  name="password" required autocomplete="current-password" >
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="submit" class="btn btn-primary px-4" value="Confirm"></input>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card col-md-5 text-white bg-primary py-5">
                            <div class="card-body text-center">
                                <div>
                                    <h2>Important</h2>
                                    <p>Please confirm your password.</p>
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
