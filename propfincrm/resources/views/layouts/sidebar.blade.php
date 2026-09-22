{{-- new-sidebar --}}

<main class="cd__main">
    <div class="wrapper sidebar-collapse">
        <div class="sidebar">
            <div class="sb-item-list">
                <div class=" pt-4"> <a href="#" class="" data-parent="#MainMenu">
                        <img src="{{ asset('images/logo.jpg') }}" style="height: 44px;" alt="dashboard"
                            class="ms-5 mb-3 logofull">
                        <img src="{{ asset('images/logo.jpg') }}" style="height: 34px; " alt="ElyLeads"
                            class="sb-text logosmall mt-2 mb-3 px-3">
                        <span class="sideTxt"></span></a></div>

                <div class="sb-item"> <a href="{{ route('dashboard', \Auth::id()) }}" class=""
                        data-parent="#MainMenu"><img src="{{ asset('images/dashboard.png') }}" style="height: 22px; "
                            alt="dashboard"><span
                            class="sideTxt"style="padding-left: 4px;">{{ __('Dashboard') }}</span></a></div>
                <div class="sb-item sb-menu"> <a href="#user" class="" data-toggle="collapse"
                        data-parent="#MainMenu"><img src="{{ asset('images/team.png') }}" style="height: 22px; "
                            alt="teams"><span class="sideTxt"style="padding-left: 3px;">
                            {{ __('Teams') }}</span> </a>
                    <div class="sb-submenu">
                        <div class="sb-item"><a href="{{ route('users.index') }}"
                                class="">{{ __('Team members') }}</a>
                        </div>
                        <div class="sb-item">
                            @if (Entrust::can('user-create'))
                                <a href="{{ route('users.create') }}">{{ __('New team member') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="sb-item sb-menu"> <a href="#leads" class="" data-toggle="collapse"
                        data-parent="#MainMenu"><img src="{{ asset('images/leads.png') }}" style="height: 22px; "
                            alt="leads"><span class="sideTxt" style="padding-left: 4px;">
                            {{ __('Leads') }}</span></a>
                    <div class="sb-submenu">
                        <div class="sb-item">
                            <a href="{{ url('lead_list') }}">{{ __('All Leads') }}</a>
                        </div>
                        <div class="sb-item">
                            @if (Entrust::can('lead-create'))
                                <a href="{{ route('leads.create') }}">{{ __('New Lead') }}</a>
                            @endif
                        </div>
                        <div class="sb-item"><a href="{{ url('all_sell_list') }}"
                                class="">{{ __('All Sell Lead') }}</a></div>
                        <div class="sb-item"><a href="{{ url('new_sell_page') }}"
                                class="">{{ __('New Sell Lead') }}</a></div>
                        <div class="sb-item"> <a href="{{ url('all_meeting_and_visits') }}"
                                class="">{{ __('Meeting & Visits') }}</a></div>

                        @if (Entrust::hasRole('administrator'))
                            <div class="sb-item"><a href="{{ route('lead_list', 'ReverseLead') }}"
                                    class="">{{ __('Reverse Leads') }}</a></div>
                        @endif

                        <div class="sb-item">
                            @if (Entrust::hasRole('administrator'))
                                <a href="{{ route('leads.bulk.upload.index') }}"
                                    class="">{{ __('Bulk Upload') }}</a>
                        </div>
                        <div class="sb-item"> <a href="{{ route('lead_list', 'DumpLead') }}"
                                class="">{{ __('Dump Leads') }}</a>
                            @endif
                        </div>
                        <div class="sb-item"><a href="{{ route('scheduler.index') }}"
                                    class="">{{ __('All Scheduler') }}</a></div>
                    </div>
                </div>
                @if (Entrust::hasRole('administrator'))
                    <div class="sb-item">
                        <a href="{{ url('call_logs') }}" class="" data-parent="#MainMenu">
                            <img src="{{ asset('images/call-logs.png') }}" style="height: 22px; " alt="call-logs">
                            <span class="sideTxt" style="padding-left: 5px;">{{ __('Call Logs') }}</span>
                        </a>
                    </div>
                @endif
                @if (Entrust::hasRole('administrator'))
                    <div class="sb-item sb-menu">
                        <a href="#attendance" class="" data-toggle="collapse" data-parent="#MainMenu">
                            <img src="{{ asset('images/calendar.png') }}" style="height: 22px; " alt="">
                            <span class="sideTxt"style="padding-left: 5px;">{{ __('HR') }}</span>
                        </a>
                        <div class="sb-submenu">
                            <div class="sb-item">
                                <a href="{{ url('attendance') }}" class="">{{ __('Attendance') }}</a>
                            </div>
                            <div class="sb-item"><a href="{{ url('leaves') }}" class="">{{ __('Leaves') }}</a>
                            </div>
                            <div class="sb-item">
                                <a href="{{ url('ta-da') }}">{{ __('TA/DA') }}</a>
                            </div>
                        </div>
                    </div>
                @endif


                @if (Entrust::hasRole('administrator'))
                    <div class="sb-item sb-menu">

                        <a href="#master" class="" data-toggle="collapse" data-parent="#MainMenu">
                            <img class="" src="{{ asset('images/master.png') }}" style="height: 22px; "
                                alt="master">
                            <span class="sideTxt"style="padding-left: 4px;">
                                {{ __('Master') }}</span></a>
                        <div class="sb-submenu">
                            <div class="sb-item">
                                <a href="{{ url('project_list') }}" class="">{{ __('Project') }}</a>
                            </div>

                            <div class="sb-item"> <a href="{{ url('requirement_list') }}"
                                    class="">{{ __('Requirement') }}</a></div>
                            <div class="sb-item"><a href="{{ url('budget_list') }}"
                                    class="">{{ __('Budget') }}</a></div>
                            <div class="sb-item"><a href="{{ url('source_list') }}"
                                    class="">{{ __('Source') }}</a></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
