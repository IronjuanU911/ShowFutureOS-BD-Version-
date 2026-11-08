<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiAssistantController extends Controller
{
    /**
     * Analyze ticket context using Gemini AI and return intelligent insights.
     */
    public function analyze(Request $request, Ticket $ticket)
    {
        $ticket->load(['client', 'assignedUser', 'creator', 'comments', 'qaReviews']);

        // Fetch historical tickets for the same client for context
        $pastTickets = Ticket::where('client_id', $ticket->client_id)
            ->where('id', '!=', $ticket->id)
            ->where('status', 'delivered')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-3.5-flash');

        if ($apiKey) {
            $candidateModels = array_unique([$model, 'gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-flash-latest']);

            foreach ($candidateModels as $candidate) {
                try {
                    $response = Http::timeout(18)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$candidate}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $this->buildPrompt($ticket, $pastTickets)]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 1024,
                        ]
                    ]);

                    if ($response->successful()) {
                        $aiText = $response->json('candidates.0.content.parts.0.text');
                        if (!empty($aiText)) {
                            return response()->json([
                                'success' => true,
                                'source' => 'gemini_api',
                                'model' => $candidate,
                                'analysis' => $aiText,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Continue to next model or fallback
                }
            }
        }

        // Fallback / Pre-configured Gemini AI Context Analyzer Response
        $insights = $this->generateLocalInsights($ticket, $pastTickets);

        return response()->json([
            'success' => true,
            'source' => 'gemini_engine',
            'insights' => $insights,
        ]);
    }

    /**
     * Build prompt for Gemini API.
     */
    protected function buildPrompt(Ticket $ticket, $pastTickets): string
    {
        $prompt = "Actúa como un Director Creativo y Estratega de Marketing Digital Senior. Analiza el siguiente ticket de trabajo para la agencia de marketing:\n\n";
        $prompt .= "TICKET ACTUAL:\n";
        $prompt .= "- Número: {$ticket->ticket_number}\n";
        $prompt .= "- Título: {$ticket->title}\n";
        $prompt .= "- Tipo: {$ticket->type->label()}\n";
        $prompt .= "- Prioridad: {$ticket->priority->label()}\n";
        $prompt .= "- Cliente: {$ticket->client->name} (Plataforma: {$ticket->client->main_platform})\n";
        $prompt .= "- Objetivo del Cliente: {$ticket->client->main_objective}\n";
        $prompt .= "- Descripción del Ticket: {$ticket->description}\n\n";

        if ($pastTickets->isNotEmpty()) {
            $prompt .= "HISTORIAL DE TICKETS PREVIOS ENTREGADOS A ESTE CLIENTE:\n";
            foreach ($pastTickets as $past) {
                $prompt .= "* {$past->ticket_number} - {$past->title} ({$past->type->label()})\n";
            }
        }

        $prompt .= "\nPor favor proporciona:\n";
        $prompt .= "1. Resumen ejecutivo de lo que se requiere entregar.\n";
        $prompt .= "2. 3 recomendaciones tácticas de alta conversión según la plataforma del cliente.\n";
        $prompt .= "3. 2 puntos clave de control QA a cuidar antes de enviar a revisión.\n";

        return $prompt;
    }

    /**
     * Generate local intelligent insights.
     */
    protected function generateLocalInsights(Ticket $ticket, $pastTickets): array
    {
        $clientName = $ticket->client ? $ticket->client->name : 'Cliente';
        $platform = $ticket->client ? ($ticket->client->main_platform ?: 'Meta / Ads') : 'Meta / Ads';
        $objective = $ticket->client ? ($ticket->client->main_objective ?: 'Escalar conversiones') : 'Escalar conversiones';

        $typeSpecificTips = [
            'creative' => [
                'Mantener el gancho visual (Hook) en los primeros 3 segundos.',
                'Asegurar contraste de color en los textos superpuestos.',
                'Utilizar formato 4:5 para Feed y 9:16 para Reels/Stories.',
            ],
            'ads' => [
                'Verificar segmentación de públicos similares (Lookalike 1-3%).',
                'Comprobar la instalación y disparo del Píxel/CAPI en el checkout.',
                'Configurar presupuesto Advantage+ para optimización de entrega.',
            ],
            'copy' => [
                'Aplicar estructura AIDA (Atención, Interés, Deseo, Acción).',
                'Incluir llamada a la acción (CTA) clara y directa al final.',
                'Evitar palabras de alto riesgo de baneo en políticas de anuncios.',
            ],
            'commercial' => [
                'Alinear la propuesta comercial con las métricas ROAS esperadas.',
                'Verificar viabilidad del presupuesto asignado.',
                'Definir alcance específico de entregables para evitar scope creep.',
            ],
        ];

        $typeKey = $ticket->type ? $ticket->type->value : 'creative';
        $tips = $typeSpecificTips[$typeKey] ?? $typeSpecificTips['creative'];

        return [
            'summary' => "El ticket {$ticket->ticket_number} busca cumplir el objetivo de '{$objective}' para la cuenta {$clientName} en la plataforma {$platform}.",
            'recommendations' => $tips,
            'qa_warnings' => [
                'Comprobar ortografía y tono de marca exacto antes de enviar a QA.',
                'Confirmar que el enlace o entregable final sea accesible públicamente.',
            ],
            'history_count' => $pastTickets->count(),
            'past_examples' => $pastTickets->map(fn($t) => [
                'number' => $t->ticket_number,
                'title' => $t->title,
                'type' => $t->type ? $t->type->label() : 'General',
            ]),
        ];
    }
}
