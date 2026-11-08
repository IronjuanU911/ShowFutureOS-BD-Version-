<?php

namespace App\Http\Controllers;

use App\Enums\ClientLevel;
use App\Enums\ClientStatus;
use App\Models\Client;
use App\Models\Onboarding;
use App\Models\OnboardingChecklistItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ClientController extends Controller
{
    /**
     * Display a listing of clients with filters and stats.
     */
    public function index(Request $request)
    {
        $query = Client::with('accountManager', 'onboarding');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('main_platform', 'like', "%{$search}%")
                  ->orWhere('main_objective', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Level filter
        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        // AM filter
        if ($request->filled('account_manager_id')) {
            $query->where('account_manager_id', $request->input('account_manager_id'));
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Calculate Stats
        $stats = [
            'total' => Client::count(),
            'onboarding' => Client::where('status', ClientStatus::Onboarding)->count(),
            'active' => Client::where('status', ClientStatus::Active)->count(),
            'total_budget' => Client::where('status', ClientStatus::Active)->sum('ads_budget'),
        ];

        $accountManagers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['admin', 'account-manager']);
        })->get();

        return view('clients.index', compact('clients', 'stats', 'accountManagers'));
    }

    /**
     * Show form to create a new client.
     */
    public function create()
    {
        $accountManagers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['admin', 'account-manager']);
        })->get();

        return view('clients.create', compact('accountManagers'));
    }

    /**
     * Store a newly created client in storage and initialize onboarding.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required', new Enum(ClientStatus::class)],
            'level' => ['required', new Enum(ClientLevel::class)],
            'ads_budget' => 'nullable|numeric|min:0',
            'account_manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'renewal_date' => 'nullable|date|after_or_equal:start_date',
            'has_sla' => 'boolean',
            'main_platform' => 'nullable|string|max:255',
            'main_objective' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'access_platform' => 'nullable|string|max:100',
            'access_url' => 'nullable|url|max:500',
            'access_username' => 'nullable|string|max:255',
            'access_password' => 'nullable|string|max:255',
            'access_notes' => 'nullable|string',
        ]);

        $validated['has_sla'] = $request->has('has_sla');

        $client = Client::create($validated);

        // Auto-initialize Onboarding process with 7-day checklist
        $this->initializeOnboarding($client);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cliente creado exitosamente con la plantilla de Onboarding de 7 días.');
    }

    /**
     * Display client details with tabs.
     */
    public function show(Client $client)
    {
        $client->load([
            'accountManager',
            'contacts' => fn($q) => $q->orderBy('is_primary', 'desc'),
            'onboarding.items' => fn($q) => $q->orderBy('day_number')->orderBy('order'),
            'tickets' => fn($q) => $q->with('assignedTo')->orderBy('created_at', 'desc')->take(10),
        ]);

        $accountManagers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['admin', 'account-manager']);
        })->get();

        return view('clients.show', compact('client', 'accountManagers'));
    }

    /**
     * Show form to edit client.
     */
    public function edit(Client $client)
    {
        $accountManagers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['admin', 'account-manager']);
        })->get();

        return view('clients.edit', compact('client', 'accountManagers'));
    }

    /**
     * Update specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required', new Enum(ClientStatus::class)],
            'level' => ['required', new Enum(ClientLevel::class)],
            'ads_budget' => 'nullable|numeric|min:0',
            'account_manager_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'renewal_date' => 'nullable|date|after_or_equal:start_date',
            'has_sla' => 'boolean',
            'main_platform' => 'nullable|string|max:255',
            'main_objective' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'access_platform' => 'nullable|string|max:100',
            'access_url' => 'nullable|url|max:500',
            'access_username' => 'nullable|string|max:255',
            'access_password' => 'nullable|string|max:255',
            'access_notes' => 'nullable|string',
        ]);

        $validated['has_sla'] = $request->has('has_sla');

        if (empty($validated['access_password'])) {
            unset($validated['access_password']);
        }

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Información del cliente actualizada correctamente.');
    }

    /**
     * Remove specified client (Soft delete).
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'El cliente fue archivado exitosamente.');
    }

    /**
     * Helper to create 7-day onboarding checklist.
     */
    protected function initializeOnboarding(Client $client): Onboarding
    {
        $onboarding = Onboarding::create([
            'client_id' => $client->id,
            'started_at' => now(),
            'target_completion_date' => now()->addDays(7),
            'progress_percentage' => 0,
            'status' => 'in_progress',
        ]);

        $defaultItems = [
            ['day_number' => 1, 'title' => 'Reunión de Kickoff y Bienvenida', 'description' => 'Alineación de objetivos con el cliente y presentación del equipo asignado.'],
            ['day_number' => 2, 'title' => 'Solicitud y Verificación de Accesos', 'description' => 'Recopilar credenciales de Meta Business Manager, Google Ads, Web y Cuentas publicitarias.'],
            ['day_number' => 3, 'title' => 'Auditoría Inicial de Activos Digitales', 'description' => 'Análisis de campañas pasadas, histórico de métricas y pixel de seguimiento.'],
            ['day_number' => 4, 'title' => 'Definición de Estrategia & Estructura', 'description' => 'Elaboración del plan táctico de pauta y propuesta de ángulos de comunicación.'],
            ['day_number' => 5, 'title' => 'Configuración de Píxeles & Tracking (CAPI)', 'description' => 'Instalación de eventos de conversión y verificación en sitio web / CRM.'],
            ['day_number' => 6, 'title' => 'Redacción de Copies y Diseño de Creativos', 'description' => 'Elaboración del primer lote de anuncios creativos y textos promocionales.'],
            ['day_number' => 7, 'title' => 'Revisión Final QA y Lanzamiento de Campañas', 'description' => 'Verificación previa al lanzamiento con el cliente y encendido de campañas.'],
        ];

        foreach ($defaultItems as $index => $item) {
            OnboardingChecklistItem::create([
                'onboarding_id' => $onboarding->id,
                'day_number' => $item['day_number'],
                'title' => $item['title'],
                'description' => $item['description'],
                'is_completed' => false,
                'order' => $index + 1,
            ]);
        }

        return $onboarding;
    }
}
