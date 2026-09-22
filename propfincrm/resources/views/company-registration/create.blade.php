@extends('layouts.app')

@section('content')
<style>
    body { margin: 0; background: #f5f7fb; color: #17233b; font-family: 'Lato', Arial, sans-serif; }
    .auth-page { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.12fr) minmax(420px, .88fr); }
    .auth-showcase { background: url("{{ asset('images/crm_login.png') }}") center center / cover no-repeat; }
    .auth-panel { display: flex; align-items: center; justify-content: center; padding: 30px; background: #fff; }
    .auth-card { width: 100%; max-width: 430px; }
    .mobile-brand { display: none; margin-bottom: 28px; }.mobile-brand-name { color:#17233b; font-size:22px; font-weight:700; letter-spacing:-.03em; }
    .auth-card h2 { margin: 0 0 8px; color: #17233b; font-size: 25px; font-weight: 700; letter-spacing: -.025em; }
    .intro { margin: 0 0 24px; color: #718096; font-size: 13px; }.auth-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; }
    .auth-field { margin-bottom: 15px; }.auth-field.full { grid-column: 1 / -1; }.auth-field label { display: block; margin-bottom: 7px; color: #46556e; font-size: 12px; font-weight: 700; }
    .auth-control input, .auth-control select { width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d7e0eb; border-radius: 7px; outline: none; background: #fff; color: #1e293b; font-size: 13px; transition: .15s ease; }
    .auth-control input:focus, .auth-control select:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.11); }
    .auth-control input.is-invalid, .auth-control select.is-invalid { border-color: #dc2626; }.auth-control input.is-invalid:focus, .auth-control select.is-invalid:focus { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.1); }
    .auth-error { display: block; margin-top: 5px; color: #dc2626; font-size: 11px; line-height: 1.35; }
    .terms { display: flex; gap: 8px; align-items: flex-start; margin: 2px 0 18px; color: #718096; font-size: 11px; line-height: 1.45; }.terms input { width: 14px; height: 14px; margin: 1px 0 0; }
    .auth-submit { width: 100%; height: 44px; border: 0; border-radius: 7px; background: #f97316; box-shadow: 0 5px 12px rgba(234,88,12,.2); color: #fff; font-size: 13px; font-weight: 700; transition: .15s ease; }.auth-submit:hover { background: #ea580c; transform: translateY(-1px); }
    .auth-footer { margin-top: 20px; color: #9aa7b8; font-size: 11px; text-align: center; }.auth-footer a { color: #e9680b; font-weight: 700; text-decoration: none; }
    @media (max-width: 850px) { .auth-page { grid-template-columns: 1fr; }.auth-showcase { display: none; }.auth-panel { min-height: 100vh; }.mobile-brand { display: block; } }
    @media (max-width: 480px) { .auth-panel { padding: 24px; }.auth-grid { grid-template-columns: 1fr; }.auth-field.full { grid-column: auto; }.auth-card h2 { font-size: 23px; } }
</style>
<div class="auth-page">
    <aside class="auth-showcase" aria-label="ElyLeads"></aside>
    <main class="auth-panel"><div class="auth-card">
        <div class="mobile-brand"><span class="mobile-brand-name">ElyLeads</span></div>
        <h2>Create your workspace</h2><p class="intro">Set up your company and become its first administrator.</p>
        <form method="post" action="{{ route('companies.register') }}" novalidate>@csrf
            <div class="auth-grid">
                <x-form.input label="Company name" name="company_name" autocomplete="organization" field-class="auth-field full" required autofocus />
                <x-form.input label="Name" name="name" autocomplete="name" required />
                <x-form.input label="Email" name="email" type="email" autocomplete="email" required />
                <x-form.input label="Timezone" name="timezone" type="select" :value="old('timezone', 'Asia/Kolkata')" :options="['Asia/Kolkata' => 'India Standard Time', 'Asia/Dubai' => 'Gulf Standard Time', 'Asia/Singapore' => 'Singapore Time', 'Europe/London' => 'United Kingdom Time', 'America/New_York' => 'US Eastern Time']" required />
                <x-form.input label="Password" name="password" type="password" autocomplete="new-password" required />
                <x-form.input label="Confirm password" name="password_confirmation" type="password" autocomplete="new-password" field-class="auth-field full" required />
            </div>
            <x-form.input label="I agree to create a company workspace and accept the service terms." name="terms" type="checkbox" field-class="terms" required />
            <button class="auth-submit" type="submit">Create workspace</button>
        </form>
        <p class="auth-footer">Already have a workspace? <a href="{{ route('login') }}">Sign in</a><br><br>© {{ date('Y') }} ElyLeads. All rights reserved.</p>
    </div></main>
</div>
@endsection
