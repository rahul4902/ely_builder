@extends('layouts.master')

@section('page-title', 'Ticket ' . $ticket->ticket_number)
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .platform-container { padding: 20px 24px 40px; max-width: 1200px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .thread-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
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

    {{-- Breadcrumb Navigation --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('platform.tickets.index') }}" class="hover:text-slate-900 transition no-underline text-slate-500 font-medium flex items-center gap-1">
                <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i> Back to Tickets
            </a>
            <span>/</span>
            <span class="font-mono text-slate-800">{{ $ticket->ticket_number }}</span>
        </div>

        <div class="flex items-center gap-2">
            <span class="badge-status {{ $ticket->status }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $ticket->status === 'open' ? 'bg-rose-500' : ($ticket->status === 'in_progress' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
            </span>
            <span class="badge-priority {{ $ticket->priority }}">
                {{ ucfirst($ticket->priority) }} Priority
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Conversation Thread & Composer --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Ticket Main Subject Card --}}
            <div class="thread-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 m-0 leading-snug">{{ $ticket->subject }}</h2>
                        <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                            <span>Created {{ $ticket->created_at->format('d M Y, h:i A') }}</span>
                            <span>·</span>
                            <span class="capitalize">{{ str_replace('_', ' ', $ticket->category) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages Stream --}}
            <div class="space-y-4">
                @foreach ($ticket->replies as $reply)
                    <div class="thread-card p-4 {{ $reply->is_superadmin_reply ? 'border-l-4 border-l-orange-500 bg-orange-50/20' : 'bg-white' }}">
                        <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold {{ $reply->is_superadmin_reply ? 'bg-orange-600 text-white' : 'bg-slate-800 text-white' }}">
                                    {{ strtoupper(substr($reply->user?->name ?? 'U', 0, 1)) }}
                                </span>
                                <div>
                                    <span class="text-xs font-semibold text-slate-800">{{ $reply->user?->name ?? 'User' }}</span>
                                    @if ($reply->is_superadmin_reply)
                                        <span class="ml-1.5 px-1.5 py-0.2 bg-orange-100 text-orange-700 text-[10px] font-bold rounded">Platform Superadmin</span>
                                    @else
                                        <span class="ml-1.5 text-[11px] text-slate-400">Tenant</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[11px] text-slate-400 font-normal">
                                {{ $reply->created_at->format('d M, h:i A') }} ({{ $reply->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $reply->message }}
                        </div>

                        @if ($reply->attachment_path)
                            <div class="mt-3 pt-2.5 border-t border-slate-100">
                                <a href="{{ asset('storage/' . $reply->attachment_path') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-orange-600 hover:text-orange-700 font-semibold no-underline">
                                    <i data-lucide="paperclip" class="h-3.5 w-3.5"></i> View Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Superadmin Reply Composer using shared <x-form.input> --}}
            <div class="thread-card p-5">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Post Official Response</h3>
                <form method="POST" action="{{ route('platform.tickets.reply', $ticket) }}" enctype="multipart/form-data" class="space-y-3.5 m-0">
                    @csrf
                    <x-form.input
                        layout="standard"
                        label="Your Message to the Tenant"
                        name="message"
                        type="textarea"
                        required
                        placeholder="Write your response to the customer..."
                        rows="4"
                        class="form-control text-xs"
                    />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-form.input
                            layout="standard"
                            label="Update Status"
                            name="status"
                            type="select"
                            :value="$ticket->status"
                            :options="[
                                'in_progress' => 'In Progress (Awaiting Information)',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                                'open' => 'Open',
                            ]"
                            class="form-control text-xs"
                        />

                        <x-form.input
                            layout="standard"
                            label="Attach File (Optional)"
                            name="attachment"
                            type="file"
                            class="form-control text-xs file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-slate-100"
                        />
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0 flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="send" class="h-3.5 w-3.5"></i>
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Tenant Context & Ticket Actions --}}
        <div class="space-y-5">
            {{-- Tenant Info Card --}}
            <div class="thread-card p-4 space-y-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0 pb-2 border-b border-slate-100">
                    Tenant Information
                </h4>

                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Company Workspace</div>
                    <div class="text-xs font-bold text-slate-800">{{ $ticket->company?->name }}</div>
                    <div class="font-mono text-[10px] text-slate-400">ID: {{ $ticket->company?->tenant_id }}</div>
                </div>

                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Subscription Plan</div>
                    <div class="text-xs font-semibold text-slate-700">{{ $ticket->company?->planName() }}</div>
                </div>

                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Expires At</div>
                    <div class="text-xs font-medium text-slate-700">
                        {{ optional($ticket->company?->plan_expires_at)->format('d M Y') ?: 'No expiry' }}
                    </div>
                </div>

                <div>
                    <div class="text-[11px] text-slate-400 font-medium">Contact Requester</div>
                    <div class="text-xs font-semibold text-slate-800">{{ $ticket->user?->name }}</div>
                    <div class="text-xs text-slate-500">{{ $ticket->user?->email }}</div>
                    @if ($ticket->user?->work_number)
                        <div class="text-xs text-slate-500">{{ $ticket->user?->work_number }}</div>
                    @endif
                </div>

                <div class="pt-2 border-t border-slate-100">
                    {{-- 1-Click Login into this tenant --}}
                    <form action="{{ route('platform.companies.impersonate', $ticket->company) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm cursor-pointer">
                            <i data-lucide="external-link" class="h-3.5 w-3.5 text-indigo-500"></i>
                            Login to {{ $ticket->company?->name }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Quick Status Form Card --}}
            <div class="thread-card p-4 space-y-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider m-0 pb-2 border-b border-slate-100">
                    Update Ticket Status
                </h4>

                <form method="POST" action="{{ route('platform.tickets.status', $ticket) }}" class="space-y-3 m-0">
                    @csrf
                    @method('PATCH')
                    <x-form.input
                        layout="standard"
                        label="Status"
                        name="status"
                        type="select"
                        :value="$ticket->status"
                        :options="$statuses"
                        class="form-control text-xs"
                    />

                    <button type="submit" class="w-full py-1.5 rounded-lg bg-slate-800 text-xs font-semibold text-white hover:bg-slate-900 transition border-0 cursor-pointer">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
