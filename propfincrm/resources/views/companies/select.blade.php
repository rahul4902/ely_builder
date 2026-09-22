@extends('layouts.app')

@section('content')
<style>
    body { margin: 0; background: #f5f7fb; color: #17233b; font-family: 'Lato', Arial, sans-serif; }
    .company-select { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
    .company-select-card { width: 100%; max-width: 480px; padding: 34px; border: 1px solid #e4eaf2; border-radius: 12px; background: #fff; box-shadow: 0 18px 48px rgba(23,35,59,.08); }
    .company-select-brand { margin-bottom: 28px; color: #17233b; font-size: 21px; font-weight: 700; letter-spacing: -.03em; }
    .company-select h1 { margin: 0 0 8px; font-size: 25px; letter-spacing: -.025em; }
    .company-select p { margin: 0 0 22px; color: #718096; font-size: 13px; line-height: 1.5; }
    .company-option { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 14px; margin-top: 10px; padding: 14px 15px; border: 1px solid #d7e0eb; border-radius: 8px; background: #fff; color: #17233b; text-align: left; cursor: pointer; transition: .15s ease; }
    .company-option:hover, .company-option:focus-visible { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.11); outline: 0; }
    .company-option strong { display: block; font-size: 13px; }.company-option span { display: block; margin-top: 3px; color: #8a98aa; font-size: 11px; }
    .company-option b { color: #e9680b; font-size: 18px; font-weight: 400; }
</style>

<main class="company-select">
    <section class="company-select-card" aria-labelledby="company-select-title">
        <div class="company-select-brand">ElyLeads</div>
        <h1 id="company-select-title">Choose a company</h1>
        <p>Your account has access to multiple workspaces. Select the company you want to work in now.</p>
        @foreach ($companies as $company)
            <form method="POST" action="{{ route('companies.switch') }}">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <button class="company-option" type="submit">
                    <span><strong>{{ $company->name }}</strong><span>{{ ucwords(str_replace('_', ' ', $company->pivot->role)) }}</span></span>
                    <b aria-hidden="true">›</b>
                </button>
            </form>
        @endforeach
    </section>
</main>
@endsection
