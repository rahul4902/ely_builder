@section('page-title','Create new lead')
@extends('layouts.master')
{{-- @section('heading')
    <h1>{{ __('Create lead') }}</h1>


@stop --}}

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
        }
    </script>

<div class="card p-5">
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
            <div id="src">

            </div>
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
                {!! Form::text('city', null, ['class' => 'form-control']) !!}
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
            <div id="project">
    
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Requirement'), ['class' => 'control-label']) !!}
                {!! Form::select('requirement', $requirement, null, ['class' => 'selector']) !!}
            </div>
            <div id="req">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                {!! Form::label('title', __('Budget'), ['class' => 'control-label']) !!}
                {!! Form::select('Budget', $budget, null, ['class' => 'selector']) !!}
            </div>
        </div>
        <div id="budget">

        </div>
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
                    ['class' => 'selector'],
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
@stop
