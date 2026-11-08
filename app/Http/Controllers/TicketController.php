<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TicketController extends Controller
{
    /**
     * Display Kanban board of tickets grouped by status.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['client', 'assignedUser', 'creator', 'latestApprovedQa', 'qaReviews']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        $allTickets = $query->orderBy('kanban_order')->orderByDesc('created_at')->get();

        // Group by status for Kanban columns
        $kanban = [
            'backlog' => $allTickets->where('status', TicketStatus::Backlog),
            'in_progress' => $allTickets->where('status', TicketStatus::InProgress),
            'qa' => $allTickets->where('status', TicketStatus::Qa),
            'delivered' => $allTickets->where('status', TicketStatus::Delivered),
        ];

        $clients = Client::orderBy('name')->get();
        $teamMembers = User::where('is_active', true)->orderBy('name')->get();

        return view('tickets.index', compact('kanban', 'allTickets', 'clients', 'teamMembers'));
    }

    /**
     * Show form to create a new ticket.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $teamMembers = User::where('is_active', true)->orderBy('name')->get();

        return view('tickets.create', compact('clients', 'teamMembers'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'type' => ['required', new Enum(TicketType::class)],
            'priority' => ['required', new Enum(TicketPriority::class)],
            'assigned_to' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
            'format' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'requires_client_approval' => 'boolean',
        ]);

        $validated['ticket_number'] = Ticket::generateTicketNumber();
        $validated['status'] = TicketStatus::Backlog;
        $validated['created_by'] = auth()->id();
        $validated['requires_client_approval'] = $request->has('requires_client_approval');

        $ticket = Ticket::create($validated);

        // Record history directly in ticket columns
        $ticket->recordHistory('status', null, TicketStatus::Backlog->label(), auth()->id());

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} creado exitosamente en el Backlog.");
    }

    /**
     * Display ticket detail view.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'client',
            'assignedUser',
            'creator',
            'comments.user',
            'attachments.uploader',
            'historyUser',
            'qaReviews.reviewer',
        ]);

        $teamMembers = User::where('is_active', true)->orderBy('name')->get();

        return view('tickets.show', compact('ticket', 'teamMembers'));
    }

    /**
     * Show form to edit ticket.
     */
    public function edit(Ticket $ticket)
    {
        $clients = Client::orderBy('name')->get();
        $teamMembers = User::where('is_active', true)->orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'clients', 'teamMembers'));
    }

    /**
     * Update ticket details.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'type' => ['required', new Enum(TicketType::class)],
            'priority' => ['required', new Enum(TicketPriority::class)],
            'assigned_to' => 'nullable|exists:users,id',
            'deadline' => 'nullable|date',
            'format' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'requires_client_approval' => 'boolean',
        ]);

        $validated['requires_client_approval'] = $request->has('requires_client_approval');

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado correctamente.');
    }

    /**
     * Update ticket status (Kanban drag or status button).
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', new Enum(TicketStatus::class)],
        ]);

        $oldStatus = $ticket->status;
        $newStatus = TicketStatus::from($request->input('status'));

        if ($oldStatus === $newStatus) {
            return back();
        }

        // Business Rule: Cannot transition directly to Delivered without an approved QA review
        if ($newStatus === TicketStatus::Delivered && !$ticket->has_approved_qa) {
            return back()->with('error', 'No se puede marcar como Entregado un ticket sin una revisión de QA aprobada.');
        }

        $ticket->status = $newStatus;
        if ($newStatus === TicketStatus::Delivered) {
            $ticket->delivered_at = now();
        }
        $ticket->save();

        // Record History Log directly in ticket columns
        $ticket->recordHistory('status', $oldStatus->label(), $newStatus->label(), auth()->id());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $ticket->status->value,
                'status_label' => $ticket->status->label(),
            ]);
        }

        return back()->with('success', "Estado del ticket {$ticket->ticket_number} actualizado a {$newStatus->label()}.");
    }

    /**
     * Soft delete ticket.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'El ticket fue archivado exitosamente.');
    }
}
