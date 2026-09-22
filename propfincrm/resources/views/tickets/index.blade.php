@extends('layouts.master')

@section('page-title', 'Support Tickets')
@section('main-class', 'p-0 bg-slate-50/50 min-h-[calc(100vh-44px)]')

@section('styles')
<style>
    .tickets-container { padding: 20px 24px 40px; max-width: 1200px; margin: 0 auto; font-family: 'Outfit', sans-serif; }
    .tickets-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden; }
    .tickets-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #edf2f7; }
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
<div class="tickets-container space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Help &amp; Support Tickets</h1>
            <p class="text-xs text-slate-500 mt-0.5">Need assistance with your ElyLeads CRM? Create a ticket and our platform team will help you.</p>
        </div>
        <div>
            <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-orange-600 text-xs font-semibold text-white hover:bg-orange-700 transition shadow-sm border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                Create Support Ticket
            </button>
        </div>
    </div>

    {{-- Tickets Card Table --}}
    <div class="tickets-card">
        <div class="tickets-card-header">
            <div>
                <h3 class="text-sm font-bold text-slate-800 m-0">Your Support Tickets</h3>
                <span class="text-xs text-slate-400">Track inquiries for {{ $company->name }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Ticket ID &amp; Subject</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-orange-600 text-[13px] no-underline">
                                    {{ $ticket->subject }}
                                </a>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5">
                                    {{ $ticket->ticket_number }}
                                </div>
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
                                <div class="text-xs text-slate-700 font-medium">{{ $ticket->user?->name ?? 'You' }}</div>
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
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-100 py-1 rounded-lg text-xs" style="min-width: 140px;">
                                        <li>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="dropdown-item py-1.5 px-3 flex items-center text-slate-700 hover:bg-slate-50 no-underline">
                                                <i class="fa-regular fa-comments me-2 text-slate-400"></i> View Discussion
                                            </a>
                                        </li>
                                        @if ($ticket->status !== 'closed')
                                            <li><hr class="dropdown-divider my-1 border-slate-100"></li>
                                            <li>
                                                <form action="{{ route('tickets.close', $ticket) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-1.5 px-3 flex items-center text-slate-600 hover:bg-slate-50 w-full text-start border-0 bg-transparent" onclick="return confirm('Do you want to mark this ticket as closed?')">
                                                        <i class="fa-solid fa-check me-2 text-emerald-500"></i> Mark Closed
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="life-buoy" class="h-8 w-8 mx-auto mb-2 text-slate-300"></i>
                                You haven't raised any support tickets yet.
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

{{-- Create Support Ticket Modal using shared <x-form.input> --}}
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-xl overflow-hidden" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header border-b border-slate-100 px-5 py-3.5 bg-slate-50">
                <h5 class="text-sm font-bold text-slate-800 m-0">Open a New Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-5 space-y-3.5">
                <x-form.input
                    layout="standard"
                    label="Subject / Issue Summary"
                    name="subject"
                    required
                    placeholder="Briefly describe what you need help with..."
                    class="form-control text-xs"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-form.input
                        layout="standard"
                        label="Category"
                        name="category"
                        type="select"
                        :options="$categories"
                        required
                        class="form-control text-xs"
                    />

                    <x-form.input
                        layout="standard"
                        label="Priority Level"
                        name="priority"
                        type="select"
                        :options="$priorities"
                        value="medium"
                        required
                        class="form-control text-xs"
                    />
                </div>

                <x-form.input
                    layout="standard"
                    label="Detailed Description"
                    name="message"
                    type="textarea"
                    required
                    placeholder="Provide details, error messages, steps to reproduce, or questions..."
                    rows="4"
                    class="form-control text-xs"
                />

                <x-form.input
                    layout="standard"
                    label="Attachment / Screenshot (optional)"
                    name="attachment"
                    type="file"
                    class="form-control text-xs file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-slate-100"
                />
            </div>

            <div class="modal-footer border-t border-slate-100 px-5 py-3 bg-slate-50 flex justify-end gap-2">
                <button type="button" class="btn btn-sm btn-light text-xs font-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-warning text-xs font-semibold px-4 bg-orange-600 text-white hover:bg-orange-700 border-0">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
