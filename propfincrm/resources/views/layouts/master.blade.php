<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ElyLeads</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind is scoped to utilities only: it does not reset legacy form or plugin styles. --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: { brand: { 50: '#fff7ed', 500: '#f97316', 600: '#ea580c' } },
                    fontFamily: { sans: ['Outfit', 'sans-serif'] }
                }
            }
        };
    </script>

    <!-- Core Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="{{ asset('datatable/datatable.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.tailwindcss.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Legacy styles remain available for unconverted pages only. -->
    @unless (request()->is('dashboard') || request()->is('lead*') || request()->is('all_sell*') || request()->is('new_sell*') || request()->is('all_meeting*') || request()->is('scheduler*') || request()->is('call_logs*') || request()->is('users*') || request()->is('project_list') || request()->is('source_list') || request()->is('requirement_list') || request()->is('budget_list') || request()->is('settings/company*') || request()->is('platform/*') || request()->is('master/roles-permissions') || request()->is('roles*') || request()->is('attendance*') || request()->is('leaves*') || request()->is('ta-da*') || request()->is('departments*') || request()->is('clients*') || request()->is('tasks*') || request()->is('invoices*') || request()->is('integrations*') || request()->is('settings*'))
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endunless
    <link href="{{ asset('css/shell.css') }}?v={{ filemtime(public_path('css/shell.css')) }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/crm-common.css') }}?v={{ filemtime(public_path('css/crm-common.css')) }}" rel="stylesheet" type="text/css">

    <!-- Global Base URL -->
    <script type="text/javascript">
        window.base_url = "{{ url('/') }}";
        var base_url = window.base_url;
    </script>

    <!-- jQuery 3.6 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Pre-apply sidebar mini state to prevent layout shift
        (function() {
            try {
                if (localStorage.getItem('crm_sidebar_mini') === 'true' && window.innerWidth >= 992) {
                    document.documentElement.classList.add('sidebar-mini-preload');
                    document.addEventListener('DOMContentLoaded', function() {
                        document.body.classList.add('sidebar-mini');
                        document.documentElement.classList.remove('sidebar-mini-preload');
                    });
                }
            } catch(e) {}
        })();
    </script>

    @yield('styles')
</head>

