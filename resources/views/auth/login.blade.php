@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="row justify-content-center md-container">
        <div class="col-md-12 col-lg-10">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="login-card wrap d-md-flex">
                <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last hidden">
                <!-- <div class="text-wrap p-4 p-lg-5 text-center align-items-center order-md-last hidden"> -->
                    <div class="text w-100">
                        <h2>Welcome to DENR 10</h2>
                        <div class="title-container"><h1>Regional Asset Management System</h1></div>
                        <h4>Automated Integrated Reconciled</h4>
                       <!--  <a href="/register" class="btn btn-white btn-outline-white">Sign Up</a> -->
                    </div>

                </div>

                <div class="card-body login-wrap p-4 p-lg-5">
                    <div class="d-flex">
                        <div class="w-100">
                        <h3 class="mb-4">Sign In</h3>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('login') }}" class="signin-form">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="username" class="label">{{ __('Username') }}</label>

                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="Enter Username">

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="label">{{ __('Password') }}</label>

                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter Password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group">
                                <button type="submit" class="form-control btn btn-primary submit px-3">
                                    {{ __('Login') }}
                                </button>
                        </div>
                        <div class="form-group d-md-flex">
                                <div class="w-50 text-left">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="checkbox-wrap checkbox-primary mb-0" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                <div class="w-50 text-md-right">
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
