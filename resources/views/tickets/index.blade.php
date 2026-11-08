<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="bg-[#511560] text-white p-2 rounded-lg shadow-sm">
                        <i class="bi bi-kanban-fill text-xl"></i>
                    </span>
                    Tablero de Tickets (Kanban)
                </h2>
                <p class="text-sm text-gray-500 mt-1">Gestión de flujo de trabajo para Creativos, Ads Managers, Copywriters y Editores.</p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}" class="pure-button pure-button-primary bg-[#511560] hover:bg-[#41104d] text-white text-sm font-semibold rounded-xl shadow-md transition gap-2 inline-flex items-center py-2 px-4">
                    <i class="bi bi-plus-lg"></i>
                    Nuevo Ticket
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50/50 min-h-screen">
        <div class="container-fluid max-w-7xl">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
                        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl shadow-sm d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-xl"></i>
                        <span class="text-sm font-medium text-rose-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filters Bar (Pure.css form + Bootstrap Grid) -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="{{ route('tickets.index') }}" class="pure-form row g-3 align-items-center">
                    <div class="col-12 col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código, título..." class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>
                    <div class="col-12 col-md-3">
                        <select name="client_id" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            <option value="">Todos los Clientes</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <select name="type" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            <option value="">Todos los Tipos</option>
                            @foreach(App\Enums\TicketType::cases() as $type)
                                <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <select name="priority" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            <option value="">Prioridad</option>
                            @foreach(App\Enums\TicketPriority::cases() as $pri)
                                <option value="{{ $pri->value }}" {{ request('priority') == $pri->value ? 'selected' : '' }}>{{ $pri->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="pure-button pure-button-primary bg-gray-900 text-white w-100 text-sm font-semibold rounded-xl py-2">
                            <i class="bi bi-funnel-fill"></i> Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'client_id', 'type', 'priority', 'assigned_to']))
                            <a href="{{ route('tickets.index') }}" class="pure-button text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-medium">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- KANBAN BOARD (4 COLUMNS) -->
            <div class="row g-4 items-start">

                <!-- COLUMN 1: BACKLOG -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="bg-gray-100/80 p-4 rounded-2xl border border-gray-200 min-h-[500px]">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-bold text-sm text-gray-700 uppercase tracking-wider d-flex align-items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-slate-400"></span> Backlog ({{ $kanban['backlog']->count() }})
                            </h3>
                            <span class="text-xs font-semibold text-gray-400 bg-white px-2 py-0.5 rounded-md border border-gray-200">Por iniciar</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($kanban['backlog'] as $ticket)
                                @include('tickets.partials.kanban-card', ['ticket' => $ticket, 'nextStatus' => 'in_progress', 'nextLabel' => 'Iniciar Tarea'])
                            @empty
                                <div class="p-4 bg-white/60 rounded-xl text-center text-xs text-gray-400 italic border border-dashed border-gray-200">
                                    No hay tickets en Backlog
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: IN PROGRESS -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-100 min-h-[500px]">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-bold text-sm text-blue-900 uppercase tracking-wider d-flex align-items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span> En Proceso ({{ $kanban['in_progress']->count() }})
                            </h3>
                            <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md border border-blue-200">Ejecución</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($kanban['in_progress'] as $ticket)
                                @include('tickets.partials.kanban-card', ['ticket' => $ticket, 'nextStatus' => 'qa', 'nextLabel' => 'Enviar a QA'])
                            @empty
                                <div class="p-4 bg-white/60 rounded-xl text-center text-xs text-blue-400 italic border border-dashed border-blue-200">
                                    Sin tareas en proceso
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: QA REVISION -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-100 min-h-[500px]">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-bold text-sm text-amber-900 uppercase tracking-wider d-flex align-items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span> Revisión QA ({{ $kanban['qa']->count() }})
                            </h3>
                            <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md border border-amber-200">Revisión AM</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($kanban['qa'] as $ticket)
                                @include('tickets.partials.kanban-card', ['ticket' => $ticket, 'showQaAction' => true])
                            @empty
                                <div class="p-4 bg-white/60 rounded-xl text-center text-xs text-amber-400 italic border border-dashed border-amber-200">
                                    Sin tickets en QA
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- COLUMN 4: DELIVERED -->
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-100 min-h-[500px]">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="font-bold text-sm text-emerald-900 uppercase tracking-wider d-flex align-items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Entregados ({{ $kanban['delivered']->count() }})
                            </h3>
                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200">Finalizado</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($kanban['delivered'] as $ticket)
                                @include('tickets.partials.kanban-card', ['ticket' => $ticket, 'isDelivered' => true])
                            @empty
                                <div class="p-4 bg-white/60 rounded-xl text-center text-xs text-emerald-400 italic border border-dashed border-emerald-200">
                                    Aún no hay entregas
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
