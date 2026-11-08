<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\QaReview;
use App\Models\Ticket;
use Illuminate\Http\Request;

class QaReviewController extends Controller
{
    /**
     * Store or update a QA Review for a ticket.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $currentUser = auth()->user();
        if (!$currentUser || (!$currentUser->isAdmin() && !$currentUser->isAccountManager())) {
            abort(403, 'Acceso Denegado: Solo los Administradores y Account Managers tienen autorización para evaluar y aprobar tickets en Control de Calidad (QA).');
        }

        $validated = $request->validate([
            'is_approved' => 'required|boolean',
            'strategic_message_aligned' => 'boolean',
            'strategic_cta_clear' => 'boolean',
            'strategic_funnel_correct' => 'boolean',
            'brand_tone_correct' => 'boolean',
            'brand_no_risky_claims' => 'boolean',
            'brand_policy_aligned' => 'boolean',
            'tech_format_correct' => 'boolean',
            'tech_subtitles_legible' => 'boolean',
            'tech_duration_adequate' => 'boolean',
            'rejection_reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $isApproved = $request->boolean('is_approved');

        $reviewData = [
            'ticket_id' => $ticket->id,
            'reviewed_by' => auth()->id(),
            'is_approved' => $isApproved,
            'strategic_message_aligned' => $request->boolean('strategic_message_aligned'),
            'strategic_cta_clear' => $request->boolean('strategic_cta_clear'),
            'strategic_funnel_correct' => $request->boolean('strategic_funnel_correct'),
            'brand_tone_correct' => $request->boolean('brand_tone_correct'),
            'brand_no_risky_claims' => $request->boolean('brand_no_risky_claims'),
            'brand_policy_aligned' => $request->boolean('brand_policy_aligned'),
            'tech_format_correct' => $request->boolean('tech_format_correct'),
            'tech_subtitles_legible' => $request->boolean('tech_subtitles_legible'),
            'tech_duration_adequate' => $request->boolean('tech_duration_adequate'),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'reviewed_at' => now(),
        ];

        $qaReview = QaReview::create($reviewData);

        if ($isApproved) {
            // QA Approved -> Move Ticket to Delivered
            $oldStatus = $ticket->status;
            $ticket->update([
                'status' => TicketStatus::Delivered,
                'delivered_at' => now(),
            ]);

            $ticket->recordHistory('qa_approval', $oldStatus->label(), 'QA Aprobado (Ticket Entregado)', auth()->id());

            $message = 'Revisión de QA APROBADA con éxito. El ticket ha sido marcado como ENTREGADO.';
        } else {
            // QA Rejected -> Return Ticket to In Progress with rejection feedback
            $oldStatus = $ticket->status;
            $ticket->update([
                'status' => TicketStatus::InProgress,
            ]);

            $ticket->recordHistory('qa_rejection', $oldStatus->label(), 'Rechazado en QA -> Devuelto a En Proceso: ' . ($validated['rejection_reason'] ?? 'Ajustes requeridos'), auth()->id());

            $message = 'Revisión de QA RECHAZADA. El ticket fue devuelto a "En Proceso" para aplicar las observaciones.';
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', $message);
    }
}
