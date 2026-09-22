@extends('layouts.app')

@section('content')
<style>
    body { margin: 0; background: #f5f7fb; color: #17233b; font-family: 'Lato', Arial, sans-serif; }
    .auth-page { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.12fr) minmax(420px, .88fr); }
    .auth-showcase { background: url("{{ asset('images/crm_login.png') }}") center center / cover no-repeat; }
    .auth-panel { display: flex; align-items: center; justify-content: center; padding: 30px; background: #fff; }
    .auth-card { width: 100%; max-width: 390px; }
    .auth-card .mobile-brand { display: none; margin-bottom: 36px; }
    .auth-card .mobile-brand-name { color: #17233b; font-size: 22px; font-weight: 700; letter-spacing: -.03em; }
    .auth-card h2 { margin: 0 0 8px; color: #17233b; font-size: 25px; font-weight: 700; letter-spacing: -.025em; }
    .auth-card .intro { margin: 0 0 30px; color: #718096; font-size: 13px; }
    .auth-submit { width: 100%; height: 44px; border: 0; border-radius: 7px; background: #f97316; box-shadow: 0 5px 12px rgba(234,88,12,.2); color: #fff; font-size: 13px; font-weight: 700; transition: .15s ease; cursor: pointer; }
    .auth-submit:hover { background: #ea580c; transform: translateY(-1px); }
    .auth-footer { margin-top: 26px; color: #9aa7b8; font-size: 11px; text-align: center; }
    .auth-row { display: flex; justify-content: space-between; align-items: center; margin: 16px 0 0; }
    .auth-row a { color: #e9680b; font-size: 12px; font-weight: 700; text-decoration: none; }
    .auth-row a:hover { color: #c65305; text-decoration: underline; }
    @media (max-width: 850px) { .auth-page { grid-template-columns: 1fr; } .auth-showcase { display: none; } .auth-panel { min-height: 100vh; } .auth-card .mobile-brand { display: block; } }
    @media (max-width: 480px) { .auth-panel { padding: 24px; } .auth-card h2 { font-size: 23px; } }
</style>

<div class="auth-page">
    <aside class="auth-showcase" aria-label="ElyLeads"></aside>

    <main class="auth-panel">
        <div class="auth-card">
            <div class="mobile-brand"><span class="mobile-brand-name">ElyLeads</span></div>
            <h2>{{ __('Reset Password') }}</h2>
            <p class="intro">{{ __('Enter your email address to receive a password reset link.') }}</p>

            @if (session('status'))
                <div class="alert alert-success py-2 px-3 mb-3 text-xs rounded-2">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/password/email') }}" novalidate>
                @csrf
                <x-form.input
                    label="{{ __('Email address') }}"
                    name="email"
                    type="email"
                    placeholder="{{ __('Enter your registered email') }}"
                    autocomplete="email"
                    icon="email"
                    :value="old('email')"
                    required
                    autofocus
                />

                <button class="auth-submit mt-2" type="submit">
                    {{ __('Send Reset Link') }}
                </button>

                <div class="auth-row">
                    <a href="{{ url('/login') }}">&larr; {{ __('Back to login') }}</a>
                </div>
            </form>

            <p class="auth-footer">&copy; {{ date('Y') }} ElyLeads. All rights reserved.</p>
        </div>
    </main>
</div>
@endsection

