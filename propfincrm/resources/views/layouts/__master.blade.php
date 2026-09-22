<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ElyLeads</title>
    {{-- <link href="{{ URL::asset('css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ URL::asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    {{-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet"> --}}
    {{-- <link href="{{ URL::asset('css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"> --}}
    <link href="{{ asset('datatable/datatable.min.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ URL::asset('css/dropzone.css') }}" rel="stylesheet" type="text/css">


    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!}
    </script>

    {{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script> --}}
    @yield('styles')
    <script>
        var base_url = '{{ url('/') }}';
    </script>

    {{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css'></script> --}}

</head>

<body>
  
    <div id="wrapper"></div>
    {{-- <div class=""> --}}



    {{-- <div class="navbar navbar-default navbar-top">
            <!--NOTIFICATIONS START-->
            <div class="dropdown">
                <a id="dLabel" role="button" data-toggle="dropdown" href="/page.html">
                    <i class="glyphicon glyphicon-bell"><span id="notifycount"></span></i>
                </a>
                <ul class="dropdown-menu notify-drop  notifications" role="menu" aria-labelledby="dLabel">
                    <div class="notification-heading">
                        <h4 class="menu-title">Notifications</h4>
                        <h4 class="menu-title pull-right"><a href="{{ url('notifications/markall') }}">Mark all as
                                read</a><i class="glyphicon glyphicon-circle-arrow-right"></i></h4>
                    </div>
                    <li class="divider"></li>
                    <div class="notifications-wrapper">
                        <span id="notification-item"></span>
                        @push('scripts')
                            <script>
                                id = {};

                                function postRead(id) {
                                    $.ajax({
                                        type: 'post',
                                        url: '{{ url('/notifications/markread') }}',
                                        data: {
                                            id: id,
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    });

                                }

                                $(function() {
                                    $.get("{{ url('/notifications/getall') }}", function(notifications) {
                                        var notifyItem = document.getElementById('notification-item');
                                        var bell = document.getElementById('notifycount');
                                        var msg = "";
                                        var count = 0;
                                        $.each(notifications, function(index, notification) {
                                            count++;
                                            var id = notification['id'];
                                            var url = notification['data']['url'];

                                            msg += `<div>
        <a class="content"  id="notify" href="{{ url('notifications') }}/` + id + `">
        ` +
                                                notification['data']['message'] +
                                                ` </a></div>
        <hr class="notify-line"/>`;
                                            notifyItem.innerHTML = msg;

                                            /**         notifyItem.onclick = (function(id){
                                                                     return function(){
                                                                         postRead(id);
                                                                     }})(id); **/

                                        });
                                        bell.innerHTML = count;
                                    })

                                });
                            </script>
                        @endpush
                    </div>

                </ul>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $.ajax({
                            url: base_url + "/ajax/India",
                            type: "GET",
                            dataType: "json",
                            success: function(data) {

                                $('select[name="state"]').empty();

                                $.each(data, function(key, value) {
                                    $('select[name="state"]').append('<option value="' + key + '">' +
                                        value + '</option>');
                                });

                            }
                        });
                        $('select[name="country"]').on('change', function() {
                            var countryid = $(this).val();
                            if (countryid) {
                                $.ajax({
                                    url: base_url + "/ajax/" + countryid,
                                    type: "GET",
                                    dataType: "json",
                                    success: function(data) {

                                        $('select[name="state"]').empty();

                                        $.each(data, function(key, value) {
                                            $('select[name="state"]').append('<option value="' +
                                                key + '">' + value + '</option>');
                                        });

                                    }
                                });
                            } else {

                                $('select[name="state"]').empty();
                            }
                        });
                    });


                    $(document).ready(function() {
                        $('select[name="requirement"]').on('change', function() {
                            var req = $(this).val();
                            if (req == "other") {


                                $('#req').html(
                                    ' <div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_requirement']) !!}</div> </div>'
                                );

                            } else {

                                $('#req').empty();


                            }
                        });
                    });

                    $(document).ready(function() {
                        $('select[name="source"]').on('change', function() {
                            var src = $(this).val();
                            if (src == "other") {
                                $('#src').html(
                                    '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_source']) !!}</div> </div>'
                                );

                            } else {

                                $('#src').empty();


                            }
                        });
                    });
                    $(document).ready(function() {
                        $('select[name="project"]').on('change', function() {
                            var pro = $(this).val();
                            //              alert("value:" + pro);
                            if (pro == "other") {

                                $('#project').html(
                                    '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_project']) !!}</div> </div>'
                                );

                            } else {

                                $('#project').empty();


                            }
                        });
                    });
                    $(document).ready(function() {
                        $('select[name="Budget"]').on('change', function() {
                            var bgt = $(this).val();
                            if (bgt == "other") {
                                $('#budget').html(
                                    '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_budget']) !!}</div> </div>'
                                );

                            } else {

                                $('#budget').empty();


                            }
                        });
                    });

                    $(document).ready(function() {
                        var rolesvalue = $('#roles').val();
                        if (rolesvalue == 3) {
                            $('.teamlead').show();
                            $('.teamleadedit').show();
                        } else {
                            $('.teamlead').hide();
                            $('.teamleadedit').hide();
                            // $('#roles').val('');
                        }
                        $('#roles').on('change', function() {
                            var value = $(this).val();
                            if (value == 3) {
                                $('.teamlead').show();
                                $('.teamleadedit').show();
                            } else {
                                $('.teamlead').hide();
                                $('.teamleadedit').hide();
                                // $('#roles').val('');
                            }

                        });
                    });
                </script>
            </div>
            <!--NOTIFICATIONS END-->
            <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div> --}}

    {{-- </div> --}}


    <div class="d-flex w-100">



        <!-- /#sidebar-wrapper -->
        <!-- Sidebar menu -->

        {{-- @include('include.sidebar') --}}
        {{-- <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm" role="navigation">
            <div class="list-group panel">
                <p class=" list-group-item" title=""><img src="{{ url('images/flarepoint_logo.png') }}"
                        alt=""></p>
                <a href="{{ route('dashboard', \Auth::id()) }}" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-dashboard"></i> {{ __('Dashboard') }} </a>
                <a href="{{ route('users.show', \Auth::id()) }}" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-user"></i> {{ __('Profile') }} </a>



                <a href="#user" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="fa fa-users"></i> {{ __('Users') }} </a>
                <div class="collapse" id="user">
                    <a href="{{ route('users.index') }}" class="list-group-item childlist">{{ __('Users All') }}</a>
                    @if (Entrust::can('user-create'))
                        <a href="{{ route('users.create') }}"
                            class="list-group-item childlist">{{ __('New User') }}</a>
                    @endif
                </div>

                <a href="#leads" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-hourglass"></i> {{ __('Leads') }}</a>
                <div class="collapse" id="leads">
                    <a href="{{ url('lead_list') }}" class="list-group-item childlist">{{ __('All Leads') }}</a>
                    @if (Entrust::can('lead-create'))
                        <a href="{{ route('leads.create') }}"
                            class="list-group-item childlist">{{ __('New Lead') }}</a>
                    @endif
                    <a href="{{ url('all_sell_list') }}"
                        class="list-group-item childlist">{{ __('All Sell Lead') }}</a>
                    <a href="{{ url('all_meeting_and_visits') }}"
                        class="list-group-item childlist">{{ __('Meeting & Visits') }}</a>
                    <a href="{{ url('call_logs') }}" class="list-group-item childlist">{{ __('Call Logs') }}</a>
                    <a href="{{ url('attendance') }}" class="list-group-item childlist">{{ __('Attendance') }}</a>
                    <a href="{{ route('lead_list', 'ReverseLead') }}" class="list-group-item childlist">{{ __('Reverse Leads') }}</a>
                    <a href="{{ url('new_sell_page') }}"
                        class="list-group-item childlist">{{ __('New Sell Lead') }}</a>
                    @if (Entrust::hasRole('administrator'))
                        <a href="{{ route('leads.bulk.upload.index') }}"
                            class="list-group-item childlist">{{ __('Bulk Upload') }}</a>
                        <a href="{{ route('lead_list', 'DumpLead') }}"
                            class="list-group-item childlist">{{ __('Dump Leads') }}</a>
                    @endif

                </div>


                @if (Entrust::hasRole('administrator'))
                    <a href="#master" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                            class="glyphicon glyphicon-hourglass"></i> {{ __('Master') }}</a>
                    <div class="collapse" id="master">
                        <a href="{{ url('project_list') }}"
                            class="list-group-item childlist">{{ __('Project') }}</a>
                        <a href="{{ url('requirement_list') }}"
                            class="list-group-item childlist">{{ __('Requirement') }}</a>
                        <a href="{{ url('budget_list') }}" class="list-group-item childlist">{{ __('Budget') }}</a>
                        <a href="{{ url('source_list') }}" class="list-group-item childlist">{{ __('Source') }}</a>

                    </div>
                @endif

                @if (Entrust::hasRole('administrator'))
                    <a href="#settings" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                            class="glyphicon glyphicon-cog"></i> {{ __('Settings') }}</a>
                    <div class="collapse" id="settings">
                        <a href="{{ route('settings.index') }}"
                            class="list-group-item childlist">{{ __('Overall Settings') }}</a>

                        <a href="{{ route('roles.index') }}"
                            class="list-group-item childlist">{{ __('Role Management') }}</a>
                        <a href="{{ route('integrations.index') }}"
                            class="list-group-item childlist">{{ __('Integrations') }}</a>
                    </div>
                @endif
                <a href="{{ url('/logout') }}" class=" list-group-item impmenu" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-log-out"></i> {{ __('Sign Out') }} </a>

            </div>
        </nav>  --}}

        @include('include.sidebar')
        {{-- <div class="w-100"> --}}


        <!-- Page Content -->
        <div id="page-content-wrapper" style="background-color: #ebeff3; overflow-x: scroll; height: 100vh; weight: 100%">
            <div class="navbar navbar-default navbar-top justify-content-start p-0 bg-white">
                <div class="d-flex justify-content-between w-100">
                    <div class="d-flex">
                        <div class="btn-toggle-sidebar sb-item position-relative" data-parent="#MainMenu">
                            <i class="sb-icon fa fa-angle-double-left fs-2"></i>
                            <span class="sb-text"></span>
                            <i class="sb-icon fa fa-angle-double-right fs-2"></i>
                            {{-- <img src="{{ asset('images/logo.jpg') }}" style="height: 34px; "
                                alt="logo" class="sb-text mt-3"> --}}
                        </div>
                        <div class="d-flex align-items-center"> <b class="fs-2">@yield('page-title')</b></div>
                    </div>
                    <div class="d-flex">
                        <!--NOTIFICATIONS START-->

                        <div class="dropdown mt-0">
                            <a id="dLabel" role="button" data-bs-toggle="dropdown" href="/page.html"
                                aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-2" width="20px"
                                    viewBox="0 0 448 512">
                                    <path
                                        d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 25.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416l400 0c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4l0-25.4c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm0 96c61.9 0 112 50.1 112 112l0 25.4c0 47.9 13.9 94.6 39.7 134.6L72.3 368C98.1 328 112 281.3 112 233.4l0-25.4c0-61.9 50.1-112 112-112zm64 352l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7s18.7-28.3 18.7-45.3z" />
                                </svg><span id="notifycount"></span>
                                {{-- <i class="fa-regular fa-bell"><span id="notifycount"></span></i> --}}
                                {{-- <i class="glyphicon glyphicon-bell"><span id="notifycount"></span></i> --}}
                            </a>
                            <ul class="dropdown-menu notify-drop  notifications" role="menu"
                                aria-labelledby="dLabel">
                                <div class="notification-heading py-3">
                                    <h4 class="menu-title">Notifications</h4>
                                    <h4 class="menu-title pull-right"><a href="{{ url('notifications/markall') }}">Mark
                                            all as
                                            read</a><i class="fa fa-circle-arrow-right ms-2"></i></h4>
                                    {{-- <i class="glyphicon glyphicon-circle-arrow-right ms-2"></i> --}}
                                </div>
                                {{-- <li class="divider"></li> --}}
                                <div class="notifications-wrapper">
                                    <span id="notification-item"></span>
                                    @push('scripts')
                                        <script>
                                            id = {};

                                            function postRead(id) {
                                                $.ajax({
                                                    type: 'post',
                                                    url: '{{ url('/notifications/markread') }}',
                                                    data: {
                                                        id: id,
                                                    },
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                    }
                                                });

                                            }

                                            $(function() {
                                                $.get("{{ url('/notifications/getall') }}", function(notifications) {
                                                    var notifyItem = document.getElementById('notification-item');
                                                    var bell = document.getElementById('notifycount');
                                                    var msg = "";
                                                    var count = 0;
                                                    $.each(notifications, function(index, notification) {
                                                        count++;
                                                        var id = notification['id'];
                                                        var url = notification['data']['url'];

                                                        msg += `<div class="py-3 notification-section">
                                            <a class="content"  id="notify" href="{{ url('notifications') }}/` + id + `">
                                            ` +
                                                            notification['data']['message'] +
                                                            ` </a></div>`;
                                                        notifyItem.innerHTML = msg;

                                                        /**         notifyItem.onclick = (function(id){
                                                                                 return function(){
                                                                                     postRead(id);
                                                                                 }})(id); **/

                                                    });
                                                    bell.innerHTML = count;
                                                })

                                            });
                                        </script>
                                    @endpush
                                </div>

                            </ul>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $.ajax({
                                        url: base_url + "/ajax/India",
                                        type: "GET",
                                        dataType: "json",
                                        success: function(data) {

                                            $('select[name="state"]').empty();

                                            $.each(data, function(key, value) {
                                                $('select[name="state"]').append('<option value="' + key + '">' +
                                                    value + '</option>');
                                            });

                                        }
                                    });
                                    $('select[name="country"]').on('change', function() {
                                        var countryid = $(this).val();
                                        if (countryid) {
                                            $.ajax({
                                                url: base_url + "/ajax/" + countryid,
                                                type: "GET",
                                                dataType: "json",
                                                success: function(data) {

                                                    $('select[name="state"]').empty();

                                                    $.each(data, function(key, value) {
                                                        $('select[name="state"]').append('<option value="' +
                                                            key + '">' + value + '</option>');
                                                    });

                                                }
                                            });
                                        } else {

                                            $('select[name="state"]').empty();
                                        }
                                    });
                                });


                                $(document).ready(function() {
                                    $('select[name="requirement"]').on('change', function() {
                                        var req = $(this).val();
                                        if (req == "other") {


                                            $('#req').html(
                                                ' <div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_requirement']) !!}</div> </div>'
                                            );

                                        } else {

                                            $('#req').empty();


                                        }
                                    });
                                });

                                $(document).ready(function() {
                                    $('select[name="source"]').on('change', function() {
                                        var src = $(this).val();
                                        if (src == "other") {
                                            $('#src').html(
                                                '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_source']) !!}</div> </div>'
                                            );

                                        } else {

                                            $('#src').empty();


                                        }
                                    });
                                });
                                $(document).ready(function() {
                                    $('select[name="project"]').on('change', function() {
                                        var pro = $(this).val();
                                        //              alert("value:" + pro);
                                        if (pro == "other") {

                                            $('#project').html(
                                                '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_project']) !!}</div> </div>'
                                            );

                                        } else {

                                            $('#project').empty();


                                        }
                                    });
                                });
                                $(document).ready(function() {
                                    $('select[name="Budget"]').on('change', function() {
                                        var bgt = $(this).val();
                                        if (bgt == "other") {
                                            $('#budget').html(
                                                '<div class="form-group"><div class="col-lg-12">{!! Form::text('city', $value = null, ['class' => 'form-control', 'id' => 'other_budget']) !!}</div> </div>'
                                            );

                                        } else {

                                            $('#budget').empty();


                                        }
                                    });
                                });

                                $(document).ready(function() {
                                    var rolesvalue = $('#roles').val();
                                    if (rolesvalue == 3) {
                                        $('.teamlead').show();
                                        $('.teamleadedit').show();
                                    } else {
                                        $('.teamlead').hide();
                                        $('.teamleadedit').hide();
                                        // $('#roles').val('');
                                    }
                                    $('#roles').on('change', function() {
                                        var value = $(this).val();
                                        if (value == 3) {
                                            $('.teamlead').show();
                                            $('.teamleadedit').show();
                                        } else {
                                            $('.teamlead').hide();
                                            $('.teamleadedit').hide();
                                            // $('#roles').val('');
                                        }

                                    });
                                });
                            </script>
                        </div>
                        {{-- profile --}}
                        <div class="avatar-menu d-flex align-items-center" id="avatar-menu">
                            <div class="header-avatar" id="header-avatar">
                                @if (auth()->user()->image_path)
                                    <img class="header-avatar" src="../images/media/{{ auth()->user()->image_path }}"
                                        alt="Profile Image" />
                                @else
                                    <div class="initial-avatar">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) .
                                            (strpos(auth()->user()->name, ' ')
                                                ? strtoupper(substr(auth()->user()->name, strpos(auth()->user()->name, ' ') + 1, 1))
                                                : '') }}
                                    </div>
                                @endif
                            </div>
                            {{-- <div class="notification-bubble" id="notification-bubble"><span></span>
                            </div> --}}
                            <ul id="avatar-dropdownmenu" style="display: none;">
                                <li class="sb-item d-flex px-2 align-item-end py-1 bg-light">
                                    <div class="avatar-menu">
                                        <div class="header-avatar">
                                            @if (auth()->user()->image_path)
                                                <img class="header-avatar"
                                                    src="../images/media/{{ auth()->user()->image_path }}"
                                                    alt="Profile Image" />
                                            @else
                                                <div class="initial-avatar">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) .
                                                        (strpos(auth()->user()->name, ' ')
                                                            ? strtoupper(substr(auth()->user()->name, strpos(auth()->user()->name, ' ') + 1, 1))
                                                            : '') }}
                                                </div>
                                            @endif
                                            <span class="online-indicator"></span>
                                            <span class="icon icon-caret-down"></span>
                                        </div>
                                    </div>

                                    <h4 class="d-flex align-items-center m-0">{{ auth()->user()->name }}</h4>
                                </li>
                                <li class="sb-item"><a href="{{ route('users.show', \Auth::id()) }}" class=""
                                        data-parent="#MainMenu"><i class="fa-solid fa-user sideicon ps-0 pe-4"></i>
                                        <span class="sideTxt"> {{ __('Profile') }}
                                        </span></a></li>

                                <li class="sb-item sb-menu"><a href="{{ url('/logout') }}" class="sideicon"
                                        data-parent="#MainMenu"><i class="fa-solid fa-right-from-bracket pe-4"></i>
                                        <span class="sideTxt"> {{ __('Sign Out') }}</span></a></li>
                            </ul>
                        </div>

                    </div>
                </div>

                <!--NOTIFICATIONS END-->
                <button type="button" class="navbar-toggle d-none" data-toggle="offcanvas" data-target="#myNavmenu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>@yield('heading')</h1>
                        @yield('content')
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>

            @endif
            @if (Session::has('flash_message_warning'))
                <message message="{{ Session::get('flash_message_warning') }}" type="warning"></message>
            @endif
            @if (Session::has('flash_message'))
                <message message="{{ Session::get('flash_message') }}" type="success"></message>
            @endif
        </div>
        {{-- </div> --}}
        <!-- /#page-content-wrapper -->
    </div>
    <script>
        $(document).ready(function() {

            var avatar = $("#header-avatar");
            var dropdown = $("#avatar-dropdownmenu");
            var bubble = $("#notification-bubble");

            avatar.click(function(event) {
                event.stopPropagation();
                dropdown.toggle();
            });

            // body click closes menu
            $("body").click(function() {
                dropdown.hide();
            });
        });
    </script>

    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/dropzone.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ URL::asset('js/dataTables.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ URL::asset('js/jquery.dataTables.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('datatable/datatable.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}
    <script src="{{ asset('js/custom.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ URL::asset('js/jasny-bootstrap.min.js') }}"></script> --}}



    <!-- Script JS -->
    <script>
        $(function() {
            // toggle sidebar collapse
            $('.btn-toggle-sidebar').on('click', function() {
                $('.wrapper').toggleClass('sidebar-collapse');
            });
            // mark sidebar item as active when clicked
            $('.sb-item').on('click', function() {
                if ($(this).hasClass('btn-toggle-sidebar')) {
                    return; // already actived
                }
                $(this).siblings().removeClass('active');
                $(this).siblings().find('.sb-item').removeClass('active');
                $(this).addClass('active');
            })
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {

                function adjustLayout() {
                    if (window.innerWidth <= 768) {
                        mainContent.style.marginLeft = '0';
                        sidebar.style.width = '0';
                    } else {
                        mainContent.style.marginLeft = '60px';
                        sidebar.style.width = '60px';
                    }
                }

                window.addEventListener('resize', adjustLayout);
                adjustLayout();

                sidebar.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 768) {
                        mainContent.style.marginLeft = '250px';
                    }
                });

                sidebar.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 768) {
                        mainContent.style.marginLeft = '60px';
                    }
                });
            }
        });
    </script>


    @stack('scripts')
    @yield('js')
</body>

</html>
