<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('tickets.show', ['ticket' => $ticket->id, 'tab' => 'comments'])
            ->with('success', 'Comentario registrado correctamente.');
    }
}
