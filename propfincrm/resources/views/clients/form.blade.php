{{-- clients/form.blade.php
    Variables expected:
      $data            – session data array (from VAT lookup; may be null)
      $industries      – array for industry_id select
      $users           – array for user_id select
      $submitButtonText – label for the submit button
      $client          – (optional) model instance for edit mode
--}}

{{-- Row 1: Name --}}
<div class="row">
    <div class="col-12">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Name') }}"
            name="name"
            id="name"
            :value="old('name', isset($client) ? $client->name : (isset($data['owners'][0]['name']) ? $data['owners'][0]['name'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Contact name') }}"
        />
    </div>
</div>

{{-- Row 2: VAT + Company Name --}}
<div class="row">
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('VAT') }}"
            name="vat"
            id="vat"
            :value="old('vat', isset($client) ? $client->vat : (isset($data['vat']) ? $data['vat'] : null))"
            class="crm-form-control"
            placeholder="{{ __('VAT number') }}"
        />
    </div>
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Company Name') }}"
            name="company_name"
            id="company_name"
            :value="old('company_name', isset($client) ? $client->company_name : (isset($data['name']) ? $data['name'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Company name') }}"
        />
    </div>
</div>

{{-- Row 3: Email --}}
<div class="row">
    <div class="col-12">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Email') }}"
            name="email"
            id="email"
            type="email"
            :value="old('email', isset($client) ? $client->email : (isset($data['email']) ? $data['email'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Email address') }}"
        />
    </div>
</div>

{{-- Row 4: Address --}}
<div class="row">
    <div class="col-12">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Address') }}"
            name="address"
            id="address"
            :value="old('address', isset($client) ? $client->address : (isset($data['address']) ? $data['address'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Street address') }}"
        />
    </div>
</div>

{{-- Row 5: Zipcode + City --}}
<div class="row">
    <div class="col-md-4">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Zipcode') }}"
            name="zipcode"
            id="zipcode"
            :value="old('zipcode', isset($client) ? $client->zipcode : (isset($data['zipcode']) ? $data['zipcode'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Zip / Postal code') }}"
        />
    </div>
    <div class="col-md-8">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('City') }}"
            name="city"
            id="city"
            :value="old('city', isset($client) ? $client->city : (isset($data['city']) ? $data['city'] : null))"
            class="crm-form-control"
            placeholder="{{ __('City') }}"
        />
    </div>
</div>

{{-- Row 6: Primary + Secondary Number --}}
<div class="row">
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Primary Number') }}"
            name="primary_number"
            id="primary_number"
            :value="old('primary_number', isset($client) ? $client->primary_number : (isset($data['phone']) ? $data['phone'] : null))"
            class="crm-form-control"
            placeholder="{{ __('Primary phone') }}"
        />
    </div>
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Secondary Number') }}"
            name="secondary_number"
            id="secondary_number"
            :value="old('secondary_number', isset($client) ? $client->secondary_number : null)"
            class="crm-form-control"
            placeholder="{{ __('Secondary phone') }}"
        />
    </div>
</div>

{{-- Row 7: Company Type --}}
<div class="row">
    <div class="col-12">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Company Type') }}"
            name="company_type"
            id="company_type"
            :value="old('company_type', isset($client) ? $client->company_type : (isset($data['companydesc']) ? $data['companydesc'] : null))"
            class="crm-form-control"
            placeholder="{{ __('E.g. Holding, Ltd, etc.') }}"
        />
    </div>
</div>

{{-- Row 8: Industry + Assigned User --}}
<div class="row">
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Industry') }}"
            name="industry_id"
            id="industry_id_field"
            type="select"
            :options="$industries"
            :value="old('industry_id', isset($client) ? $client->industry_id : null)"
            class="crm-form-control"
        />
    </div>
    <div class="col-md-6">
        <x-form.input
            layout="standard"
            field-class="crm-form-group"
            label="{{ __('Assign User') }}"
            name="user_id"
            id="user_id_field"
            type="select"
            :options="$users"
            :value="old('user_id', isset($client) ? optional($client->user)->id : null)"
            class="crm-form-control"
        />
    </div>
</div>

{{-- Submit + Cancel --}}
<div class="crm-form-footer">
    <a href="{{ route('clients.index') }}" class="crm-btn">{{ __('Cancel') }}</a>
    <button type="submit" class="crm-btn crm-btn-primary">{{ $submitButtonText }}</button>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    if (typeof $.fn.select2 === 'function') {
        $('#industry_id_field, #user_id_field').each(function () {
            var $el = $(this);
            if (!$el.hasClass('select2-hidden-accessible')) {
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '{{ __("Select\u2026") }}'
                });
            }
        });
    }
});
</script>
@endpush
