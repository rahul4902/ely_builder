@php
    $sidebarUser = auth()->user();
    $activeCompany = $sidebarUser?->activeCompany();
    $activeCompanyRole = $sidebarUser?->companyRole();
    $availableCompanies = $sidebarUser?->companies()->wherePivot('is_active', true)->orderBy('companies.name')->get() ?? collect();
@endphp
<aside id="sidebar-wrapper" class="app-sidebar fixed inset-y-0 left-0 z-40 flex h-screen w-[220px] -translate-x-full flex-col overflow-hidden border-r border-slate-200 bg-slate-50/70 font-sans transition-transform duration-200 lg:translate-x-0">
    {{-- Brand Header --}}
    <a href="{{ $activeCompany ? route('dashboard', \Auth::id()) : route('platform.companies.index') }}" title="{{ $activeCompany ? __('Dashboard') : __('Platform Management') }}" class="flex h-11 items-center border-b border-slate-200 px-4 no-underline bg-white">
        <div class="sidebar-brand-full flex items-center">
            <span class="text-[17px] font-bold tracking-tight text-slate-900">ElyLeads</span>
            @if ($sidebarUser?->isPlatformSuperAdmin() && !$activeCompany)
                <span class="ms-2 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-orange-100 text-orange-700 uppercase tracking-wide">Platform</span>
            @endif
        </div>
        <div class="sidebar-brand-mini hidden items-center justify-center w-full">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-800 font-bold text-white text-xs">E</span>
        </div>
    </a>

    {{-- Nav Links --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1">
        @if ($sidebarUser?->isPlatformSuperAdmin() && !$activeCompany)
            {{-- PLATFORM MANAGEMENT (STANDALONE SUPERADMIN) --}}
            <p class="sidebar-section-title mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-orange-600 font-bold">Platform Management</p>

            <a href="{{ route('platform.companies.index') }}" title="{{ __('Tenants') }}"
               class="{{ request()->routeIs('platform.companies.*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="building-2" class="h-[18px] w-[18px] shrink-0 text-orange-500"></i>
                <span class="sidebar-text font-medium text-slate-800">{{ __('Tenants & Companies') }}</span>
            </a>

            <a href="{{ route('platform.subscriptions.index') }}" title="{{ __('Subscriptions') }}"
               class="{{ request()->routeIs('platform.subscriptions.*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="credit-card" class="h-[18px] w-[18px] shrink-0 text-orange-500"></i>
                <span class="sidebar-text font-medium text-slate-800">{{ __('Subscriptions & Plans') }}</span>
            </a>

            <a href="{{ route('platform.payments.index') }}" title="{{ __('Payments') }}"
               class="{{ request()->routeIs('platform.payments.*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="receipt" class="h-[18px] w-[18px] shrink-0 text-orange-500"></i>
                <span class="sidebar-text font-medium text-slate-800">{{ __('Payments Collection') }}</span>
            </a>

            <a href="{{ route('platform.tickets.index') }}" title="{{ __('Support Desk') }}"
               class="{{ request()->routeIs('platform.tickets.*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="life-buoy" class="h-[18px] w-[18px] shrink-0 text-orange-500"></i>
                <span class="sidebar-text font-medium text-slate-800">{{ __('Support Desk') }}</span>
            </a>
        @else
            {{-- MAIN SECTION --}}
            <p class="sidebar-section-title mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Main</p>
            <div class="sidebar-section-divider hidden border-t border-slate-200 my-2 mx-1"></div>

            {{-- Dashboard --}}
            <a href="{{ route('dashboard', \Auth::id()) }}" title="{{ __('Dashboard') }}"
               class="{{ request()->is('dashboard') || request()->is('/') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="house" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                <span class="sidebar-text">{{ __('Dashboard') }}</span>
            </a>

        {{-- Teams Dropdown --}}
        <div>
            <button type="button"
                    class="w-full flex h-9 items-center justify-between rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition cursor-pointer border-0 {{ request()->is('users*') ? 'crm-menu-active' : 'crm-menu-item' }}"
                    data-bs-toggle="collapse" title="{{ __('Teams') }}" data-bs-target="#teamsSubmenu" aria-expanded="{{ request()->is('users*') ? 'true' : 'false' }}">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="users" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                    <span class="sidebar-text">{{ __('Teams') }}</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
            </button>
            <div class="collapse {{ request()->is('users*') ? 'show' : '' }} ml-4 pl-3 my-1 space-y-0.5 border-l border-slate-200" id="teamsSubmenu">
                <a href="{{ route('users.index') }}"
                   class="{{ request()->routeIs('users.index') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('Team members') }}
                </a>
                @if (Entrust::can('user-create'))
                    <a href="{{ route('users.create') }}"
                       class="{{ request()->routeIs('users.create') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('New team member') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- LEADS SECTION --}}
        <p class="sidebar-section-title mb-1.5 mt-3 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">CRM &amp; Sales</p>
        <div class="sidebar-section-divider hidden border-t border-slate-200 my-2 mx-1"></div>

        {{-- Leads Dropdown --}}
        <div>
            <button type="button"
                    class="w-full flex h-9 items-center justify-between rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition cursor-pointer border-0 {{ request()->is('lead*') || request()->is('all_sell*') || request()->is('new_sell*') || request()->is('all_meeting*') || request()->is('scheduler*') ? 'crm-menu-active' : 'crm-menu-item' }}"
                    data-bs-toggle="collapse" title="{{ __('Leads') }}" data-bs-target="#leadsSubmenu" aria-expanded="{{ request()->is('lead*') || request()->is('all_sell*') || request()->is('new_sell*') || request()->is('all_meeting*') || request()->is('scheduler*') ? 'true' : 'false' }}">
                <span class="flex items-center gap-2.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-[18px] w-[18px] shrink-0 text-slate-500" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2"></rect><circle cx="12" cy="10" r="2.25"></circle><path d="M8.5 17c.8-1.75 2-2.6 3.5-2.6s2.7.85 3.5 2.6"></path><path d="M7 7h.01M7 11h.01M7 15h.01"></path></svg>
                    <span class="sidebar-text">{{ __('Leads') }}</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
            </button>
            <div class="collapse {{ request()->is('lead*') || request()->is('all_sell*') || request()->is('new_sell*') || request()->is('all_meeting*') || request()->is('scheduler*') ? 'show' : '' }} ml-4 pl-3 my-1 space-y-0.5 border-l border-slate-200" id="leadsSubmenu">
                <a href="{{ url('lead_list') }}"
                   class="{{ request()->is('lead_list') && !request()->is('lead_list/*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('All Leads') }}
                </a>
                @if (Entrust::can('lead-create'))
                    <a href="{{ route('leads.create') }}"
                       class="{{ request()->routeIs('leads.create') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('New Lead') }}
                    </a>
                @endif
                <a href="{{ url('all_sell_list') }}"
                   class="{{ request()->is('all_sell_list') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('All Sell Lead') }}
                </a>
                <a href="{{ url('new_sell_page') }}"
                   class="{{ request()->is('new_sell_page') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('New Sell Lead') }}
                </a>
                <a href="{{ url('all_meeting_and_visits') }}"
                   class="{{ request()->is('all_meeting_and_visits') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('Meeting & Visits') }}
                </a>
                @if (Entrust::hasRole('administrator'))
                    <a href="{{ route('lead_list', 'ReverseLead') }}"
                       class="{{ request()->is('lead_list/ReverseLead') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Reverse Leads') }}
                    </a>
                    <a href="{{ route('leads.bulk.upload.index') }}"
                       class="{{ request()->is('leads/bulkUpload*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Bulk Upload') }}
                    </a>
                    <a href="{{ route('lead_list', 'DumpLead') }}"
                       class="{{ request()->is('lead_list/DumpLead') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Dump Leads') }}
                    </a>
                @endif
                <a href="{{ route('scheduler.index') }}"
                   class="{{ request()->routeIs('scheduler.index') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                    {{ __('All Scheduler') }}
                </a>
            </div>
        </div>

        {{-- Call Logs (Admin) --}}
        @if ($sidebarUser?->isCompanyAdmin())
            <a href="{{ url('call_logs') }}" title="{{ __('Call Logs') }}"
               class="{{ request()->is('call_logs*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="phone" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                <span class="sidebar-text">{{ __('Call Logs') }}</span>
            </a>
        @endif

        {{-- MANAGEMENT SECTION --}}
        @if ($sidebarUser?->isCompanyAdmin())
            <p class="sidebar-section-title mb-1.5 mt-3 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Operations &amp; HR</p>
            <div class="sidebar-section-divider hidden border-t border-slate-200 my-2 mx-1"></div>

            {{-- HR Dropdown --}}
            <div>
                <button type="button"
                        class="w-full flex h-9 items-center justify-between rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition cursor-pointer border-0 {{ request()->is('attendance*') || request()->is('leaves*') || request()->is('ta-da*') ? 'crm-menu-active' : 'crm-menu-item' }}"
                        data-bs-toggle="collapse" title="{{ __('HR') }}" data-bs-target="#hrSubmenu" aria-expanded="{{ request()->is('attendance*') || request()->is('leaves*') || request()->is('ta-da*') ? 'true' : 'false' }}">
                    <span class="flex items-center gap-2.5">
                        <i data-lucide="calendar-days" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                        <span class="sidebar-text">{{ __('HR') }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
                </button>
                <div class="collapse {{ request()->is('attendance*') || request()->is('leaves*') || request()->is('ta-da*') ? 'show' : '' }} ml-4 pl-3 my-1 space-y-0.5 border-l border-slate-200" id="hrSubmenu">
                    <a href="{{ url('attendance') }}"
                       class="{{ request()->is('attendance*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Attendance') }}
                    </a>
                    <a href="{{ url('leaves') }}"
                       class="{{ request()->is('leaves*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Leaves') }}
                    </a>
                    <a href="{{ url('ta-da') }}"
                       class="{{ request()->is('ta-da*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('TA/DA') }}
                    </a>
                </div>
            </div>

            {{-- Master Dropdown --}}
            <div>
                <button type="button"
                        class="w-full flex h-9 items-center justify-between rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition cursor-pointer border-0 {{ request()->is('project_list*') || request()->is('requirement_list*') || request()->is('budget_list*') || request()->is('source_list*') || request()->routeIs('roles.*') ? 'crm-menu-active' : 'crm-menu-item' }}"
                        data-bs-toggle="collapse" title="{{ __('Master') }}" data-bs-target="#masterSubmenu" aria-expanded="{{ request()->is('project_list*') || request()->is('requirement_list*') || request()->is('budget_list*') || request()->is('source_list*') || request()->routeIs('roles.*') ? 'true' : 'false' }}">
                    <span class="flex items-center gap-2.5">
                        <i data-lucide="sliders-horizontal" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                        <span class="sidebar-text">{{ __('Master') }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
                </button>
                <div class="collapse {{ request()->is('project_list*') || request()->is('requirement_list*') || request()->is('budget_list*') || request()->is('source_list*') || request()->routeIs('roles.*') || request()->routeIs('departments.*') ? 'show' : '' }} ml-4 pl-3 my-1 space-y-0.5 border-l border-slate-200" id="masterSubmenu">
                    @if (Entrust::hasRole('administrator'))
                    <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">{{ __('Roles') }}</a>
                    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">{{ __('Departments') }}</a>
                    @endif
                    <a href="{{ url('project_list') }}"
                       class="{{ request()->is('project_list*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Project') }}
                    </a>
                    <a href="{{ url('requirement_list') }}"
                       class="{{ request()->is('requirement_list*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Requirement') }}
                    </a>
                    <a href="{{ url('budget_list') }}"
                       class="{{ request()->is('budget_list*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Budget') }}
                    </a>
                    <a href="{{ url('source_list') }}"
                       class="{{ request()->is('source_list*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Source') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- TENANT SUPPORT & BILLING --}}
        @if ($activeCompany)
            <p class="sidebar-section-title mb-1.5 mt-3 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Account &amp; Support</p>
            <a href="{{ route('tenant.subscription') }}" title="{{ __('Billing & Plan') }}"
               class="{{ request()->is('subscription*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="credit-card" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                <span class="sidebar-text">{{ __('Billing & Plan') }}</span>
            </a>
            <a href="{{ route('tickets.index') }}" title="{{ __('Support Tickets') }}"
               class="{{ request()->is('tickets*') ? 'crm-menu-active' : 'crm-menu-item' }} flex h-9 items-center gap-2.5 rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition">
                <i data-lucide="life-buoy" class="h-[18px] w-[18px] shrink-0 text-slate-500"></i>
                <span class="sidebar-text">{{ __('Support Tickets') }}</span>
            </a>
        @endif

        {{-- PLATFORM SUPERADMIN SECTION --}}
        @if ($sidebarUser?->isPlatformSuperAdmin())
            <p class="sidebar-section-title mb-1.5 mt-3 px-2.5 text-[11px] font-semibold uppercase tracking-wider text-orange-600 font-bold">Platform Admin</p>
            <div>
                <button type="button"
                        class="w-full flex h-9 items-center justify-between rounded-lg px-2.5 text-[13.5px] font-medium no-underline transition cursor-pointer border-0 {{ request()->is('platform*') ? 'crm-menu-active' : 'crm-menu-item' }}"
                        data-bs-toggle="collapse" title="{{ __('Superadmin') }}" data-bs-target="#platformSubmenu" aria-expanded="{{ request()->is('platform*') ? 'true' : 'false' }}">
                    <span class="flex items-center gap-2.5">
                        <i data-lucide="shield" class="h-[18px] w-[18px] shrink-0 text-orange-500"></i>
                        <span class="sidebar-text font-semibold text-slate-800">{{ __('Superadmin') }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200"></i>
                </button>
                <div class="collapse {{ request()->is('platform*') ? 'show' : '' }} ml-4 pl-3 my-1 space-y-0.5 border-l border-orange-200" id="platformSubmenu">
                    <a href="{{ route('platform.companies.index') }}"
                       class="{{ request()->routeIs('platform.companies.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Tenants') }}
                    </a>
                    <a href="{{ route('platform.subscriptions.index') }}"
                       class="{{ request()->routeIs('platform.subscriptions.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Subscriptions') }}
                    </a>
                    <a href="{{ route('platform.payments.index') }}"
                       class="{{ request()->routeIs('platform.payments.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Payments') }}
                    </a>
                    <a href="{{ route('platform.tickets.index') }}"
                       class="{{ request()->routeIs('platform.tickets.*') ? 'crm-submenu-active' : 'crm-submenu-item' }} flex h-8 items-center rounded-md px-2.5 text-[13px] no-underline transition">
                        {{ __('Support Desk') }}
                    </a>
                </div>
            </div>
        @endif
        @endif
    </nav>

    {{-- User Profile Card in Footer with Dropup Menu --}}
    <div class="border-t border-slate-200 p-2.5 dropup position-relative bg-white">
        <button type="button"
                id="userProfileDropdown"
                data-bs-toggle="dropdown"
                data-bs-auto-close="true"
                aria-expanded="false"
                title="{{ auth()->user()->name ?? 'User' }}"
                class="crm-user-card w-full flex items-center gap-2.5 p-2 text-left cursor-pointer border-0 bg-white">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-800 text-xs font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </span>
            <span class="sidebar-user-details min-w-0 flex-1">
                <span class="block truncate text-[13px] font-semibold text-slate-800 leading-tight">{{ auth()->user()->name ?? 'User' }}</span>
                <span class="inline-block truncate text-[11px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded leading-none mt-1">{{ $activeCompany ? $activeCompany->name . ' · ' . ucwords(str_replace('_', ' ', $activeCompanyRole ?? 'member')) : ($sidebarUser?->isPlatformSuperAdmin() ? 'Platform Super Admin' : 'No active company') }}</span>
            </span>
            <i data-lucide="chevron-up" class="h-4 w-4 text-slate-400 ms-1"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-200 p-1 mb-2 bg-white" aria-labelledby="userProfileDropdown" style="min-width: 210px; border-radius: 10px; font-size: 13px; z-index: 1050;">
            <li class="px-3 py-2 border-b border-slate-100">
                <div class="font-semibold text-slate-800 text-[13px] truncate">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
            </li>
            <li>
                <a class="dropdown-item flex items-center gap-2 py-2 px-3 rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition text-[12.5px] font-medium no-underline" href="{{ route('users.show', \Auth::id()) }}">
                    <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                    <span>{{ __('My Profile') }}</span>
                </a>
            </li>
            @if ($activeCompany)
                <li>
                    <a class="dropdown-item flex items-center gap-2 py-2 px-3 rounded-md text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition text-[12.5px] font-medium no-underline" href="{{ route('tenant.subscription') }}">
                        <i data-lucide="credit-card" class="h-4 w-4 text-slate-400"></i>
                        <span>{{ __('Billing & Subscription') }}</span>
                    </a>
                </li>
            @endif
            @if ($sidebarUser?->isPlatformSuperAdmin())
                <li>
                    <a class="dropdown-item flex items-center gap-2 py-2 px-3 rounded-md text-orange-600 hover:bg-orange-50 transition text-[12.5px] font-medium no-underline" href="{{ route('platform.companies.index') }}">
                        <i data-lucide="shield" class="h-4 w-4 text-orange-500"></i>
                        <span>{{ __('Platform Admin') }}</span>
                    </a>
                </li>
            @endif
            @if ($availableCompanies->count() > 1)
                <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                <li class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Switch company</li>
                @foreach ($availableCompanies as $companyOption)
                    <li>
                        <form method="POST" action="{{ route('companies.switch') }}">@csrf
                            <input type="hidden" name="company_id" value="{{ $companyOption->id }}">
                            <button type="submit" class="flex w-full items-center justify-between gap-2 rounded-md px-3 py-2 text-left text-[12.5px] font-medium no-underline {{ $activeCompany?->id === $companyOption->id ? 'bg-slate-100 text-slate-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="truncate">{{ $companyOption->name }}</span>
                                @if ($activeCompany?->id === $companyOption->id)<i data-lucide="check" class="h-3.5 w-3.5 shrink-0 text-slate-600"></i>@endif
                            </button>
                        </form>
                    </li>
                @endforeach
            @endif
            <li>
                <hr class="dropdown-divider my-1 border-slate-100">
            </li>
            <li>
                <a class="dropdown-item flex items-center gap-2 py-2 px-3 rounded-md text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition text-[12.5px] font-medium no-underline" href="{{ url('/logout') }}">
                    <i data-lucide="log-out" class="h-4 w-4 text-rose-500"></i>
                    <span>{{ __('Logout') }}</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
