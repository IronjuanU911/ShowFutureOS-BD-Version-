<?php

namespace Database\Seeders;

use App\Enums\ClientLevel;
use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\Onboarding;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $am = User::where('email', 'am@showfuture.com')->first() ?? User::first();

        // Client 1: Onboarding
        $client1 = Client::create([
            'name' => 'Kombucha Bio E-Commerce',
            'status' => ClientStatus::Onboarding,
            'level' => ClientLevel::Growth,
            'ads_budget' => 3500.00,
            'account_manager_id' => $am->id,
            'start_date' => now(),
            'renewal_date' => now()->addMonths(6),
            'has_sla' => true,
            'main_platform' => 'Meta Ads + Shopify',
            'main_objective' => 'Escalar facturación mensual a $80k USD manteniendo ROAS superior a 3.0',
            'notes' => 'El cliente prefiere reportes quincenales por WhatsApp y llamadas de seguimiento los jueves.',
            'access_platform' => 'Meta Business Manager',
            'access_url' => 'https://business.facebook.com',
            'access_username' => 'admin@kombuchabio.com',
            'access_password' => 'Kombucha#2026',
            'access_notes' => 'ID de BM: 8941204910249',
        ]);

        $onboarding1 = Onboarding::create([
            'client_id' => $client1->id,
            'started_at' => now(),
            'target_completion_date' => now()->addDays(7),
            'progress_percentage' => 43,
            'status' => 'in_progress',
        ]);

        $items1 = [
            ['day_number' => 1, 'title' => 'Reunión de Kickoff y Bienvenida', 'description' => 'Alineación de objetivos con el cliente.', 'is_completed' => true],
            ['day_number' => 2, 'title' => 'Solicitud y Verificación de Accesos', 'description' => 'Recopilar credenciales de Meta BM, Google Ads y Shopify.', 'is_completed' => true],
            ['day_number' => 3, 'title' => 'Auditoría Inicial de Activos Digitales', 'description' => 'Análisis de campañas pasadas e historial de píxel.', 'is_completed' => true],
            ['day_number' => 4, 'title' => 'Definición de Estrategia & Estructura', 'description' => 'Elaboración del plan táctico de pauta.', 'is_completed' => false],
            ['day_number' => 5, 'title' => 'Configuración de Píxeles & CAPI', 'description' => 'Instalación de eventos de conversión.', 'is_completed' => false],
            ['day_number' => 6, 'title' => 'Redacción de Copies y Creativos', 'description' => 'Primer lote de anuncios visuales y copies.', 'is_completed' => false],
            ['day_number' => 7, 'title' => 'Lanzamiento de Campañas', 'description' => 'Verificación QA y encendido de pauta.', 'is_completed' => false],
        ];

        foreach ($items1 as $index => $itemData) {
            OnboardingChecklistItem::create(array_merge($itemData, [
                'onboarding_id' => $onboarding1->id,
                'order' => $index + 1,
            ]));
        }

        $client1->contacts()->create([
            'name' => 'Sofía Ramírez',
            'role' => 'Directora de Marketing',
            'email' => 'sofia@kombuchabio.com',
            'phone' => '+57 301 555 8899',
            'is_primary' => true,
        ]);

        // Client 2: Active
        $client2 = Client::create([
            'name' => 'Apparel Co. Fitness Wear',
            'status' => ClientStatus::Active,
            'level' => ClientLevel::Scale,
            'ads_budget' => 8500.00,
            'account_manager_id' => $am->id,
            'start_date' => now()->subMonths(3),
            'renewal_date' => now()->addMonths(9),
            'has_sla' => true,
            'main_platform' => 'Meta Ads + TikTok Ads + Google Shopping',
            'main_objective' => 'Mantener CAC por debajo de $14 USD en colección de verano.',
            'notes' => 'Reuniones de revisión semanal los lunes a las 9:00 AM.',
        ]);

        $onboarding2 = Onboarding::create([
            'client_id' => $client2->id,
            'started_at' => now()->subMonths(3),
            'target_completion_date' => now()->subMonths(3)->addDays(7),
            'completed_at' => now()->subMonths(3)->addDays(6),
            'progress_percentage' => 100,
            'status' => 'completed',
        ]);

        foreach ($items1 as $index => $itemData) {
            OnboardingChecklistItem::create(array_merge($itemData, [
                'onboarding_id' => $onboarding2->id,
                'is_completed' => true,
                'order' => $index + 1,
            ]));
        }

        $client2->contacts()->create([
            'name' => 'Mateo Gómez',
            'role' => 'CEO & Founder',
            'email' => 'mateo@apparelco.com',
            'phone' => '+57 312 444 7711',
            'is_primary' => true,
        ]);

        // Client 3: Starter / Paused
        Client::create([
            'name' => 'Clínica Dental OdontoPlus',
            'status' => ClientStatus::Paused,
            'level' => ClientLevel::Starter,
            'ads_budget' => 1200.00,
            'account_manager_id' => $am->id,
            'start_date' => now()->subMonths(1),
            'has_sla' => false,
            'main_platform' => 'Google Search Ads + Meta Local',
            'main_objective' => 'Generación de leads para citas de diseño de sonrisa.',
            'notes' => 'Pausado temporalmente por remodelación de la sede principal.',
        ]);
    }
}
