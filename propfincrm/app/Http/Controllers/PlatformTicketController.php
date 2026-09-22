<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class PlatformTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'platform.super_admin']);
    }

    public function index(Request $request)
    {
        $query = SupportTicket::with(['company', 'user', 'latestReply'])
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $tickets = $query->paginate(20)->withQueryString();

        $metrics = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('platform.tickets.index', [
            'tickets' => $tickets,
            'metrics' => $metrics,
            'companies' => $companies,
            'categories' => SupportTicket::categories(),
            'priorities' => SupportTicket::priorities(),
            'statuses' => SupportTicket::statuses(),
        ]);
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['company.plan', 'user', 'replies.user']);

        return view('platform.tickets.show', [
            'ticket' => $ticket,
            'statuses' => SupportTicket::statuses(),
            'priorities' => SupportTicket::priorities(),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'in:open,in_progress,resolved,closed'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
        }

        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'is_superadmin_reply' => true,
            'attachment_path' => $attachmentPath,
        ]);

        $ticket->update([
            'last_reply_at' => now(),
            'status' => $data['status'] ?? ($ticket->status === 'open' ? 'in_progress' : $ticket->status),
        ]);

        return back()->with('flash_message', 'Reply posted successfully to the tenant.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,resolved,closed'],
        ]);

        $ticket->update(['status' => $data['status']]);

        return back()->with('flash_message', "Ticket status changed to {$data['status']}.");
    }
}