<body class="bg-white font-sans">
    <div class="min-h-screen bg-white lg:flex">
        {{-- Modern Sidebar Component --}}
        @include('include.sidebar')

        {{-- Main Page Content Wrapper --}}
        <div id="page-content-wrapper" class="min-w-0 flex-1 bg-white lg:ml-[220px]">
            @if (session('impersonating_company_id'))
                <div class="bg-amber-500 text-white px-4 py-2 text-xs font-medium flex items-center justify-between shadow-sm sticky top-0 z-50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-alert" class="h-4 w-4"></i>
                        <span><strong>Platform Superadmin Impersonation:</strong> You are managing <strong>{{ optional(auth()->user()->activeCompany())->name }}</strong> workspace.</span>
                    </div>
                    <form method="POST" action="{{ route('platform.impersonate.leave') }}" class="m-0">
                        @csrf
                        <button type="submit" class="bg-white text-amber-800 hover:bg-amber-100 font-semibold px-2.5 py-1 rounded text-[11px] shadow-sm transition border-0 cursor-pointer">
                            Exit to Platform Superadmin &rarr;
                        </button>
                    </form>
                </div>
            @endif
            {{-- Modern SaaS Topbar --}}
            <header class="sticky top-0 z-30 flex h-11 items-center justify-between border-b border-slate-200 bg-white px-4 font-sans">
                <div class="flex items-center gap-2.5">
                    <button type="button" class="btn-toggle-sidebar inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition border-0 bg-transparent cursor-pointer" id="sidebarToggle" title="Toggle Sidebar"><i data-lucide="panel-left" class="h-4 w-4"></i></button>
                    <div class="flex items-center gap-1.5">
                        <h4 class="m-0 text-[17px] font-semibold leading-none text-slate-900">@yield('page-title', 'ElyLeads')</h4>
                        @hasSection('page-count')
                            <span class="text-[12.5px] text-slate-400 font-normal">@yield('page-count')</span>
                        @endif
                    </div>
                    @if (auth()->check() && auth()->user()->activeCompany())
                        @php
                            $currentActiveCompany = auth()->user()->activeCompany();
                            $daysLeft = $currentActiveCompany->daysRemaining();
                            $isExp = $currentActiveCompany->isExpired();
                        @endphp
                        <div class="hidden sm:flex items-center gap-1.5">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600">
                                <i data-lucide="building-2" class="mr-1.5 h-3.5 w-3.5"></i>{{ $currentActiveCompany->name }}
                            </span>
                            @if ($isExp)
                                <a href="{{ route('tenant.subscription') }}" class="inline-flex items-center gap-1 rounded-md bg-rose-50 border border-rose-200 px-2 py-0.5 text-[11px] font-semibold text-rose-700 no-underline hover:bg-rose-100 transition">
                                    <i data-lucide="alert-triangle" class="h-3 w-3"></i> Plan Expired · Renew
                                </a>
                            @elseif ($daysLeft !== null && $daysLeft <= 7)
                                <a href="{{ route('tenant.subscription') }}" class="inline-flex items-center gap-1 rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 text-[11px] font-semibold text-amber-700 no-underline hover:bg-amber-100 transition">
                                    <i data-lucide="clock" class="h-3 w-3"></i> {{ $daysLeft }}d left · {{ $currentActiveCompany->planName() }}
                                </a>
                            @else
                                <a href="{{ route('tenant.subscription') }}" class="inline-flex items-center gap-1 rounded-md bg-indigo-50 border border-indigo-100 px-2 py-0.5 text-[11px] font-medium text-indigo-700 no-underline hover:bg-indigo-100 transition" title="Manage Subscription">
                                    <i data-lucide="sparkles" class="h-3 w-3"></i> {{ $currentActiveCompany->planName() }}
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    {{-- Info Icon --}}
                    <button type="button" class="topbar-action-icon" title="Information">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </button>

                    {{-- Company admins can open their company-specific settings here. --}}
                    @if (auth()->check() && auth()->user()->isCompanyAdmin())
                    <a href="{{ route('settings.company') }}" class="topbar-action-icon" title="Company Settings" aria-label="Company Settings">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </a>
                    @endif

                    {{-- Notifications Dropdown --}}
                    <div class="dropdown">
                        <button class="topbar-action-icon relative"
                                type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                aria-label="Notifications" title="Notifications">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                            </svg>
                            <span id="notifycount" class="absolute -right-1 -top-1 hidden min-w-4 rounded-full bg-red-500 px-1 py-px text-[9px] font-semibold text-white leading-none">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" aria-labelledby="notifDropdown" style="width: 320px; border-radius: 12px; overflow: hidden;">
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light border-bottom">
                                <span class="fw-semibold text-dark">{{ __('Notifications') }}</span>
                                <a href="{{ url('notifications/markall') }}" class="small text-primary text-decoration-none fw-medium">
                                    {{ __('Mark all read') }} <i data-lucide="check-check" class="ms-1 h-3.5 w-3.5"></i>
                                </a>
                            </div>
                            <div class="notifications-wrapper" style="max-height: 320px; overflow-y: auto;">
                                <div id="notification-item" class="p-2">
                                    <div class="text-center text-muted py-3 small">{{ __('No new notifications') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            {{-- Main Content Body --}}
            <main class="app-page-body @yield('main-class', 'p-4') {{ request()->is('project_list') || request()->is('source_list') || request()->is('requirement_list') || request()->is('budget_list') ? 'master-data-page' : '' }}">
                @if (session()->has('flash_message'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                        <i data-lucide="circle-check" class="me-2 inline-block h-4 w-4"></i>{{ session('flash_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                        <i data-lucide="triangle-alert" class="me-2 inline-block h-4 w-4"></i>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/dropzone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('datatable/datatable.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>window.lucide && window.lucide.createIcons();</script>
    <script>
        // Shared date picker for every native date field. The submitted value remains YYYY-MM-DD.
        window.initAppDatePickers = function(scope) {
            if (!window.flatpickr) return;
            $(scope || document).find('input[type="date"]:not([data-disable-global-datepicker])').each(function() {
                if (this._flatpickr) return;
                var datePickerOptions = {
                    altInput: true,
                    altFormat: 'd-m-Y',
                    dateFormat: 'Y-m-d',
                    allowInput: true
                };
                var datePickerOffcanvas = $(this).closest('.offcanvas');
                if (datePickerOffcanvas.length) datePickerOptions.appendTo = datePickerOffcanvas[0];
                flatpickr(this, datePickerOptions);
            });
        };
        $(function() { window.initAppDatePickers(document); });
    </script>
    <script>
        // Shared Select2 bootstrap for standard and multi-select dropdowns across the app.
        window.initAppSelects = function(scope) {
            if (typeof $.fn.select2 !== 'function') {
                if (!window.__select2FallbackLoading) {
                    window.__select2FallbackLoading = true;
                    var fallback = document.createElement('script');
                    fallback.src = 'https://unpkg.com/select2@4.0.13/dist/js/select2.full.min.js';
                    fallback.onload = function() { window.initAppSelects(document); };
                    fallback.onerror = function() {
                        var secondary = document.createElement('script');
                        secondary.src = 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js';
                        secondary.onload = function() { window.initAppSelects(document); };
                        document.head.appendChild(secondary);
                    };
                    document.head.appendChild(fallback);
                }
                return false;
            }

            var $scope = $(scope || document);
            var selector = 'select:not([data-disable-select2]):not(.dataTables_length select):not([name$="_length"]):not(.dt-input)';
            var $targets = $scope.is(selector) ? $scope : $scope.find(selector);

            $targets.each(function() {
                var $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) return;

                var selectOptions = {
                    theme: 'bootstrap-5',
                    width: '100%',
                    minimumResultsForSearch: 0,
                    placeholder: $select.attr('placeholder') || ($select.prop('multiple') ? 'Search and select' : 'Search and select')
                };

                var parentModal = $select.closest('.modal');
                var parentOffcanvas = $select.closest('.offcanvas');
                if (parentModal.length) {
                    selectOptions.dropdownParent = parentModal;
                } else if (parentOffcanvas.length) {
                    selectOptions.dropdownParent = parentOffcanvas;
                }

                $select.select2(selectOptions);
            });
            return true;
        };

        $(function() {
            window.initAppSelects(document);

            // Re-initialize for dynamically opened modals & offcanvases
            $(document).on('shown.bs.modal', function(e) {
                window.initAppSelects(e.target);
            });
            $(document).on('shown.bs.offcanvas', function(e) {
                window.initAppSelects(e.target);
            });
            $(document).ajaxComplete(function() {
                window.initAppSelects(document);
            });
        });
    </script>

    <!-- Global Layout & Sidebar Toggle Scripts -->
    <script>
        $(function() {
            // Restore sidebar mini state on load
            if (localStorage.getItem('crm_sidebar_mini') === 'true' && window.innerWidth >= 992) {
                $('body').addClass('sidebar-mini');
            }

            // Toggle sidebar click handler (icon-only mini mode on desktop, off-canvas on mobile)
            $(document).on('click', '.btn-toggle-sidebar, #sidebarToggle', function(e) {
                e.preventDefault();
                if (window.innerWidth < 992) {
                    $('#sidebar-wrapper').toggleClass('-translate-x-full');
                    return;
                }
                $('body').toggleClass('sidebar-mini');
                var isMini = $('body').hasClass('sidebar-mini');
                localStorage.setItem('crm_sidebar_mini', isMini ? 'true' : 'false');

                // Adjust any active DataTables columns smoothly
                setTimeout(function() {
                    if ($.fn.DataTable) {
                        $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
                    }
                    $(window).trigger('resize');
                }, 220);
            });

            // When in sidebar-mini mode, clicking an expandable submenu group auto-expands the sidebar
            $(document).on('click', 'body.sidebar-mini #sidebar-wrapper button[data-bs-toggle="collapse"]', function() {
                $('body').removeClass('sidebar-mini');
                localStorage.setItem('crm_sidebar_mini', 'false');
                setTimeout(function() {
                    if ($.fn.DataTable) {
                        $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
                    }
                    $(window).trigger('resize');
                }, 220);
            });
        });

        // Notifications Loader
        function postRead(id) {
            $.ajax({
                type: 'post',
                url: '{{ url("/notifications/markread") }}',
                data: { id: id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        $(function() {
            $.get("{{ url('/notifications/getall') }}", function(notifications) {
                var notifyItem = $('#notification-item');
                var bell = $('#notifycount');
                var msg = "";
                var count = 0;
                if (notifications && notifications.length > 0) {
                    $.each(notifications, function(index, notification) {
                        count++;
                        var id = notification['id'];
                        var noteData = notification['data'] || {};
                        var message = noteData['message'] || 'New notification';
                        msg += '<div class="p-2 border-bottom">' +
                               '<a class="text-decoration-none text-dark d-block small" href="{{ url("notifications") }}/' + id + '">' +
                               '<i class="fa-regular fa-bell text-primary me-2"></i>' + message +
                               '</a></div>';
                    });
                    notifyItem.html(msg);
                    bell.text(count).show();
                } else {
                    notifyItem.html('<div class="text-center text-muted py-3 small">{{ __("No new notifications") }}</div>');
                    bell.hide();
                }
            }).fail(function() {
                $('#notifycount').hide();
            });
        });

        // Form Helpers (Dynamic dropdowns for Lead creation/editing)
        $(document).ready(function() {
            if ($('select[name="country"]').length) {
                $.ajax({
                    url: base_url + "/ajax/India",
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="state"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="state"]').append('<option value="' + key + '">' + value + '</option>');
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
                                    $('select[name="state"]').append('<option value="' + key + '">' + value + '</option>');
                                });
                            }
                        });
                    } else {
                        $('select[name="state"]').empty();
                    }
                });
            }

            $('select[name="requirement"]').on('change', function() {
                if ($(this).val() == "other") {
                    $('#req').html('<div class="form-group"><div class="col-lg-12"><input type="text" name="city" class="form-control" id="other_requirement"></div></div>');
                } else {
                    $('#req').empty();
                }
            });

            $('select[name="source"]').on('change', function() {
                if ($(this).val() == "other") {
                    $('#src').html('<div class="form-group"><div class="col-lg-12"><input type="text" name="city" class="form-control" id="other_source"></div></div>');
                } else {
                    $('#src').empty();
                }
            });

            $('select[name="project"]').on('change', function() {
                if ($(this).val() == "other") {
                    $('#project').html('<div class="form-group"><div class="col-lg-12"><input type="text" name="city" class="form-control" id="other_project"></div></div>');
                } else {
                    $('#project').empty();
                }
            });

            $('select[name="Budget"]').on('change', function() {
                if ($(this).val() == "other") {
                    $('#budget').html('<div class="form-group"><div class="col-lg-12"><input type="text" name="city" class="form-control" id="other_budget"></div></div>');
                } else {
                    $('#budget').empty();
                }
            });

            if ($('#roles').length) {
                var checkRoles = function(val) {
                    if (val == 3) {
                        $('.teamlead, .teamleadedit').show();
                    } else {
                        $('.teamlead, .teamleadedit').hide();
                    }
                };
                checkRoles($('#roles').val());
                $('#roles').on('change', function() {
                    checkRoles($(this).val());
                });
            }
        });
    </script>

    @stack('scripts')
    @yield('js')
</body>

</html>
