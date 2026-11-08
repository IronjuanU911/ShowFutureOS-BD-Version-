<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2c0b34] to-[#511560] text-white flex items-center justify-center shadow-md">
                        <i class="bi bi-speedometer2 text-xl"></i>
                    </span>
                    <div>
                        <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                            Panel Operativo & Clientes 360°
                        </h2>
                        <p class="text-xs text-gray-500 font-medium">
                            Visualización ejecutiva de cuentas, presupuestos, SLA, onboarding de 7 días y entregables de producción.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción Global -->
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-200 hover:border-[#511560] hover:text-[#511560] text-gray-700 text-xs font-bold rounded-xl shadow-2xs hover:shadow transition">
                    <i class="bi bi-person-plus-fill text-[#511560]"></i>
                    Nuevo Cliente
                </a>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-gray-200 hover:border-[#511560] hover:text-[#511560] text-gray-700 text-xs font-bold rounded-xl shadow-2xs hover:shadow transition">
                    <i class="bi bi-plus-circle-fill text-[#511560]"></i>
                    Nuevo Ticket
                </a>
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#511560] hover:bg-[#41104d] text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition">
                    <i class="bi bi-kanban-fill"></i>
                    Ver Tablero Kanban
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. BLOQUE DE TARJETAS KPI GLOBALES -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Clientes -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Cuentas</span>
                        <span class="w-8 h-8 rounded-lg bg-[#faf4fb] text-[#511560] flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-buildings"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalClientsCount }}</p>
                    <div class="mt-2 flex items-center gap-1.5 text-[11px] text-gray-500">
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold">{{ $activeClientsCount }} Activos</span>
                        <span class="px-2 py-0.5 rounded-full bg-[#faf4fb] text-[#511560] font-bold">{{ $onboardingClientsCount }} Onboarding</span>
                    </div>
                </div>

                <!-- Pauta Publicitaria Total -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pauta Gestionada</span>
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-cash-stack"></i>
                        </span>
                    </div>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-2">${{ number_format($totalAdsBudget, 2) }}</p>
                    <p class="mt-2 text-[11px] text-gray-500 font-medium">Presupuesto mensual Ads</p>
                </div>

                <!-- Onboarding Salud -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Salud Onboarding</span>
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-check2-circle"></i>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <p class="text-3xl font-extrabold text-[#511560]">{{ $avgOnboardingProgress }}%</p>
                        <span class="text-[11px] text-gray-400">promedio</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-[#511560] h-1.5 rounded-full" style="width: {{ $avgOnboardingProgress }}%"></div>
                    </div>
                </div>

                <!-- SLA Garantizado -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">SLA Garantizado</span>
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-shield-check"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $slaGuaranteedCount }}</p>
                    <p class="mt-2 text-[11px] text-gray-500 font-medium">Respuesta &lt;= 4h hábiles</p>
                </div>

                <!-- Flujo Operativo Kanban -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Tareas en Curso</span>
                        <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                            <i class="bi bi-kanban"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $inProgressTicketsCount + $qaTicketsCount + $backlogTicketsCount }}</p>
                    <div class="mt-2 flex items-center gap-1.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold">{{ $qaTicketsCount }} en QA</span>
                        @if($highPriorityTicketsCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold">{{ $highPriorityTicketsCount }} alta prioridad</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. BARRA DE ATENCIÓN URGENTE (SI EXISTEN TICKETS CRÍTICOS) -->
            @if($urgentTickets->isNotEmpty())
                <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-4 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                <i class="bi bi-bell-fill"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider">Atención Operativa Inmediata</h4>
                                <p class="text-xs text-amber-800">Hay {{ $urgentTickets->count() }} tareas prioritarias que requieren revisión o tienen fecha de entrega cercana.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                            @foreach($urgentTickets as $ut)
                                <a href="{{ route('tickets.show', $ut) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-amber-300 rounded-lg text-xs font-bold text-gray-800 hover:border-[#511560] hover:text-[#511560] transition shadow-2xs shrink-0">
                                    <span class="text-[#511560]">{{ $ut->ticket_number }}</span>
                                    <span class="truncate max-w-[140px]">{{ $ut->title }}</span>
                                    @if($ut->deadline)
                                        <span class="text-[10px] text-gray-400">({{ $ut->deadline->format('d/m') }})</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. BARRA DE BÚSQUEDA Y FILTRADO AVANZADO DE CLIENTES -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    <!-- Búsqueda -->
                    <div class="lg:col-span-4">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Buscar Cuenta</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Nombre de cliente, objetivo, plataforma..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560] transition">
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Estado</label>
                        <select name="status" class="w-full py-2 px-3 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los Estados</option>
                            @foreach(\App\Enums\ClientStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nivel -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nivel</label>
                        <select name="level" class="w-full py-2 px-3 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los Niveles</option>
                            @foreach(\App\Enums\ClientLevel::cases() as $lvl)
                                <option value="{{ $lvl->value }}" {{ request('level') === $lvl->value ? 'selected' : '' }}>
                                    {{ $lvl->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Account Manager -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Account Manager</label>
                        <select name="account_manager_id" class="w-full py-2 px-3 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los AM</option>
                            @foreach($accountManagers as $am)
                                <option value="{{ $am->id }}" {{ request('account_manager_id') == $am->id ? 'selected' : '' }}>
                                    {{ $am->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones Filtrar y Limpiar -->
                    <div class="lg:col-span-2 flex items-end gap-2 pt-4 lg:pt-0">
                        <button type="submit" class="flex-1 py-2 px-3 bg-[#511560] hover:bg-[#41104d] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i>
                            Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'level', 'account_manager_id', 'platform']))
                            <a href="{{ route('dashboard') }}" class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition" title="Limpiar Filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 4. DIRECTORIO MASTER DE CLIENTES 360° -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 flex items-center gap-2">
                            <span>Directorio de Cuentas Activas</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">{{ $clients->total() }}</span>
                        </h3>
                        <p class="text-xs text-gray-500">Haz clic en cualquier tarjeta para abrir la ficha 360° con contactos, bóveda de claves y tickets.</p>
                    </div>
                    <a href="{{ route('clients.index') }}" class="text-xs font-bold text-[#511560] hover:underline flex items-center gap-1">
                        Ver vista tabla CRM <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @if($clients->isEmpty())
                    <div class="bg-white p-12 text-center rounded-2xl border border-gray-100 shadow-sm">
                        <div class="w-14 h-14 rounded-full bg-[#faf4fb] text-[#511560] flex items-center justify-center text-2xl mx-auto mb-3">
                            <i class="bi bi-search"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-base">No se encontraron clientes</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Prueba ajustando los filtros de búsqueda o registra una nueva cuenta en el sistema.</p>
                        <div class="mt-4">
                            <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#511560] text-white text-xs font-bold rounded-xl shadow">
                                <i class="bi bi-plus-lg"></i> Registrar Cliente
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($clients as $client)
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                                <!-- Encabezado de la Tarjeta -->
                                <div class="p-5 border-b border-gray-50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            @if($client->logo)
                                                <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-2xs">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#2c0b34] to-[#511560] text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                                    {{ substr($client->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('clients.show', $client) }}" class="font-bold text-base text-gray-900 group-hover:text-[#511560] transition leading-snug block">
                                                    {{ $client->name }}
                                                </a>
                                                <p class="text-[11px] text-gray-400 font-medium">
                                                    <i class="bi bi-globe2 mr-1"></i>{{ $client->main_platform ?: 'Multi-plataforma' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Insignias de Estado y Nivel -->
                                        <div class="flex flex-col items-end gap-1">
                                            <!-- Estado -->
                                            @if($client->status === \App\Enums\ClientStatus::Active)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Activo
                                                </span>
                                            @elseif($client->status === \App\Enums\ClientStatus::Onboarding)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#faf4fb] text-[#511560] border border-[#e8ceef]">
                                                    Onboarding
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                                    {{ $client->status->label() }}
                                                </span>
                                            @endif

                                            <!-- Nivel -->
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider
                                                @if($client->level === \App\Enums\ClientLevel::Scale) bg-purple-100 text-[#511560]
                                                @elseif($client->level === \App\Enums\ClientLevel::Growth) bg-blue-50 text-blue-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                                {{ $client->level->label() }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Objetivo Principal -->
                                    @if($client->main_objective)
                                        <p class="text-xs text-gray-600 mt-3 line-clamp-1 italic bg-gray-50/70 px-2.5 py-1 rounded-lg">
                                            <i class="bi bi-bullseye mr-1 text-[#511560]"></i>"{{ $client->main_objective }}"
                                        </p>
                                    @endif
                                </div>

                                <!-- Cuerpo con Métricas de la Cuenta -->
                                <div class="p-5 space-y-4">
                                    <!-- Presupuesto y SLA -->
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="p-2.5 bg-gray-50/70 rounded-xl">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Pauta Ads</span>
                                            <span class="text-sm font-extrabold text-emerald-600">
                                                ${{ number_format($client->ads_budget ?? 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="p-2.5 bg-gray-50/70 rounded-xl">
                                            <span class="text-[10px] uppercase font-bold text-gray-400 block">SLA Garantizado</span>
                                            @if($client->has_sla)
                                                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1 mt-0.5">
                                                    <i class="bi bi-check-circle-fill text-emerald-500"></i> &lt;= 4h
                                                </span>
                                            @else
                                                <span class="text-xs font-medium text-gray-400 mt-0.5 block">Estándar</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Barra de Progreso de Onboarding -->
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-gray-700 flex items-center gap-1.5">
                                                <i class="bi bi-card-checklist text-[#511560]"></i> Onboarding 7 Días
                                            </span>
                                            <span class="font-extrabold text-[#511560]">
                                                {{ $client->onboarding->progress_percentage ?? 0 }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-[#511560] to-[#7a2290] h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ $client->onboarding->progress_percentage ?? 0 }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Contadores Rápidos: Contactos, Bóveda, Tickets -->
                                    <div class="flex items-center justify-between pt-1 text-xs text-gray-500 border-t border-gray-100">
                                        <div class="flex items-center gap-1" title="Contactos registrados">
                                            <i class="bi bi-person-lines-fill text-[#511560]"></i>
                                            <span class="font-bold text-gray-700">{{ $client->contacts_count ?? $client->contacts->count() }}</span>
                                            <span class="text-[10px]">contactos</span>
                                        </div>

                                        <div class="flex items-center gap-1" title="Accesos y credenciales en bóveda">
                                            <i class="bi bi-key-fill text-amber-500"></i>
                                            <span class="font-bold text-gray-700">{{ $client->access_platform ? 1 : 0 }}</span>
                                            <span class="text-[10px]">accesos</span>
                                        </div>

                                        <div class="flex items-center gap-1" title="Tickets registrados">
                                            <i class="bi bi-ticket-perforated-fill text-indigo-500"></i>
                                            <span class="font-bold text-gray-700">{{ $client->tickets_count ?? $client->tickets->count() }}</span>
                                            <span class="text-[10px]">tickets</span>
                                        </div>
                                    </div>

                                    <!-- Account Manager Asignado -->
                                    <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-xs">
                                        <span class="text-[11px] text-gray-400 font-medium">Account Manager:</span>
                                        @if($client->accountManager)
                                            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                                <span class="w-5 h-5 rounded-full bg-[#511560] text-white flex items-center justify-center font-bold text-[9px]">
                                                    {{ substr($client->accountManager->name, 0, 1) }}
                                                </span>
                                                {{ $client->accountManager->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-[11px]">No asignado</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Botonera de Acción Rápida -->
                                <div class="p-3 bg-gray-50/70 border-t border-gray-100 flex items-center gap-1.5">
                                    <a href="{{ route('clients.show', $client) }}" 
                                       class="flex-1 text-center py-2 px-3 bg-white hover:bg-[#511560] hover:text-white text-gray-800 text-xs font-bold rounded-xl border border-gray-200 hover:border-[#511560] shadow-2xs transition">
                                        Ficha 360°
                                    </a>
                                    <a href="{{ route('tickets.create', ['client_id' => $client->id]) }}" 
                                       class="py-2 px-3 bg-white hover:bg-emerald-600 hover:text-white text-gray-700 text-xs font-bold rounded-xl border border-gray-200 transition" 
                                       title="Crear ticket para este cliente">
                                        <i class="bi bi-plus-lg"></i> Ticket
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       class="p-2 text-gray-400 hover:text-[#511560] hover:bg-white rounded-xl transition" 
                                       title="Editar Cuenta">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación de Clientes -->
                    <div class="pt-5 border-t border-gray-100">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>

            <!-- 5. DESGLOSE ESTADÍSTICO DE OPERACIONES -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-2">
                <!-- Distribución por Nivel de Cuenta -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-sm text-gray-900 mb-3 flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-[#511560]"></i>
                        Segmentación por Nivel
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                                <span>Scale</span>
                                <span>{{ $levelCounts['scale'] }} cuentas</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-[#511560] h-2 rounded-full" style="width: {{ $totalClientsCount > 0 ? ($levelCounts['scale'] / $totalClientsCount) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                                <span>Growth</span>
                                <span>{{ $levelCounts['growth'] }} cuentas</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalClientsCount > 0 ? ($levelCounts['growth'] / $totalClientsCount) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                                <span>Starter</span>
                                <span>{{ $levelCounts['starter'] }} cuentas</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-gray-400 h-2 rounded-full" style="width: {{ $totalClientsCount > 0 ? ($levelCounts['starter'] / $totalClientsCount) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado del Embudo Kanban -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-sm text-gray-900 mb-3 flex items-center gap-2">
                        <i class="bi bi-kanban-fill text-[#511560]"></i>
                        Estado de Tareas en Kanban
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-gray-400 block">Backlog</span>
                            <span class="text-xl font-extrabold text-gray-700">{{ $backlogTicketsCount }}</span>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-blue-600 block">En Proceso</span>
                            <span class="text-xl font-extrabold text-blue-700">{{ $inProgressTicketsCount }}</span>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-amber-600 block">En QA</span>
                            <span class="text-xl font-extrabold text-amber-700">{{ $qaTicketsCount }}</span>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl">
                            <span class="text-[10px] uppercase font-bold text-emerald-600 block">Entregados</span>
                            <span class="text-xl font-extrabold text-emerald-700">{{ $deliveredTicketsCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plataformas Conectadas -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-sm text-gray-900 mb-3 flex items-center gap-2">
                            <i class="bi bi-cpu-fill text-[#511560]"></i>
                            Asistente Gemini & Plataformas
                        </h4>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            El módulo de Inteligencia Artificial Gemini analiza los requerimientos de cada ticket cruzando el objetivo del cliente con las mejores prácticas en <strong class="text-gray-800">Meta Ads, Google, TikTok y Shopify</strong>.
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-[#511560] flex items-center gap-1.5">
                            <i class="bi bi-stars"></i> Gemini 3.5 Flash Conectado
                        </span>
                        <a href="{{ route('tickets.index') }}" class="text-xs text-gray-500 hover:text-[#511560] font-bold">
                            Probar en Kanban &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
