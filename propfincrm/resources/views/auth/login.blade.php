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
    .auth-field { margin-bottom: 18px; }
    .auth-field label { display: block; margin-bottom: 7px; color: #46556e; font-size: 12px; font-weight: 700; }
    .auth-control { position: relative; }
    .auth-control svg { position: absolute; top: 50%; left: 14px; width: 17px; height: 17px; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .auth-control input { width: 100%; height: 44px; padding: 0 13px 0 43px; border: 1px solid #d7e0eb; border-radius: 7px; outline: none; background: #fff; color: #1e293b; font-size: 13px; transition: .15s ease; }
    .auth-control input::placeholder { color: #a1adbd; }
    .auth-control input:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.11); }
    .auth-error { display: block; margin-top: 6px; color: #dc2626; font-size: 11px; }
    .auth-row { display: flex; justify-content: flex-end; margin: -3px 0 22px; }
    .auth-row a { color: #e9680b; font-size: 12px; font-weight: 700; text-decoration: none; }
    .auth-row a:hover { color: #c65305; text-decoration: underline; }
    .auth-submit { width: 100%; height: 44px; border: 0; border-radius: 7px; background: #f97316; box-shadow: 0 5px 12px rgba(234,88,12,.2); color: #fff; font-size: 13px; font-weight: 700; transition: .15s ease; }
    .auth-submit:hover { background: #ea580c; transform: translateY(-1px); }
    .auth-footer { margin-top: 26px; color: #9aa7b8; font-size: 11px; text-align: center; }
    @media (max-width: 850px) { .auth-page { grid-template-columns: 1fr; } .auth-showcase { display: none; } .auth-panel { min-height: 100vh; } .auth-card .mobile-brand { display: block; } }
    @media (max-width: 480px) { .auth-panel { padding: 24px; } .auth-card h2 { font-size: 23px; } }
</style>

<div class="auth-page">
    <aside class="auth-showcase" aria-label="ElyLeads"></aside>

    <main class="auth-panel">
        <div class="auth-card">
            <div class="mobile-brand"><span class="mobile-brand-name">ElyLeads</span></div>
            <h2>Welcome back</h2><p class="intro">Sign in to continue to your ElyLeads workspace.</p>
            <form action="{{ url('/login') }}" method="post" novalidate>
                @csrf
                <x-form.input label="Email address" name="email" type="email" placeholder="Enter your email" autocomplete="email" icon="email" required autofocus />
                <x-form.input label="Password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" icon="password" required />
                <div class="auth-row" style="display: flex; justify-content: space-between; align-items: center; margin: -3px 0 22px;">
                    <a href="{{ route('company-registration.create') }}" style="color: #475569; font-weight: 600;">Create new workspace</a>
                    <a href="{{ url('/password/reset') }}">Forgot password?</a>
                </div>
                <button class="auth-submit" type="submit">Sign in</button>
            </form>
            <p class="auth-footer">© {{ date('Y') }} ElyLeads. All rights reserved.</p>
        </div>
    </main>
</div>
@endsection
