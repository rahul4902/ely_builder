@section('page-title','Create new lead')
@section('main-class', 'p-0')
@extends('layouts.master')

@section('styles')
    <style>
        #lead-create-page { background: #f8fafc; min-height: calc(100vh - 40px); padding: 10px; }
        #lead-create-page .lead-create-card { max-width: 1240px; margin: 0 auto; padding: 18px !important; border: 1px solid #e2e8f0; border-radius: 0; box-shadow: none; }
        /* Replace Bootstrap's row/column gutters with a compact Tailwind-like grid. */
        #lead-create-page .row { display: grid !important; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 10px 12px; margin: 0 !important; }
        #lead-create-page .row > [class*="col-"], #lead-create-page .row > [id] { width: auto !important; min-width: 0; max-width: none !important; padding: 0 !important; margin: 0 !important; }
        #lead-create-page .row .col-lg-12 { width: auto !important; max-width: none !important; padding: 0 !important; }
        @media (min-width: 768px) { #lead-create-page .row { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (min-width: 992px) { #lead-create-page .row { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        #lead-create-page .form-group, #lead-create-page .removeleft, #lead-create-page .mt-4 { margin: 0 !important; }
        #lead-create-page #budget:empty, #lead-create-page #src:empty, #lead-create-page #project:empty, #lead-create-page #req:empty { display: none; }
        #lead-create-page .lead-create-card > :first-child { margin-bottom: 14px !important; padding-bottom: 10px !important; }
        #lead-create-page label.control-label { display: block; margin-bottom: 4px; color: #334155; font-size: 12px; font-weight: 500; line-height: 16px; }
        #lead-create-page .form-control, #lead-create-page .selector { box-sizing: border-box; width: 100%; min-width: 0; height: 36px; min-height: 36px; border: 1px solid #cbd5e1; border-radius: 5px; color: #334155; font-size: 12px; line-height: 18px; padding: 8px 10px; box-shadow: none; }
        #lead-create-page .form-control:focus, #lead-create-page .selector:focus { border-color: #f97316; box-shadow: 0 0 0 2px rgba(249,115,22,.12); outline: 0; }
        #lead-create-page .select2-container { width: 100% !important; }
        #lead-create-page .select2-selection { box-sizing: border-box; height: 36px !important; min-height: 36px !important; border: 1px solid #cbd5e1 !important; border-radius: 5px !important; box-shadow: none !important; }
        #lead-create-page .select2-selection__rendered { color: #334155 !important; font-size: 12px !important; line-height: 34px !important; padding-left: 10px !important; }
        #lead-create-page .select2-selection__arrow { height: 34px !important; }
        #lead-create-page .select2-container--focus .select2-selection, #lead-create-page .select2-container--open .select2-selection { border-color: #f97316 !important; box-shadow: 0 0 0 2px rgba(249,115,22,.12) !important; }
        #lead-create-page .btn-primary { margin-top: 12px; border: 0; border-radius: 5px; background: #f97316; font-size: 12px; font-weight: 600; padding: 8px 14px; box-shadow: none; }
        #lead-create-page .btn-primary:hover { background: #ea580c; color: #fff !important; }
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
<div id="lead-create-page">
<div class="mx-auto max-w-[1240px]">
<div class="col-12 d-flex">
    <div class="pull-left me-3 mb-3">
        <a href="{{ url('lead_list') }}" class="inline-flex items-center gap-2 border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-700 no-underline transition hover:bg-slate-50" ><i data-lucide="arrow-left" class="h-4 w-4"></i> All leads
        </a>
    </div>
</div>
<div class="card lead-create-card p-4 p-md-5">
    <div class="mb-4 border-b border-slate-200 pb-3">
        <h1 class="mb-1 text-base font-semibold text-slate-800">Lead details</h1>
        <p class="mb-0 text-xs text-slate-500">Add contact, requirement and follow-up information.</p>
    </div>
    @if (isset($user))
        <!--        --><?php //print_r($user); exit;
        ?>

{!! Form::model($user, ['url' => 'update_lead', 'files' => true]) !!}
@else
        {!! Form::open([
            'route' => 'leads.store',
            'onsubmit' => 'return checkOther()',
        ]) !!}
    @endif
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Name'), ['class' => 'control-label']) !!}
                {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </div>
            @if (isset($user))
                <input type="hidden" name="update_id" value="{{ $user->id }}">
            @endif
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Mobile No'), ['class' => 'control-label']) !!}
                {!! Form::text('contact_no', null, [
                    'maxlength' => '10',
                    'class' => 'form-control',
                    'placeholder' => ' Enter Only Digit',
                    'required' => 'required',
                ]) !!}
                @if (isset($flag))
                    <p style="color: red">Number Is Already Register</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Email'), ['class' => 'control-label']) !!}
                {!! Form::email('email', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Source'), ['class' => 'control-label']) !!}
                {!! Form::select('source', $source, null, ['class' => 'selector']) !!}
            </div>
            <div id="src"></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Country'), ['class' => 'control-label']) !!}

                {!! Form::select('country', $countries, 'India', ['class' => 'selector']) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('State'), ['class' => 'control-label']) !!}
                <select name="state" id="state" class="selector">
                </select>

            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('City'), ['class' => 'control-label']) !!}
                <select name="city" class="selector city-selector" data-tags="true">
                    <option value=""></option>
                    @php($savedCity = old('city', isset($user) ? $user->city : ''))
                    @if ($savedCity !== '')
                        <option value="{{ $savedCity }}" selected>{{ $savedCity }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Location'), ['class' => 'control-label']) !!}
                {!! Form::text('location', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Pin/Zip Code'), ['class' => 'control-label']) !!}
                {!! Form::text('pin', null, [
                    'pattern' => '[0-9]{6}',
                    'maxlength' => '6',
                    'class' => 'form-control',
                    'placeholder' => 'Enter Only Digit Minimum 6',
                ]) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Project'), ['class' => 'control-label']) !!}

                {!! Form::select('project', $project, null, ['class' => 'selector']) !!}
            </div>
        <div id="project"></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Requirement'), ['class' => 'control-label']) !!}
                {!! Form::select('requirement', $requirement, null, ['class' => 'selector']) !!}
            </div>
        <div id="req"></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Budget'), ['class' => 'control-label']) !!}
                {!! Form::select('Budget', $budget, null, ['class' => 'selector']) !!}
            </div>
        </div>
        <div id="budget"></div>
        <div class="col-12 col-md-4">
            <div class="removeleft">
                {!! Form::label('status', __('Status'), ['class' => 'control-label']) !!}
                {!! Form::select(
                    'status',
                    [
                        '1' => 'Contact Client',
                        '2' => 'Completed',
                    ],
                    null,
                    ['class' => 'selector']
                ) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="removeleft">
                {!! Form::label('contact_date', __('Deadline'), ['class' => 'control-label']) !!}
                {!! Form::date('contact_date', \Carbon\Carbon::now()->addDays(7), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="removeleft removeright">
                {!! Form::label('contact_time', __('Time'), ['class' => 'control-label']) !!}
                {!! Form::time('contact_time', '11:00', ['class' => 'form-control']) !!}
            </div>
        </div>

        <?php
        if ($users->isEmpty()) {
            $user = Auth::user()->userRole->getUserName->name;
            $user_id = Auth::user()->userRole->getUserName->id;
        } ?>
        <div class="col-12 col-md-4 mt-4">
            <div class="form-group">
                {!! Form::label('user_assigned_id', __('Assign user'), ['class' => 'control-label']) !!}


                <!--{!! Form::select('user_assigned_id', $users, null, ['class' => 'form-control']) !!}-->
                <select name="user_assigned_id" class="selector">
                    @if ($users->isEmpty())
                        <option value="{{ $user_id }}"> {{ $user }}</option>
                    @else
                        @foreach ($users as $key => $u)
                            <option value="{{ $key }}"> {{ $u }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-12 col-md-4 mt-4">
            <div class="form-group">
                {!! Form::label('lead_type', __('Lead Type'), ['class' => 'control-label']) !!}
                {!! Form::select('lead_type', ['Hot' => 'Hot', 'Cold' => 'Cold'], null, ['class' => 'selector']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">

        {!! Form::hidden('client_id', 1) !!}

    </div>

    {!! Form::submit(__('Create new Lead'), ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}

</div>
 </div>
</div>
@stop

@push('scripts')
<script>
    $(function () {
        function initialiseSearchSelects() {
            if (typeof $.fn.select2 !== 'function') {
                return false;
            }

            $('#lead-create-page select').each(function () {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $select.hasClass('city-selector') ? 'Search or type city' : 'Search and select',
                allowClear: false,
                minimumResultsForSearch: 0,
                tags: $select.data('tags') === true
            });
            });
            return true;
        }

        if (!initialiseSearchSelects()) {
            var select2Attempts = 0;
            var select2Waiter = setInterval(function () {
                select2Attempts++;
                if (initialiseSearchSelects() || select2Attempts >= 20) {
                    clearInterval(select2Waiter);
                }
            }, 250);
        }

        // State options arrive asynchronously; keep its searchable Select2 UI in sync.
        $(document).ajaxComplete(function (_event, _xhr, settings) {
            if (settings.url && settings.url.indexOf('/ajax/') !== -1) {
                initialiseSearchSelects();
                $('select[name="state"]').trigger('change.select2');
            }
        });

        $('select[name="country"]').on('change', function () {
            setTimeout(function () { $('select[name="state"]').trigger('change.select2'); }, 400);
        });
        setTimeout(function () { $('select[name="state"]').trigger('change.select2'); }, 700);
    });
</script>
@endpush
