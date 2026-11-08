<?php

namespace Database\Seeders;

use App\Enums\ClientLevel;
use App\Enums\ClientStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MassiveStressTestSeeder extends Seeder
{
    public function run(): void
    {
        $startTime = microtime(true);
        echo "========================================================\n";
        echo " INICIANDO SOBRECARGA DEL SISTEMA (1,000 REGISTROS C/U)\n";
        echo "========================================================\n\n";

        // 1. Asegurar Equipo Operativo de Show Future (8 colaboradores)
        $this->ensureTeamMembers();

        $accountManagers = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'account-manager']))->pluck('id')->toArray();
        $creatives = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['creative', 'editor', 'copywriter', 'ads-manager']))->pluck('id')->toArray();
        $allUsers = User::pluck('id')->toArray();

        // 2. Poblar 1,000 CLIENTES en lotes
        echo "1. Generando 1,000 Clientes con Bóveda de Accesos...\n";
        $clientIds = $this->seedClients($accountManagers);
        echo "   -> " . count($clientIds) . " clientes insertados exitosamente.\n\n";

        // 3. Poblar 1,000 ONBOARDINGS y sus Checklist Items correspondientes
        echo "2. Generando 1,000 Onboardings de Clientes y Checklists...\n";
        $this->seedOnboardings($clientIds);
        echo "   -> 1,000 onboardings generados con éxito.\n\n";

        // 4. Poblar 1,000+ CONTACTOS DE CLIENTES
        echo "3. Generando 1,000+ Contactos Clave de Clientes...\n";
        $this->seedContacts($clientIds);
        echo "   -> Contactos clave insertados con éxito.\n\n";

        // 5. Poblar 1,000 TICKETS
        echo "4. Generando 1,000 Tickets de Producción (Kanban & Historial)...\n";
        $ticketIds = $this->seedTickets($clientIds, $creatives, $accountManagers);
        echo "   -> " . count($ticketIds) . " tickets insertados exitosamente.\n\n";

        // 6. Poblar 1,000 REVISIONES DE CONTROL DE CALIDAD (QA REVIEWS)
        echo "5. Generando 1,000 Evaluaciones de QA (Control de Calidad)...\n";
        $this->seedQaReviews($ticketIds, $accountManagers);
        echo "   -> 1,000 revisiones de QA insertadas exitosamente.\n\n";

        // 7. Poblar 1,500 COMENTARIOS EN TICKETS
        echo "6. Generando 1,500 Comentarios de Discusión en Tickets...\n";
        $this->seedTicketComments($ticketIds, $allUsers);
        echo "   -> Comentarios insertados con éxito.\n\n";

        $duration = round(microtime(true) - $startTime, 2);
        echo "========================================================\n";
        echo " SOBRECARGA COMPLETADA CON ÉXITO EN {$duration} SEGUNDOS\n";
        echo "========================================================\n";
    }

    private function ensureTeamMembers(): void
    {
        $password = Hash::make('password123');

        $members = [
            ['name' => 'Mariana López', 'email' => 'mariana@showfuture.com', 'role' => 'creative', 'phone' => '+57 311 234 5678'],
            ['name' => 'Santiago Restrepo', 'email' => 'santiago@showfuture.com', 'role' => 'copywriter', 'phone' => '+57 312 345 6789'],
            ['name' => 'Valeria Castro', 'email' => 'valeria@showfuture.com', 'role' => 'editor', 'phone' => '+57 313 456 7890'],
            ['name' => 'Felipe Morales', 'email' => 'felipe@showfuture.com', 'role' => 'ads-manager', 'phone' => '+57 314 567 8901'],
            ['name' => 'Diana Quintero', 'email' => 'diana@showfuture.com', 'role' => 'account-manager', 'phone' => '+57 315 678 9012'],
        ];

        foreach ($members as $m) {
            $user = User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => $password,
                    'phone' => $m['phone'],
                    'is_active' => true,
                ]
            );

            $role = Role::where('slug', $m['role'])->first();
            if ($role && !$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id);
            }
        }
    }

    private function seedClients(array $accountManagers): array
    {
        $prefixes = ['Apex', 'Nova', 'Lumina', 'Urban', 'Zenith', 'Terra', 'Aura', 'Vital', 'Kroma', 'Bloom', 'Echo', 'Orbit', 'Pulse', 'Stellar', 'Velo', 'Nexus', 'Optima', 'Prisma', 'Radiant', 'Sintonia', 'Atlas', 'Astra', 'Vortex', 'Alba', 'Solaria'];
        $cores = ['Style', 'Tech', 'Bio', 'Fit', 'Skin', 'Living', 'Foods', 'Audio', 'Dental', 'Fashion', 'Motors', 'Energy', 'Beauty', 'Lab', 'Wear', 'Home', 'Studio', 'Pharma', 'Media', 'Supply', 'Nutrition', 'Cafe', 'Beverage', 'Decor', 'Glow'];
        $suffixes = ['Co.', 'Group', 'Brands', 'Global', 'Online', 'Holdings', 'Solutions', 'Direct', 'Store', 'Hub', 'Latam', 'Official', 'Labs', 'Studios'];

        $statuses = [ClientStatus::Active->value, ClientStatus::Active->value, ClientStatus::Active->value, ClientStatus::Onboarding->value, ClientStatus::Paused->value, ClientStatus::Closed->value];
        $levels = [ClientLevel::Starter->value, ClientLevel::Growth->value, ClientLevel::Growth->value, ClientLevel::Scale->value];
        
        $platforms = [
            'Meta Ads + Shopify',
            'Google Ads (Search & PMax) + GA4',
            'TikTok Ads + Meta Ads',
            'Shopify + Klaviyo + Meta',
            'Amazon Ads + MercadoLibre',
            'Meta Ads + WhatsApp Funnel',
            'Google Search + LinkedIn Ads',
            'Meta Ads + WooCommerce'
        ];

        $objectives = [
            'Escalar facturación mensual manteniendo ROAS superior a 3.5 en Meta Ads',
            'Adquisición acelerada de clientes con CAC inferior a $14 USD',
            'Lanzamiento omnicanal de nueva colección con foco en margen bruto',
            'Generación de 150+ leads calificados mensuales para el equipo de ventas',
            'Dominar el Share of Voice en Google Search y campañas Performance Max',
            'Optimizar embudo de conversión y reducir abandono de carrito en Shopify',
            'Penetración de audiencia joven mediante creativos nativos en TikTok Ads',
            'Fidelización y aumento del LTV mediante retargeting y cross-selling'
        ];

        $accessPlatforms = ['Meta Business Manager', 'Shopify Admin', 'Google Ads MCC', 'TikTok Ads Manager', 'WooCommerce Admin', 'Google Analytics 4'];

        $encryptedPassword = Crypt::encryptString('ShowFuture2026*Access');

        $rows = [];
        $total = 1000;
        $now = now();

        for ($i = 1; $i <= $total; $i++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $core = $cores[array_rand($cores)];
            $suffix = $suffixes[array_rand($suffixes)];
            $name = "{$prefix} {$core} {$suffix} #{$i}";

            $status = $statuses[array_rand($statuses)];
            $level = $levels[array_rand($levels)];
            $budget = match($level) {
                'scale' => rand(5000, 25000),
                'growth' => rand(2500, 8000),
                default => rand(800, 2500),
            };

            $startDate = now()->subDays(rand(10, 500));
            $renewalDate = (clone $startDate)->addMonths(rand(6, 12));
            $accPlatform = $accessPlatforms[array_rand($accessPlatforms)];

            $slugName = Str::slug($name);

            $rows[] = [
                'name' => $name,
                'status' => $status,
                'level' => $level,
                'ads_budget' => $budget,
                'account_manager_id' => $accountManagers[array_rand($accountManagers)],
                'start_date' => $startDate->format('Y-m-d'),
                'renewal_date' => $renewalDate->format('Y-m-d'),
                'has_sla' => ($level === 'scale' || rand(1, 10) > 4),
                'main_platform' => $platforms[array_rand($platforms)],
                'main_objective' => $objectives[array_rand($objectives)],
                'logo' => null,
                'notes' => "Reuniones de seguimiento quincenales. Reportes KPI automatizados.",
                'access_platform' => $accPlatform,
                'access_url' => 'https://business.facebook.com/access/' . $slugName,
                'access_username' => "admin@{$slugName}.com",
                'access_password' => $encryptedPassword,
                'access_notes' => "BM ID: " . rand(100000000, 999999999),
                'created_at' => $startDate,
                'updated_at' => $now,
            ];
        }

        // Inserción en chunks de 200 filas para velocidad óptima
        $createdClientIds = [];
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('clients')->insert($chunk);
        }

        return DB::table('clients')->orderByDesc('id')->take($total)->pluck('id')->reverse()->values()->toArray();
    }

    private function seedOnboardings(array $clientIds): void
    {
        $onboardingRows = [];
        $checklistRows = [];
        $now = now();

        $checklistTemplates = [
            ['day' => 1, 'title' => 'Reunión de Kickoff y Alineación Estratégica', 'desc' => 'Definición de metas de ROAS, CPA y propuesta de valor única.'],
            ['day' => 2, 'title' => 'Verificación y Recepción de Accesos', 'desc' => 'Comprobación de BM, píxeles, catálogos y permisos de administrador.'],
            ['day' => 3, 'title' => 'Auditoría Técnica de Activos & Conversiones', 'desc' => 'Prueba de eventos del píxel, Conversions API (CAPI) y Google Tag Manager.'],
            ['day' => 4, 'title' => 'Definición de Estructura de Campañas & Presupuesto', 'desc' => 'Distribución de inversión entre Prospección (TOF), Consideración (MOF) y Retargeting (BOF).'],
            ['day' => 5, 'title' => 'Briefing Creativo y Entrega de Copies', 'desc' => 'Redacción de ganchos persuasivos y especificaciones para diseño visual.'],
            ['day' => 6, 'title' => 'Aprobación de Creativos y Montaje en Plataforma', 'desc' => 'Configuración de anuncios en borrador y control de calidad interno.'],
            ['day' => 7, 'title' => 'Lanzamiento Oficial & Monitoreo de 48 Horas', 'desc' => 'Encendido de pauta activa y revisión de métricas iniciales de entrega.']
        ];

        foreach ($clientIds as $clientId) {
            $isComplete = (rand(1, 10) > 3);
            $progress = $isComplete ? 100 : rand(15, 85);
            $status = $isComplete ? 'completed' : 'in_progress';
            $startDate = now()->subDays(rand(5, 120));
            $targetDate = (clone $startDate)->addDays(7);
            $completedDate = $isComplete ? (clone $startDate)->addDays(rand(5, 7)) : null;

            $onboardingRows[] = [
                'client_id' => $clientId,
                'status' => $status,
                'communication_rules_sent' => true,
                'calendar_shared' => true,
                'started_at' => $startDate->format('Y-m-d'),
                'target_completion_date' => $targetDate->format('Y-m-d'),
                'progress_percentage' => $progress,
                'completed_at' => $completedDate ? $completedDate->format('Y-m-d') : null,
                'notes' => 'Onboarding estandarizado de 7 días ShowFutureOS.',
                'communication_channel' => 'WhatsApp + Slack Privado',
                'created_at' => $startDate,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($onboardingRows, 200) as $chunk) {
            DB::table('onboardings')->insert($chunk);
        }

        // Obtener IDs de onboardings creados para poblar los checklist items de los activos
        $onboardings = DB::table('onboardings')->whereIn('client_id', $clientIds)->get(['id', 'status', 'progress_percentage']);

        foreach ($onboardings as $onb) {
            // Solo insertamos items en una muestra representativa (primeros 300) para no saturar 7,000 queries
            if ($onb->id % 3 !== 0) continue;

            $completedItemsCount = (int) round(($onb->progress_percentage / 100) * 7);

            foreach ($checklistTemplates as $index => $tpl) {
                $isItemDone = ($index < $completedItemsCount);
                $checklistRows[] = [
                    'onboarding_id' => $onb->id,
                    'day_number' => $tpl['day'],
                    'title' => $tpl['title'],
                    'description' => $tpl['desc'],
                    'is_completed' => $isItemDone,
                    'completed_at' => $isItemDone ? now()->subDays(7 - $index) : null,
                    'completed_by' => null,
                    'order' => $index + 1,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($checklistRows, 250) as $chunk) {
            DB::table('onboarding_checklist_items')->insert($chunk);
        }
    }

    private function seedContacts(array $clientIds): void
    {
        $firstNames = ['Sofía', 'Mateo', 'Valentina', 'Santiago', 'Camila', 'Alejandro', 'Isabella', 'Nicolás', 'Mariana', 'Daniel', 'Luciana', 'Sebastián', 'Gabriela', 'Felipe', 'Paula'];
        $lastNames = ['Ramírez', 'Gómez', 'Rodríguez', 'Hernández', 'Martínez', 'López', 'Restrepo', 'Vargas', 'Castro', 'Morales', 'Jiménez', 'Torres', 'Pérez', 'Silva', 'Quintero'];
        $roles = ['CEO & Fundador', 'Director de Marketing (CMO)', 'Gerente de E-commerce', 'Brand Manager', 'Líder Comercial', 'Operations Manager'];

        $contacts = [];
        $now = now();

        foreach ($clientIds as $clientId) {
            $f = $firstNames[array_rand($firstNames)];
            $l = $lastNames[array_rand($lastNames)];
            $name = "{$f} {$l}";
            $slug = Str::slug("{$f}.{$l}");

            $contacts[] = [
                'client_id' => $clientId,
                'name' => $name,
                'role' => $roles[array_rand($roles)],
                'phone' => '+57 3' . rand(10, 25) . ' ' . rand(100, 999) . ' ' . rand(1000, 9999),
                'email' => "{$slug}@contacto.com",
                'is_primary' => true,
                'notes' => 'Contacto principal para aprobaciones estratégicas y pagos.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($contacts, 250) as $chunk) {
            DB::table('client_contacts')->insert($chunk);
        }
    }

    private function seedTickets(array $clientIds, array $creatives, array $accountManagers): array
    {
        $types = [TicketType::Creative->value, TicketType::Ads->value, TicketType::Copy->value, TicketType::Commercial->value];
        $priorities = [TicketPriority::High->value, TicketPriority::Medium->value, TicketPriority::Medium->value, TicketPriority::Low->value];
        $statuses = [TicketStatus::Delivered->value, TicketStatus::Delivered->value, TicketStatus::InProgress->value, TicketStatus::Qa->value, TicketStatus::Backlog->value];

        $creativeTitles = [
            'Diseño de Carrusel 1080x1080 con Ángulo de Problema-Solución',
            'Pack de 5 Banners Display Adaptables para Retargeting Dinámico',
            'Video Reel 9:16 Demostración de Uso Real y Reacción',
            'Diseño de Infografía Comparativa vs Marcas Competidoras',
            'Motion Graphics de Beneficios con Tipografía Cinética',
            'Render 3D de Producto en Alta Definición para Header de Campaña',
            'Video UGC con Hook de Primeros 3 Segundos y CTA Final',
            'Creativos Estáticos en Formato 4:5 y 9:16 para Escala en Meta'
        ];

        $copyTitles = [
            'Redacción de 3 Ángulos Persuasivos (Emocional, Racional y Urgencia)',
            'Guión Completo para Video UGC de 30 Segundos con Storytelling',
            'Secuencia de Copies para Retargeting de Carrito Abandonado',
            'Textos Persuasivos para Landing Page de Captura de Leads',
            'Ganchos (Hooks) de Alta Retención para Anuncios en TikTok Ads',
            'Titulares y Descripciones Optimizadas para Google Search Ads'
        ];

        $adsTitles = [
            'Setup y Calibración de Conversions API (CAPI) en Shopify',
            'Reestructuración de Campañas Performance Max con Feed de Productos',
            'Lanzamiento de Campaña CBO de Escala con Creativos Ganadores',
            'Auditoría de Atribución y Desempeño en Google Analytics 4',
            'Segmentación y Test A/B de Públicos Similares (LAL 1%, 2% y 3%)',
            'Optimización de Presupuesto Diario y Poda de Conjuntos Fatigados'
        ];

        $commTitles = [
            'Reporte Ejecutivo Mensual de Rentabilidad y Retorno ROAS',
            'Propuesta de Escala de Inversión Publicitaria para Q4',
            'Alineación de Metas Comerciales y Lanzamiento de Promoción Mensual'
        ];

        $formats = ['Carousel 1080x1080', 'Video Reel 9:16 (1080x1920)', 'Banner Display 1200x628', 'Stories 9:16', 'Copy Text / Markdown', 'Setup Técnico'];

        $lastTicket = DB::table('tickets')->orderByDesc('id')->first();
        $startNum = $lastTicket ? ((int) preg_replace('/[^0-9]/', '', $lastTicket->ticket_number)) + 1 : 100;

        $rows = [];
        $totalTickets = 1000;
        $now = now();

        for ($i = 0; $i < $totalTickets; $i++) {
            $num = $startNum + $i;
            $ticketNumber = 'SF-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            $clientId = $clientIds[array_rand($clientIds)];
            $type = $types[array_rand($types)];
            $priority = $priorities[array_rand($priorities)];
            $status = $statuses[array_rand($statuses)];

            $title = match($type) {
                'creative' => $creativeTitles[array_rand($creativeTitles)],
                'copy' => $copyTitles[array_rand($copyTitles)],
                'ads' => $adsTitles[array_rand($adsTitles)],
                default => $commTitles[array_rand($commTitles)],
            };

            $assignedTo = $creatives[array_rand($creatives)];
            $createdBy = $accountManagers[array_rand($accountManagers)];

            $createdAt = now()->subDays(rand(2, 90));
            $deadline = (clone $createdAt)->addDays(rand(2, 7));
            $isDelivered = ($status === TicketStatus::Delivered->value);
            $deliveredAt = $isDelivered ? (clone $createdAt)->addDays(rand(1, 5)) : null;

            $historyUser = $isDelivered ? $createdBy : $assignedTo;
            $historyNewValue = match($status) {
                TicketStatus::Delivered->value => 'QA Aprobado (Ticket Entregado)',
                TicketStatus::Qa->value => 'Enviado a Revisión de QA',
                TicketStatus::InProgress->value => 'En Proceso de Producción',
                default => 'Creado en Backlog'
            };

            $rows[] = [
                'ticket_number' => $ticketNumber,
                'client_id' => $clientId,
                'type' => $type,
                'title' => $title,
                'description' => "Entregable táctico orientado a maximizar rendimiento de pauta y conversión según lineamientos de marca.",
                'format' => $formats[array_rand($formats)],
                'priority' => $priority,
                'status' => $status,
                'assigned_to' => $assignedTo,
                'created_by' => $createdBy,
                'deadline' => $deadline->format('Y-m-d'),
                'requires_client_approval' => rand(1, 10) > 6,
                'client_approved' => $isDelivered,
                'client_approved_at' => $isDelivered ? $deliveredAt : null,
                'delivered_at' => $deliveredAt,
                'kanban_order' => $i,
                'history_field' => 'status',
                'history_old_value' => 'backlog',
                'history_new_value' => $historyNewValue,
                'history_user_id' => $historyUser,
                'history_updated_at' => $deliveredAt ?? $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('tickets')->insert($chunk);
        }

        return DB::table('tickets')->orderByDesc('id')->take($totalTickets)->pluck('id')->reverse()->values()->toArray();
    }

    private function seedQaReviews(array $ticketIds, array $reviewers): void
    {
        $rows = [];
        $now = now();
        $totalReviews = 1000;

        $rejectionReasons = [
            'El gancho en los primeros 3 segundos no comunica con suficiente fuerza la propuesta de valor.',
            'Los subtítulos sobrepasan la zona segura del formato 9:16 y quedan tapados por la interfaz de TikTok.',
            'El tono del copy utiliza afirmaciones de rendimiento garantizado que incumplen las políticas de Meta Ads.',
            'La paleta de colores y el estilo tipográfico se desvían de la guía de estilo del cliente.',
            'La llamada a la acción (CTA) no es lo suficientemente directa; ajustar a "Comprar con 20% OFF".'
        ];

        $approvalNotes = [
            'QA Aprobado al 100%: Excelente sincronización de audio, subtítulos legibles y CTA contundente.',
            'Cumple con los 3 bloques de control: Estratégico, Marca y Técnico. Listo para entrega al cliente.',
            'Verificación técnica superada con éxito. Formato y compresión óptimos para carga rápida.',
            'Ángulo de urgencia muy bien planteado. Pauta lista para encendido en Meta Business Manager.'
        ];

        for ($i = 0; $i < $totalReviews; $i++) {
            // Distribuir entre los tickets
            $ticketId = $ticketIds[$i % count($ticketIds)];
            $reviewerId = $reviewers[array_rand($reviewers)];

            $isApproved = (rand(1, 10) <= 8); // 80% aprobados, 20% rechazados
            $reviewDate = now()->subHours(rand(1, 720));

            $rows[] = [
                'ticket_id' => $ticketId,
                'reviewed_by' => $reviewerId,
                'is_approved' => $isApproved,
                'strategic_message_aligned' => true,
                'strategic_cta_clear' => $isApproved,
                'strategic_funnel_correct' => true,
                'brand_tone_correct' => $isApproved,
                'brand_no_risky_claims' => true,
                'brand_policy_aligned' => $isApproved,
                'tech_format_correct' => true,
                'tech_subtitles_legible' => $isApproved,
                'tech_duration_adequate' => true,
                'rejection_reason' => $isApproved ? null : $rejectionReasons[array_rand($rejectionReasons)],
                'notes' => $isApproved ? $approvalNotes[array_rand($approvalNotes)] : 'Ajustar observaciones y reenviar a QA.',
                'reviewed_at' => $reviewDate,
                'created_at' => $reviewDate,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('qa_reviews')->insert($chunk);
        }
    }

    private function seedTicketComments(array $ticketIds, array $users): void
    {
        $commentTemplates = [
            'Ya subí la primera versión del creativo a Drive. Quedo atento a feedback del Account Manager.',
            'Ajusté la tipografía del gancho principal según las observaciones de la última revisión.',
            'Revisé la cuenta publicitaria de Meta y el ROAS de este ángulo subió a 4.1. Excelente desempeño.',
            'Se solicitó confirmación del cliente sobre el código de cupón para incluirlo en el copy.',
            'Audio masterizado y subtítulos reubicados en la zona segura. Listo para verificación QA.',
            'Entregable verificado y aprobado. Pasamos a publicar en los conjuntos de anuncios programados.',
            'Recordatorio: La fecha límite de este ticket es mañana a mediodía para no retrasar el lanzamiento.'
        ];

        $rows = [];
        $totalComments = 1500;
        $now = now();

        for ($i = 0; $i < $totalComments; $i++) {
            $ticketId = $ticketIds[array_rand($ticketIds)];
            $userId = $users[array_rand($users)];

            $rows[] = [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'body' => $commentTemplates[array_rand($commentTemplates)],
                'created_at' => now()->subHours(rand(1, 400)),
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('ticket_comments')->insert($chunk);
        }
    }
}

