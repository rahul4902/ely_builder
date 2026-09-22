@extends('layouts.master')
@section('page-title','Edit Reqirement')
{{-- @section('heading')
    <h1>{{ __('Edit Reqirement') }}</h1>
@stop --}}
@section('content')
<div class="col-12 d-flex">
    <div class="pull-left me-3 mb-3">
        <a href="{{ url('source_list') }}" type="button" class="btn btn-primary " ><i class="fa-brands fa-osi me-2"></i> Source  
        </a>
    </div>
</div>
<div class="card Edit-Project-Name">
    <div class="row">
        <div class="col-md-12-col-sm-12 col-xs-12">
            <form  data-parsley-validate class="form-horizontal form-label-left"
                   action="{{url('save_edit_source')}}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="source_edit_id" value="{{$source_lists->id}}">
                {{--<input type="hidden" name="department_id" value="{{$designation_data->DepartmentId}}">--}}

                <div class="col-md-8 col-sm-8 col-xs-12">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-12"
                               for="source_name">Source Name<span class="required">*</span>
                        </label>

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            {{--<input type="text" name="user_password" value="@if(isset($datas->CompName)) {{$datas->CompName}} @endif"   required>--}}

                            <input type="text" id="source_name" name="source_name"
                                   class="form-control col-md-7 col-xs-12"
                                   value="@if(isset($source_lists->source_name)) {{$source_lists->source_name}} @endif"
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