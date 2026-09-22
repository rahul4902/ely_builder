@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'autocomplete' => null,
    'required' => false,
    'autofocus' => false,
    'options' => [],
    'icon' => null,
    'fieldClass' => 'auth-field',
    'layout' => 'auth',
    'errorClass' => null,
])

@php
    $id = $attributes->get('id', $name);
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $hasError = $errorBag->has($name);
    $inputClass = trim(($hasError ? 'is-invalid ' : '') . $attributes->get('class', ''));
    $errorClass = $errorClass ?: ($layout === 'auth' ? 'auth-error' : 'mt-1 block text-xs text-red-600');
@endphp

@if ($type === 'checkbox')
    <label class="{{ $fieldClass }}">
        <input id="{{ $id }}" class="form-checkbox {{ $inputClass }}" type="checkbox" name="{{ $name }}" value="{{ $value ?? 1 }}" aria-invalid="{{ $hasError ? 'true' : 'false' }}" @checked(old($name, $value)) @required($required) {{ $attributes->except(['id', 'class']) }}>
        <span>{{ $label }}</span>
    </label>
    @if ($hasError)<span class="auth-error">{{ $errorBag->first($name) }}</span>@endif
@else
    <div class="{{ $fieldClass }}">
        @if ($label)<label for="{{ $id }}">{{ $label }}</label>@endif
        <div @class(['auth-control' => $layout === 'auth'])>
            @if ($icon === 'email')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            @elseif ($icon === 'password')
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
            @endif
            @if ($type === 'select')
                <select id="{{ $id }}" class="{{ $inputClass }}" name="{{ $name }}" aria-invalid="{{ $hasError ? 'true' : 'false' }}" @required($required) @autofocus($autofocus) @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif {{ $attributes->except(['id', 'class']) }}>
                    @foreach ($options as $optionValue => $optionLabel)<option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>{{ $optionLabel }}</option>@endforeach
                </select>
            @elseif ($type === 'textarea')
                <textarea id="{{ $id }}" class="{{ $inputClass }}" name="{{ $name }}" aria-invalid="{{ $hasError ? 'true' : 'false' }}" @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif @required($required) @autofocus($autofocus) {{ $attributes->except(['id', 'class']) }}>{{ old($name, $value) }}</textarea>
            @else
                <input id="{{ $id }}" class="{{ $inputClass }}" type="{{ $type }}" name="{{ $name }}" @if(!in_array($type, ['password', 'file'], true)) value="{{ old($name, $value) }}" @endif aria-invalid="{{ $hasError ? 'true' : 'false' }}" @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif @required($required) @autofocus($autofocus) {{ $attributes->except(['id', 'class']) }}>
            @endif
        </div>
        @if ($hasError)<span class="{{ $errorClass }}">{{ $errorBag->first($name) }}</span>@endif
    </div>
@endif
