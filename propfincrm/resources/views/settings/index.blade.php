@section('page-title', 'Settings')
@section('main-class', 'p-4')
@extends('layouts.master')

@section('content')
<div class="crm-form-page">
    <form method="POST" action="{{ url('settings/overall') }}">
        @csrf
        @method('PATCH')

        {{-- Task completion --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Task completion') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the task & the admin can complete the task.') }}<br>
                    {{ __('If Not allowed anyone, can complete all tasks.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="task_complete_allowed"
                    id="task_complete_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->task_complete_allowed"
                />
            </div>
        </div>

        {{-- Task assigning --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Task assigning') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the task & the admin can assign another user.') }}<br>
                    {{ __('If Not allowed anyone, can assign another user.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="task_assign_allowed"
                    id="task_assign_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->task_assign_allowed"
                />
            </div>
        </div>

        {{-- Lead completion --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Lead completion') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the lead & the admin can complete the lead.') }}<br>
                    {{ __('If Not allowed anyone, can complete all leads.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="lead_complete_allowed"
                    id="lead_complete_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->lead_complete_allowed"
                />
            </div>
        </div>

        {{-- Lead assigning --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">{{ __('Lead assigning') }}</div>
            <div class="p-3">
                <p class="mb-2">
                    {{ __('If Allowed only user who are assigned the lead & the admin can assign another user.') }}<br>
                    {{ __('If Not allowed anyone, can assign another user.') }}
                </p>
                <x-form.input
                    layout="standard"
                    field-class="crm-form-group"
                    class="crm-form-control"
                    name="lead_assign_allowed"
                    id="lead_assign_allowed"
                    type="select"
                    :options="[1 => __('Allowed'), 2 => __('Not allowed')]"
                    :value="$settings->lead_assign_allowed"
                />
            </div>
        </div>

        {{-- Theme & Appearance --}}
        <div class="crm-card mb-4" id="theme_settings">
            <div class="crm-card-header">{{ __('Theme & Appearance') }}</div>
            <div class="p-3">
                <p class="mb-3 text-muted">
                    {{ __('Customize the interface theme mode and primary accent colors used across the website.') }}
                </p>

                {{-- Mode --}}
                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">{{ __('Theme Mode') }}</label>
                    <input type="hidden" name="theme_mode" id="theme_mode_input" value="{{ old('theme_mode', $settings->theme_mode ?? 'light') }}">
                    <div class="d-flex gap-3 flex-wrap">
                        <div class="border rounded p-3 cursor-pointer theme-mode-choice {{ ($settings->theme_mode ?? 'light') === 'light' ? 'border-primary bg-light shadow-sm' : '' }}" data-mode="light" style="min-width: 180px; cursor: pointer;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i data-lucide="sun" class="text-warning"></i>
                                <span class="fw-semibold">{{ __('Light Mode') }}</span>
                                <span class="badge bg-secondary ms-auto">{{ __('Default') }}</span>
                            </div>
                            <small class="text-muted">{{ __('Standard light theme used across the website.') }}</small>
                        </div>
                        <div class="border rounded p-3 cursor-pointer theme-mode-choice {{ ($settings->theme_mode ?? 'light') === 'dark' ? 'border-primary bg-light shadow-sm' : '' }}" data-mode="dark" style="min-width: 180px; cursor: pointer;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i data-lucide="moon" class="text-primary"></i>
                                <span class="fw-semibold">{{ __('Dark Mode') }}</span>
                            </div>
                            <small class="text-muted">{{ __('Dark interface mode for comfortable viewing.') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Accent Color --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">{{ __('Primary & Accent Color') }}</label>
                    @php
                        $activeAccent = strtoupper($settings->theme_accent ?? '#0B57D0');
                        $paletteOptions = [
                            ['name' => 'CRM Blue', 'color' => '#0B57D0', 'default' => true],
                            ['name' => 'Ely Orange', 'color' => '#F97316', 'brand' => true],
                            ['name' => 'Teal', 'color' => '#00796B'],
                            ['name' => 'Purple', 'color' => '#7E57C2'],
                            ['name' => 'Terracotta', 'color' => '#C2410C'],
                            ['name' => 'Berry Pink', 'color' => '#C2185B'],
                            ['name' => 'Indigo Blue', 'color' => '#2563EB'],
                            ['name' => 'Emerald Green', 'color' => '#059669'],
                        ];
                    @endphp
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @foreach($paletteOptions as $p)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 setting-accent-btn {{ $activeAccent === strtoupper($p['color']) ? 'active border-primary' : '' }}" 
                                    data-color="{{ $p['color'] }}">
                                <span style="width: 16px; height: 16px; border-radius: 50%; background-color: {{ $p['color'] }}; display: inline-block;"></span>
                                <span>{{ $p['name'] }}</span>
                                @if(!empty($p['default']))
                                    <span class="badge bg-primary-subtle text-primary">{{ __('Default') }}</span>
                                @elseif(!empty($p['brand']))
                                    <span class="badge bg-warning-subtle text-dark">{{ __('Brand') }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <label for="theme_accent_picker" class="small fw-semibold text-muted mb-0">{{ __('Custom Color:') }}</label>
                            <input type="color" id="theme_accent_picker" value="{{ $activeAccent }}" class="form-control form-control-color" style="width: 42px; height: 34px; padding: 2px;">
                            <input type="text" name="theme_accent" id="theme_accent_input" value="{{ $activeAccent }}" class="form-control font-monospace text-uppercase" style="width: 110px;" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#0B57D0">
                        </div>
                        <button type="button" id="btn_reset_defaults" class="btn btn-sm btn-light border">
                            <i data-lucide="rotate-ccw" class="inline-block h-3.5 w-3.5 me-1"></i> {{ __('Reset to Defaults') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="crm-btn crm-btn-primary">{{ __('Save Settings') }}</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 === 'function') {
        $('#task_complete_allowed, #task_assign_allowed, #lead_complete_allowed, #lead_assign_allowed').each(function () {
            var $el = $(this);
            if (!$el.hasClass('select2-hidden-accessible')) {
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }
        });
    }

    // Theme mode click
    $('.theme-mode-choice').on('click', function () {
        var mode = $(this).data('mode');
        $('#theme_mode_input').val(mode);
        $('.theme-mode-choice').removeClass('border-primary bg-light shadow-sm');
        $(this).addClass('border-primary bg-light shadow-sm');
        if (window.applyCrmTheme) {
            window.applyCrmTheme(mode);
        }
    });

    // Accent button click
    function applyColor(hex) {
        if (!/^#[0-9A-Fa-f]{6}$/i.test(hex)) return;
        var cleanHex = hex.toUpperCase();
        $('#theme_accent_input').val(cleanHex);
        $('#theme_accent_picker').val(cleanHex);
        $('.setting-accent-btn').each(function () {
            var btnColor = ($(this).data('color') || '').toUpperCase();
            $(this).toggleClass('active border-primary', btnColor === cleanHex);
        });
        if (window.applyCrmAccent) {
            window.applyCrmAccent(cleanHex);
        }
    }

    $('.setting-accent-btn').on('click', function () {
        applyColor($(this).data('color'));
    });

    $('#theme_accent_picker').on('input', function () {
        applyColor($(this).val());
    });

    $('#theme_accent_input').on('input', function () {
        var val = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/i.test(val)) {
            applyColor(val);
        }
    });

    $('#btn_reset_defaults').on('click', function () {
        $('.theme-mode-choice[data-mode="light"]').trigger('click');
        applyColor('#0B57D0');
    });

    $('form').on('submit', function () {
        var mode = $('#theme_mode_input').val();
        var accent = $('#theme_accent_input').val();
        if (mode) try { localStorage.setItem('crm_theme', mode); } catch(e) {}
        if (accent) try { localStorage.setItem('crm_accent', accent); } catch(e) {}
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endpush
