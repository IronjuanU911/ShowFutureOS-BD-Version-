<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'attachment' => 'required|file|max:20480', // 20MB max
        ]);

        $file = $request->file('attachment');
        $fileName = $file->getClientOriginalName();
        $path = $file->store('attachments/' . $ticket->id, 'public');

        $ticket->attachments()->create([
            'uploaded_by' => auth()->id(),
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('tickets.show', ['ticket' => $ticket->id, 'tab' => 'attachments'])
            ->with('success', 'Archivo adjuntado correctamente.');
    }

    public function destroy(Ticket $ticket, TicketAttachment $attachment)
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->route('tickets.show', ['ticket' => $ticket->id, 'tab' => 'attachments'])
            ->with('success', 'Archivo adjunto eliminado.');
    }
}
