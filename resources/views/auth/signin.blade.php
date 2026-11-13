@extends('layouts.base', ['subtitle' => 'Sign in'])

@section('body-attribuet')
    class="authentication-bg"
@endsection

@section('content')
    <div class="auth-shell">
        {{-- Sign-in form --}}
        <main class="auth-main">
            <div class="auth-form">
                <img src="{{ asset('images/logo-dark.png') }}" alt="{{ setting('company_name') ?: 'FM Shoes' }}" class="auth-form-logo">

                <h2>Sign in</h2>
                <p class="auth-sub">Enter your email and password</p>

                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <div class="auth-input">
                            <iconify-icon icon="solar:letter-outline"></iconify-icon>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                value="{{ old('email') }}" placeholder="name@company.com" autocomplete="username" required autofocus>
                        </div>
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="auth-input">
                            <iconify-icon icon="solar:lock-keyhole-minimalistic-outline"></iconify-icon>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="auth-eye" data-toggle-password aria-label="Show password">
                                <iconify-icon icon="solar:eye-outline"></iconify-icon>
                            </button>
                        </div>
                        @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Keep me signed in</label>
                    </div>

                    <button class="btn btn-auth w-100" type="submit">
                        Sign in <iconify-icon icon="solar:arrow-right-outline" class="align-middle ms-1"></iconify-icon>
                    </button>
                </form>

            </div>

            <div class="auth-main-foot">&copy; {{ date('Y') }} <b>FM Enterprises</b> &middot; All rights reserved</div>
        </main>
    </div>
@endsection

@push('page-js')
<script>
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-toggle-password]');
    if (!btn) return;
    const input = btn.parentElement.querySelector('input');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('iconify-icon').setAttribute('icon', show ? 'solar:eye-closed-outline' : 'solar:eye-outline');
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
});
</script>
@endpush
