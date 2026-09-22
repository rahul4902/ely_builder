<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ElyLeads</title>
    <link href="{{ URL::asset('css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('css/dropzone.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <script type="text/javascript">
        var base_url = {!! json_encode(url('/')) !!}
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>


    <div id="wrapper">
        <div class="navbar navbar-default navbar-top">
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
        </div>


        <!-- /#sidebar-wrapper -->
        <!-- Sidebar menu -->

        <nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-left offcanvas-sm" role="navigation">
            <div class="list-group panel">
                <p class=" list-group-item" title=""><img src="{{ url('images/flarepoint_logo.png') }}"
                        alt=""></p>
                <a href="{{ route('dashboard', \Auth::id()) }}" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-dashboard"></i> {{ __('Dashboard') }} </a>
                <a href="{{ route('users.show', \Auth::id()) }}" class=" list-group-item" data-parent="#MainMenu"><i
                        class="glyphicon glyphicon-user"></i> {{ __('Profile') }} </a>


                {{-- <a href="#clients" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i --}}
                {{-- class="glyphicon glyphicon-tag"></i> {{ __('Clients') }}</a> --}}
                {{-- <div class="collapse" id="clients"> --}}

                {{-- <a href="{{ route('clients.index')}}" class="list-group-item childlist">{{ __('All Clients') }}</a> --}}
                {{-- @if (Entrust::can('client-create')) --}}
                {{-- <a href="{{ route('clients.create')}}" --}}
                {{-- class="list-group-item childlist">{{ __('New Client') }}</a> --}}
                {{-- @endif --}}
                {{-- </div> --}}

                {{-- <a href="#tasks" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i
                                                class="glyphicon glyphicon-tasks"></i> {{ __('Tasks') }} </a>
                                    <div class="collapse" id="tasks">
                                        <a href="{{ route('tasks.index')}}" class="list-group-item childlist">{{ __('All Tasks') }}</a>
                                        @if (Entrust::can('task-create'))
                                            <a href="{{ route('tasks.create')}}" class="list-group-item childlist">{{ __('New Task') }}</a>
                                        @endif
                                    </div> --}}

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

                {{-- <a href="#departments" class=" list-group-item" data-toggle="collapse" data-parent="#MainMenu"><i --}}
                {{-- class="fa fa-object-group"></i> {{ __('Departments') }}</a> --}}
                {{-- <div class="collapse" id="departments"> --}}
                {{-- <a href="{{ route('departments.index')}}" --}}
                {{-- class="list-group-item childlist">{{ __('All Departments') }}</a> --}}
                {{-- @if (Entrust::hasRole('administrator')) --}}
                {{-- <a href="{{ route('departments.create')}}" --}}
                {{-- class="list-group-item childlist">{{ __('New Department') }}</a> --}}
                {{-- @endif --}}
                {{-- </div> --}}

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
        </nav>


        <!-- Page Content -->
        <div id="page-content-wrapper">
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
        <!-- /#page-content-wrapper -->
    </div>
    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jasny-bootstrap.min.js') }}"></script>

    @stack('scripts')
</body>

</html>
