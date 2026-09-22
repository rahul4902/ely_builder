@extends('layouts.master')

@section('page-title', 'Support Tickets Desk')
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .platform-container { padding: 20px 24px 40px; max-width: 1400px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .stat-label { font-size: 11.5px; font-weight: 500; color: #64748b; margin-bottom: 2px; }
    .stat-val { font-size: 20px; font-weight: 700; color: #1e293b; line-height: 1.1; }
    .platform-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden; }
    .platform-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #edf2f7; }
    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .custom-table th { padding: 12px 18px; background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #edf2f7; text-align: left; }
    .custom-table td { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
    .custom-table tr:hover { background-color: #fafbfc; }
    .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
    .badge-status.open { background: #fee2e2; color: #b91c1c; }
    .badge-status.in_progress { background: #fef3c7; color: #b45309; }
    .badge-status.resolved { background: #dcfce7; color: #15803d; }
    .badge-status.closed { background: #f1f5f9; color: #64748b; }
    .badge-priority { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10.5px; font-weight: 600; }
    .badge-priority.low { background: #f1f5f9; color: #475569; }
    .badge-priority.medium { background: #e0f2fe; color: #0369a1; }
    .badge-priority.high { background: #fed7aa; color: #c2410c; }
    .badge-priority.urgent { background: #fecdd3; color: #be123c; }
</style>
@endsection

@section('content')
<div class="platform-container space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Superadmin Support Desk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage customer inquiries, technical issues, and billing requests across all tenant companies.</p>
        </div>
    </div>

    {{-- Stat Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Tickets</div>
                <div class="stat-val text-slate-800">{{ $metrics['total'] }}</div>
            </div>
            <div class="stat-icon bg-slate-100 text-slate-600">
                <i data-lucide="inbox" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Open / Unresolved</div>
                <div class="stat-val text-rose-600">{{ $metrics['open'] }}</div>
            </div>
            <div class="stat-icon bg-rose-50 text-rose-600">
                <i data-lucide="alert-circle" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">In Progress</div>
                <div class="stat-val text-amber-600">{{ $metrics['in_progress'] }}</div>
            </div>
            <div class="stat-icon bg-amber-50 text-amber-600">
                <i data-lucide="clock" class="h-5 w-5"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Resolved / Closed</div>
                <div class="stat-val text-emerald-600">{{ $metrics['resolved'] }}</div>
            </div>
            <div class="stat-icon bg-emerald-50 text-emerald-600">
                <i data-lucide="check-check" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-sm">
        <form method="GET" action="{{ route('platform.tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end m-0">
            <x-form.input
                layout="standard"
                label="Filter by Tenant"
                name="company_id"
                type="select"
                :value="request('company_id')"
                :options="$companies->pluck('name', 'id')->prepend('All Companies', '')->toArray()"
                class="form-control text-xs"
            />

            <x-form.input
                layout="standard"
                label="Status"
                name="status"
                type="select"
                :value="request('status')"
                :options="collect($statuses)->prepend('All Statuses', '')->toArray()"
                class="form-control text-xs"
            />

            <x-form.input
                layout="standard"
                label="Priority"
                name="priority"
                type="select"
                :value="request('priority')"
                :options="collect($priorities)->prepend('All Priorities', '')->toArray()"
                class="form-control text-xs"
            />

            <x-form.input
                layout="standard"
                label="Category"
                name="category"
                type="select"
                :value="request('category')"
                :options="collect($categories)->prepend('All Categories', '')->toArray()"
                class="form-control text-xs"
            />

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full h-[35px] rounded-lg bg-slate-800 text-xs font-semibold text-white hover:bg-slate-900 transition border-0 cursor-pointer">
                    Filter
                </button>
                @if (request()->hasAny(['company_id', 'status', 'priority', 'category']))
                    <a href="{{ route('platform.tickets.index') }}" class="h-[35px] px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 transition no-underline">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tickets Main Table --}}
    <div class="platform-card">
        <div class="platform-card-header">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">Tickets Feed</h3>
                <span class="text-xs text-slate-400">All tenant communications ordered by latest activity</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Ticket ID &amp; Subject</th>
                        <th>Tenant / Company</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Requester</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('platform.tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-orange-600 text-[13px] no-underline">
                                    {{ $ticket->subject }}
                                </a>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5">
                                    {{ $ticket->ticket_number }}
                                </div>
                            </td>

                            <td>
                                <div class="font-semibold text-slate-800 text-xs">{{ $ticket->company?->name ?? 'Unknown' }}</div>
                            </td>

                            <td>
                                <span class="capitalize text-xs text-slate-600">
                                    {{ str_replace('_', ' ', $ticket->category) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-priority {{ $ticket->priority }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>

                            <td>
                                <div class="text-xs text-slate-700 font-medium">{{ $ticket->user?->name ?? 'User' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $ticket->user?->email }}</div>
                            </td>

                            <td>
                                <span class="badge-status {{ $ticket->status }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $ticket->status === 'open' ? 'bg-rose-500' : ($ticket->status === 'in_progress' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                            </td>

                            <td class="text-xs text-slate-500">
                                {{ optional($ticket->last_reply_at ?? $ticket->updated_at)->diffForHumans() }}
                            </td>

                            <td class="text-end">
                                {{-- 3-Dot Ellipsis Dropdown (Strict AGENTS.md Lead-List Action Pattern) --}}
                                <div class="dropdown text-end inline-block">
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition border-0 bg-transparent cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fa-solid fa-ellipsis text-sm"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 155px;">
                                        <li>
                                            <a href="{{ route('platform.tickets.show', $ticket) }}" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 no-underline">
                                                <i class="fa-regular fa-comments me-2 text-slate-400"></i> Open Thread
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                        @if ($ticket->status !== 'resolved')
                                            <li>
                                                <form action="{{ route('platform.tickets.status', $ticket) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="resolved">
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-emerald-600 hover:bg-emerald-50 w-full text-start border-0 bg-transparent">
                                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Mark Resolved
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if ($ticket->status !== 'closed')
                                            <li>
                                                <form action="{{ route('platform.tickets.status', $ticket) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-slate-600 hover:bg-slate-50 w-full text-start border-0 bg-transparent">
                                                        <i class="fa-solid fa-lock me-2 text-slate-400"></i> Close Ticket
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i data-lucide="message-square" class="h-8 w-8 mx-auto mb-2 text-slate-300"></i>
                                No support tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
