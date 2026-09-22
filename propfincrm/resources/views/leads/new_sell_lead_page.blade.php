@section('page-title', 'Create Sell Lead')
@section('main-class', 'p-0')
@extends('layouts.master')

@section('styles')
    <style>
        #new-sell-page { min-height: calc(100vh - 40px); background: #fff; }
        #new-sell-page .new-sell-shell { width: 100%; margin: 0; }
        #new-sell-page .new-sell-card { padding: 16px 20px 20px !important; border: 0; border-radius: 0; box-shadow: none; background: #fff; }
        #new-sell-page .new-sell-back { display: inline-flex; align-items: center; gap: 7px; height: 34px; margin: 0; padding: 0 11px; border: 1px solid #dbe3ee; border-radius: 6px; color: #475569; background: #fff; font-size: 12px; font-weight: 500; text-decoration: none; }
        #new-sell-page .new-sell-back:hover { color: #1e293b; background: #f8fafc; }
        #new-sell-page .new-sell-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:62px; padding:14px 20px; border-bottom:1px solid #e2e8f0; }
        #new-sell-page .new-sell-heading { margin: 0 0 14px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        #new-sell-page .new-sell-heading h1 { margin: 0; color: #1e293b; font-size: 15px; font-weight: 600; }
        #new-sell-page .new-sell-heading p { margin: 3px 0 0; color: #64748b; font-size: 12px; }
        #new-sell-page .row { display: grid; grid-template-columns: minmax(0, 1fr); gap: 10px 12px; margin: 0 !important; }
        #new-sell-page form.form-horizontal { margin: 0; }
        #new-sell-page form.form-horizontal .row > [class*="col-"] { width: auto !important; min-width: 0; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        #new-sell-page form.form-horizontal > .form-group { grid-column: 1 / -1; width: fit-content; margin: 2px 0 0 !important; padding: 0 !important; }
        #new-sell-page .form-group, #new-sell-page .form-group.mx-0 { margin: 0 !important; }
        #new-sell-page label.control-label { display: block; margin-bottom: 4px; color: #334155; font-size: 12px; font-weight: 500; line-height: 16px; text-align: left !important; }
        #new-sell-page .form-control, #new-sell-page .selector { box-sizing: border-box; width: 100%; min-width: 0; height: 36px; min-height: 36px; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 5px; color: #334155; font-size: 12px; line-height: 18px; box-shadow: none; }
        #new-sell-page .form-control:focus, #new-sell-page .selector:focus { border-color: #f97316; outline: 0; box-shadow: 0 0 0 2px rgba(249,115,22,.12); }
        #new-sell-page .select2-container { width: 100% !important; }
        #new-sell-page .select2-selection { box-sizing: border-box; height: 36px !important; min-height: 36px !important; border: 1px solid #cbd5e1 !important; border-radius: 5px !important; box-shadow: none !important; }
        #new-sell-page .select2-selection__rendered { padding-left: 10px !important; color: #334155 !important; font-size: 12px !important; line-height: 34px !important; }
        #new-sell-page .select2-selection__arrow { height: 34px !important; }
        #new-sell-page .select2-container--focus .select2-selection, #new-sell-page .select2-container--open .select2-selection { border-color: #f97316 !important; box-shadow: 0 0 0 2px rgba(249,115,22,.12) !important; }
        #new-sell-page #project:empty, #new-sell-page #src:empty { display: none; }
        #new-sell-page .btn-primary { border: 0; border-radius: 5px; padding: 8px 14px; background: #f97316; font-size: 12px; font-weight: 600; box-shadow: none; }
        #new-sell-page .btn-primary:hover { background: #ea580c; color: #fff; }
        @media (max-width: 640px) { #new-sell-page .new-sell-toolbar, #new-sell-page .new-sell-card { padding-left: 12px !important; padding-right: 12px !important; } }
        @media (min-width: 768px) { #new-sell-page .row { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 992px) { #new-sell-page .row { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    </style>
@endsection

@section('content')
    <script>
        function checkOther() {
            var val = $('select[name="project"]').val();
            if (val == 'other') {
                var ot = $('#other_project').val();

                var o = new Option(ot, ot);
                o.selected = true;
                $('select[name="project"]').append(o);

            }
            var val = $('select[name="source"]').val();
            if (val == 'other') {
                var ot = $('#other_source').val();

                var o = new Option(ot, ot);
                o.selected = true;
                $('select[name="source"]').append(o);

            }
            var val = $('select[name="Budget"]').val();
            if (val == 'other') {
                var ot = $('#other_budget').val();

                var o = new Option(ot, ot);
                o.selected = true;
                $('select[name="Budget"]').append(o);

            }
            var val = $('select[name="requirement"]').val();
            if (val == 'other') {
                var ot = $('#other_requirement').val();

                var o = new Option(ot, ot);
                o.selected = true;
                $('select[name="requirement"]').append(o);

            }
            return true;
        }
    </script>
    <div id="new-sell-page"><div class="new-sell-shell">
        <div class="new-sell-toolbar"><a href="{{ url('all_sell_list') }}" class="new-sell-back"><i data-lucide="arrow-left" class="h-4 w-4"></i> All sell leads</a></div>
    <div class="card new-sell-card">
        <div class="new-sell-heading"><h1>Sell property details</h1><p>Add the property, pricing and location information.</p></div>
        {!! Form::open([
                'url' => 'save_sell_lead',
                'onsubmit' => 'return checkOther()',
                'class' => 'form-horizontal',
            ]) !!}
        <div class="row">
            <!-- Email -->

            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('name', 'Name:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('name', $value = null, [
                            'class' => 'form-control',
                            'placeholder' => 'Enter Your Name',
                            'required' => 'required',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('number', 'Contact No.:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('number', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('email', 'Email:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::email('email', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('property_type', 'Property Type', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('property_type', ['Ready to Move-in' => 'Ready to Move-in'], 'S', ['class' => 'selector']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('projec', 'Project', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('project', $project, 'S', ['class' => 'selector']) !!}
                        <div id="project"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('category', 'Category', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('category', ['residential' => 'residential', 'commercial' => 'commercial'], 'S', [
                            'class' => 'selector',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('sub_category', 'Sub Category', ['class' => ' control-label']) !!}
                    <div class="">
                        {!! Form::select(
                            'sub_category',
                            [
                                'Kothi/Villa' => 'Kothi/Villa',
                                'Shop' => 'Shop',
                                'Shop Cum Office' => 'Shop Cum Office',
                                'Flat' => 'Flat',
                                'Land/Plot' => 'Land/Plot',
                                'Multi-storey Apartment' => 'Multi-storey Apartment',
                            ],
                            'S',
                            ['class' => 'selector'],
                        ) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('expected_price', 'Expected Price:', ['class' => ' control-label']) !!}
                    <div class="">
                        {!! Form::text('expected_price', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('primium', 'Premium:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('primium', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('market_price', 'Market_Price:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('market_price', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('booking_price', 'Booking_Price:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('booking_price', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('maintenance_charge', 'Maintenance Charge:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('maintenance_charge', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('other_charge', 'Other Charge:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('other_charge', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('security_deposit', 'Security Deposit:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('security_deposit', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('status', ['New Property' => 'New Property', 'Old Property' => 'Old Property'], 'S', [
                            'class' => 'selector',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('disposition', 'Disposition:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('disposition', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('source', 'Source', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('source', $source, 'S', ['class' => 'selector']) !!}
                        <div id="src"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('property_tag', 'Property Tag', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select(
                            'property_tag',
                            ['Hot Property' => 'Hot Property', 'Warm Property' => 'Warm Property', 'Cold Property' => 'Cold Property'],
                            'S',
                            ['class' => 'selector'],
                        ) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('address', 'Address:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('address', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('block', 'Block/Tower:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('block', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('country', 'Country', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('country', $countries, null, ['class' => 'selector']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('state', 'State', ['class' => 'control-label']) !!}
                    <div class="">
                        <select name="state" id="state" class="selector" required="required">
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('city', 'City:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('city', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('locality', 'Locality:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('locality', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('super_area', 'Super Area:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('super_area', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('plot_area', 'Plot Area:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('plot_area', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('cover_area', 'Cover Area:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('cover_area', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('carpet_area', 'Carpet Area:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('carpet_area', $value = null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('bedrooms', 'Bedrooms', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::select('bedrooms', ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5], 'S', ['class' => 'selector']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('bathroom', 'Bathrooms:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('bathroom', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('work_station', 'Workstation:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('work_station', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('cabins', 'Cabins:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('cabins', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('facing', 'Facing:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('facing', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('furnish', 'Furnish:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('furnish', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('floor', 'Floor Area:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('floor', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('built_year', 'Built Year:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('built_year', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('car_parking', 'Car Parking:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('car_parking', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('remarks', 'Remarks:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('remarks', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('description', 'Description:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('description', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group mx-0">
                    {!! Form::label('other_feature', 'Other Feature:', ['class' => 'control-label']) !!}
                    <div class="">
                        {!! Form::text('other_feature', $value = null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group ms-2" style="width: fit-content">
            {!! Form::submit(__('Create New Sell'), ['class' => 'btn btn-primary']) !!}
        </div>



        {!! Form::close() !!}

    </div>
    {{-- {!! Form::open([ --}}
    {{-- 'route' => 'leads.store', 'onsubmit' => 'return checkOther()', 'class' => 'form-horizontal' --}}
    {{-- ]) !!} --}}
    {{-- <!-- Email --> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('email', 'Email:', ['class' => 'col-lg-2 control-label']) !!} --}}
    {{-- <div class="col-lg-10"> --}}
    {{-- {!! Form::email('email', $value = null, ['class' => 'form-control', 'placeholder' => 'email']) !!} --}}
    {{-- </div> --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Name'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('name', null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Contact No'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('contact_no', null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Email'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('email', null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Source'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::select('source',$source, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div id="src"> --}}

    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Country'), ['class' => 'control-label']) !!} --}}

    {{-- {!! Form::select('country',$countries, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('State'), ['class' => 'control-label']) !!} --}}
    {{-- <select name="state" id="state" class="form-control" > --}}
    {{-- </select> --}}

    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('City'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('city',null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Location'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('location', null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Pin/Zip Code'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::text('pin', null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Project'), ['class' => 'control-label']) !!} --}}

    {{-- {!! Form::select('project',$project, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div id="project"> --}}

    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Requirement'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::select('requirement',$requirement, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div id="req"> --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('title', __('Budget'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::select('Budget',$budget, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div id="budget"> --}}

    {{-- </div> --}}

    {{-- <div class="form-inline"> --}}
    {{-- <div class="form-group col-lg-3 removeleft"> --}}
    {{-- {!! Form::label('status', __('Status'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::select('status', array( --}}
    {{-- '1' => 'Contact Client', '2' => 'Completed'), null, ['class' => 'form-control'] ) --}}
    {{-- !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group col-lg-4 removeleft"> --}}
    {{-- {!! Form::label('contact_date', __('Deadline'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::date('contact_date', \Carbon\Carbon::now()->addDays(7), ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group col-lg-5 removeleft removeright"> --}}
    {{-- {!! Form::label('contact_time', __('Time'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::time('contact_time', '11:00', ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}

    {{-- </div> --}}


    {{-- <div class="form-group"> --}}
    {{-- {!! Form::label('user_assigned_id', __('Assign user'), ['class' => 'control-label']) !!} --}}
    {{-- {!! Form::select('user_assigned_id', $users, null, ['class' => 'form-control']) !!} --}}
    {{-- </div> --}}
    {{-- <div class="form-group"> --}}

    {{-- {!! Form::hidden('client_id', 1) !!} --}}

    {{-- </div> --}}

    {{-- {!! Form::submit(__('Create new Lead'), ['class' => 'btn btn-primary']) !!} --}}

    {{-- {!! Form::close() !!} --}}
    </div></div>
@stop

@section('js')
<script>
$(function () {
    function initialiseSellSelects() {
        if (typeof $.fn.select2 !== 'function') return false;
        $('#new-sell-page select').each(function () {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) return;
            $select.select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Search and select', minimumResultsForSearch: 0 });
        });
        return true;
    }
    if (!initialiseSellSelects()) {
        var attempts = 0;
        var waiter = setInterval(function () { if (initialiseSellSelects() || ++attempts >= 20) clearInterval(waiter); }, 250);
    }
    $(document).ajaxComplete(function (_event, _xhr, settings) {
        if (settings.url && settings.url.indexOf('/ajax/') !== -1) {
            initialiseSellSelects();
            $('select[name="state"]').trigger('change.select2');
        }
    });
});
</script>
@endsection
