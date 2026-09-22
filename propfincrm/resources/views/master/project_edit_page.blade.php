@extends('layouts.master')
@section('page-title','Edit Project Name')
{{-- @section('heading')
    <h1>{{ __('Edit Project Name') }}</h1>
@stop --}}
@section('content')
<div class="col-12 d-flex">
    <div class="pull-left me-3 mb-3">
        <a href="{{ url('project_list') }}" type="button" class="btn btn-primary " ><i class="fa fa-code-branch me-2"></i> Project
        </a>
    </div>
</div>

{{-- <div class="col-12 d-flex">
    <div class="pull-left me-3 mb-3">
        <a href="{{ url('project_list') }}" type="button" class="btn btn-primary">
            <img class="team-image" src="{{ asset('images/master.png') }}" style="height: 20px" alt="">
            <span class="team-text">Project </span>
        </a>
    </div>
</div> --}}
<div class="card Edit-Project-Name">
    <div class="row">
        <div class="col-md-12-col-sm-12 col-xs-12">
            <form  data-parsley-validate class="form-horizontal form-label-left"
                   action="{{url('save_edit_project')}}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="project_edit_id" value="{{$project_list->id}}">
                {{--<input type="hidden" name="department_id" value="{{$designation_data->DepartmentId}}">--}}

                <div class="col-md-8 col-sm-8 col-xs-12">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12"
                               for="project_name">Project Name<span class="required">*</span>
                        </label>

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{--<input type="text" name="user_password" value="@if(isset($datas->CompName)) {{$datas->CompName}} @endif"   required>--}}

                            <input type="text" id="project_name" name="project_name"
                                   class="form-control col-md-7 col-xs-12"
                                   value="@if(isset($project_list->	project_name)) {{$project_list->project_name}} @endif"
                                   @if(isset($view)) disabled="disabled" @endif required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12"
                               for="project_location">Project Location<span class="required">*</span>
                        </label>

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{--<input type="text" name="user_password" value="@if(isset($datas->CompName)) {{$datas->CompName}} @endif"   required>--}}

                            <input type="text" id="project_location" name="project_location"
                                   class="form-control col-md-7 col-xs-12"
                                   value="@if(isset($project_list->project_location)) {{$project_list->project_location}} @endif"
                                   @if(isset($view)) disabled="disabled" @endif required>
                        </div>
                    </div>




                    @if(!isset($view))
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                {{--<a href="{{url('user_panel')}}" class="btn btn-primary"--}}
                                {{--type="button">Cancel</a>--}}
                                <button type="submit" class="btn btn-info">Save</button>

                            </div>
                        </div>
                    @endif

                    {{--@if(isset($view))--}}
                    {{--<div class="form-group">--}}
                    {{--<div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">--}}

                    {{--<a href="{{url('next_view_department?id='.$data->Id)}}" class="btn btn-primary"--}}
                    {{--type="button">Back</a>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                </div>
            </form>

        </div>
    </div>
</div>
    @endsection