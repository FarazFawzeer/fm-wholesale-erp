@extends('layouts.base', ['subtitle' => 'Login'])

@section('body-attribuet')
    class="authentication-bg"
@endsection

@section('content')
    <div class="account-pages py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4 p-sm-5">
                            <div class="text-center">
                                <div class="mx-auto mb-4 text-center auth-logo">
                                    <a href="#" class="logo-dark">
                                        <img src="{{ asset('images/logo-dark.png') }}" height="72" alt="FM Shoes">
                                    </a>

                                    <a href="#" class="logo-light">
                                        <img src="{{ asset('images/logo-light.png') }}" height="72" alt="FM Shoes">
                                    </a>
                                </div>
                                <h4 class="fw-bold mb-1">Welcome back</h4>
                                <p class="text-muted mb-0">Sign in to continue to FM Wholesale ERP</p>
                            </div>
                            <form method="POST" action="{{ route('login.post') }}" class="mt-4">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="Enter your email" autocomplete="username" required autofocus>
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="form-label">Password</label>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter your password" autocomplete="current-password" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary btn-lg fw-medium" type="submit">Sign In</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
