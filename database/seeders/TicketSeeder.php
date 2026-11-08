<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Client;
use App\Models\QaReview;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        if ($clients->isEmpty()) return;

        $admin = User::where('email', 'admin@showfuture.com')->first() ?? User::first();
        $am = User::where('email', 'am@showfuture.com')->first() ?? User::first();
        $ads = User::where('email', 'ads@showfuture.com')->first() ?? User::first();

        $c1 = $clients->first();
        $c2 = $clients->skip(1)->first() ?? $c1;

        // 1. Ticket Backlog
        $t1 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'client_id' => $c1->id,
            'title' => 'Diseño de Banners para Campaña CyberDay',
            'type' => TicketType::Creative,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Backlog,
            'assigned_to' => $ads->id,
            'created_by' => $am->id,
            'deadline' => now()->addDays(4),
            'format' => 'Carousel 1080x1080 + Story 9:16',
            'description' => 'Crear lote de 5 creativos visuales enfocados en oferta 30% OFF en primera compra.',
            'requires_client_approval' => true,
            'history_field' => 'status',
            'history_old_value' => null,
            'history_new_value' => 'Backlog',
            'history_user_id' => $am->id,
            'history_updated_at' => now()->subHours(10),
        ]);

        // 2. Ticket En Proceso
        $t2 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'client_id' => $c1->id,
            'title' => 'Redacción de Copies para Anuncios Meta Ads',
            'type' => TicketType::Copy,
            'priority' => TicketPriority::Medium,
            'status' => TicketStatus::InProgress,
            'assigned_to' => $admin->id,
            'created_by' => $am->id,
            'deadline' => now()->addDays(2),
            'format' => '3 variaciones de Copy (Ángulo Emocional, Racional, Urgencia)',
            'description' => 'Redactar textos persuacivos probando ganchos de conversión alta para productos top ventas.',
            'requires_client_approval' => false,
        ]);

        TicketComment::create([
            'ticket_id' => $t2->id,
            'user_id' => $admin->id,
            'body' => 'Avance 70%: Ya redacté la versión de ángulo emocional y urgencia. Falta el cierre con CTA.',
            'created_at' => now()->subHours(2),
        ]);

        // 3. Ticket En QA
        $t3 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'client_id' => $c2->id,
            'title' => 'Video Reel 9:16 Demostración de Producto',
            'type' => TicketType::Creative,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Qa,
            'assigned_to' => $ads->id,
            'created_by' => $am->id,
            'deadline' => now()->addDay(),
            'format' => 'MP4 1080x1920 (Reel/TikTok)',
            'description' => 'Video dinámico de 25 segundos mostrando uso real del producto con subtítulos integrados.',
            'requires_client_approval' => true,
        ]);

        TicketComment::create([
            'ticket_id' => $t3->id,
            'user_id' => $ads->id,
            'body' => 'Video renderizado y cargado a la plataforma. Listo para revisión de QA por el AM.',
            'created_at' => now()->subHour(),
        ]);

        // 4. Ticket Entregado con QA Aprobado
        $t4 = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'client_id' => $c2->id,
            'title' => 'Configuración de Píxel y Eventos de Conversión CAPI',
            'type' => TicketType::Ads,
            'priority' => TicketPriority::High,
            'status' => TicketStatus::Delivered,
            'assigned_to' => $ads->id,
            'created_by' => $am->id,
            'deadline' => now()->subDay(),
            'delivered_at' => now()->subHours(3),
            'format' => 'Meta CAPI + GA4',
            'description' => 'Verificación y trazabilidad completa de Purchase y Lead events.',
            'requires_client_approval' => false,
        ]);

        QaReview::create([
            'ticket_id' => $t4->id,
            'reviewed_by' => $am->id,
            'is_approved' => true,
            'strategic_message_aligned' => true,
            'strategic_cta_clear' => true,
            'strategic_funnel_correct' => true,
            'brand_tone_correct' => true,
            'brand_no_risky_claims' => true,
            'brand_policy_aligned' => true,
            'tech_format_correct' => true,
            'tech_subtitles_legible' => true,
            'tech_duration_adequate' => true,
            'notes' => 'Píxel test verificado correctamente en el sitio. Sin observaciones.',
            'reviewed_at' => now()->subHours(3),
        ]);
    }
}
