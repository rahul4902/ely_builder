<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TenantTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        $tickets = SupportTicket::where('company_id', $company->id)
            ->with(['user', 'latestReply'])
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('tickets.index', [
            'tickets' => $tickets,
            'company' => $company,
            'categories' => SupportTicket::categories(),
            'priorities' => SupportTicket::priorities(),
            'statuses' => SupportTicket::statuses(),
        ]);
    }

    public function create(Request $request)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        return view('tickets.create', [
            'company' => $company,
            'categories' => SupportTicket::categories(),
            'priorities' => SupportTicket::priorities(),
        ]);
    }

    public function store(Request $request)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company, 403, 'No active company selected.');

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:technical,billing,feature_request,general'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip'],
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'company_id' => $company->id,
            'user_id' => $request->user()->id,
            'subject' => $data['subject'],
            'category' => $data['category'],
            'priority' => $data['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket_attachments', 'public');
        }

        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'],
            'is_superadmin_reply' => false,
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('tickets.show', $ticket)->with('flash_message', "Support ticket {$ticket->ticket_number} created successfully.");
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company && $ticket->company_id === $company->id, 403, 'Unauthorized.');

        $ticket->load(['user', 'replies.user']);

        return view('tickets.show', [
            'ticket' => $ticket,
            'company' => $company,
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company && $ticket->company_id === $company->id, 403, 'Unauthorized.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
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
            'is_superadmin_reply' => false,
            'attachment_path' => $attachmentPath,
        ]);

        $ticket->update([
            'last_reply_at' => now(),
            'status' => $ticket->status === 'closed' ? 'open' : $ticket->status,
        ]);

        return back()->with('flash_message', 'Your reply has been submitted.');
    }

    public function close(Request $request, SupportTicket $ticket)
    {
        $company = $request->user()->activeCompany();
        abort_unless($company && $ticket->company_id === $company->id, 403, 'Unauthorized.');

        $ticket->update(['status' => 'closed']);

        return back()->with('flash_message', "Ticket {$ticket->ticket_number} closed.");
    }
}
