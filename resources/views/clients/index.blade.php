<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="bg-[#511560] text-white p-2 rounded-lg shadow-sm">
                        <i class="bi bi-people-fill text-xl"></i>
                    </span>
                    Gestión de Clientes (CRM)
                </h2>
                <p class="text-sm text-gray-500 mt-1">Control centralizado de cuentas, estados de onboarding, presupuestos y SLAs.</p>
            </div>
            <div>
                <a href="{{ route('clients.create') }}" class="pure-button pure-button-primary bg-[#511560] hover:bg-[#41104d] text-white text-sm font-semibold rounded-xl shadow-md transition gap-2 inline-flex items-center py-2 px-4">
                    <i class="bi bi-plus-lg"></i>
                    Nuevo Cliente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50/50 min-h-screen">
        <div class="container-fluid max-w-7xl">
            <div class="row g-4 mb-4">

                @if(session('success'))
                    <div class="col-12">
                        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
                                <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- KPI Cards (Bootstrap Grid: col-12 col-sm-6 col-lg-3) -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Clientes</p>
                            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl d-flex align-items-center justify-content-center text-2xl">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">En Onboarding (1-7 días)</p>
                            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['onboarding'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl d-flex align-items-center justify-content-center text-2xl">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Cuentas Activas</p>
                            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['active'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl d-flex align-items-center justify-content-center text-2xl">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-purple-600">Pauta Ads Total (Activas)</p>
                            <p class="text-3xl font-extrabold text-purple-600 mt-1">${{ number_format($stats['total_budget'], 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl d-flex align-items-center justify-content-center text-2xl">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Bar with Pure.css form & Bootstrap Grid -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-4">
                <form method="GET" action="{{ route('clients.index') }}" class="pure-form row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, plataforma..." class="pure-input-1 text-sm border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                    </div>
                    <div class="col-12 col-md-3">
                        <select name="status" class="pure-input-1 text-sm border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los Estados</option>
                            @foreach(App\Enums\ClientStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <select name="level" class="pure-input-1 text-sm border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los Niveles</option>
                            @foreach(App\Enums\ClientLevel::cases() as $level)
                                <option value="{{ $level->value }}" {{ request('level') == $level->value ? 'selected' : '' }}>{{ $level->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="pure-button pure-button-primary bg-gray-900 text-white w-100 text-sm font-semibold rounded-xl py-2">
                            <i class="bi bi-funnel-fill mr-1"></i> Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'level', 'account_manager_id']))
                            <a href="{{ route('clients.index') }}" class="pure-button text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-medium">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Clients Grid (Bootstrap Grid: col-12 col-md-6 col-lg-4) -->
            <div class="row g-4">
                @forelse($clients as $client)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-200 overflow-hidden d-flex flex-column justify-content-between h-100">
                            <div class="p-6">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                            @if($client->status === App\Enums\ClientStatus::Onboarding) bg-amber-100 text-amber-800
                                            @elseif($client->status === App\Enums\ClientStatus::Active) bg-emerald-100 text-emerald-800
                                            @elseif($client->status === App\Enums\ClientStatus::Paused) bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $client->status->label() }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ml-1
                                            @if($client->level === App\Enums\ClientLevel::Starter) bg-blue-50 text-blue-700
                                            @elseif($client->level === App\Enums\ClientLevel::Growth) bg-purple-50 text-purple-700
                                            @else bg-[#faf4fb] text-[#511560] font-bold @endif">
                                            {{ $client->level->label() }}
                                        </span>
                                    </div>
                                    @if($client->has_sla)
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg d-flex align-items-center gap-1 border border-emerald-200" title="SLA Activo">
                                            <i class="bi bi-shield-check"></i> SLA
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-xl font-bold text-gray-900 mt-3 hover:text-[#511560] transition">
                                    <a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a>
                                </h3>

                                @if($client->main_platform)
                                    <p class="text-xs text-gray-500 font-medium mt-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-globe text-gray-400"></i>
                                        {{ $client->main_platform }}
                                    </p>
                                @endif

                                <!-- Onboarding progress bar if onboarding -->
                                @if($client->status === App\Enums\ClientStatus::Onboarding && $client->onboarding)
                                    <div class="mt-4 bg-amber-50 p-3 rounded-xl border border-amber-100">
                                        <div class="d-flex justify-content-between text-xs font-semibold text-amber-900 mb-1">
                                            <span>Onboarding 7 Días</span>
                                            <span>{{ $client->onboarding->progress_percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-amber-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-2 rounded-full transition-all duration-300" style="width: {{ $client->onboarding->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4 space-y-2 border-t border-gray-100 pt-3">
                                    <div class="d-flex justify-content-between text-xs">
                                        <span class="text-gray-500"><i class="bi bi-person mr-1"></i> Account Manager:</span>
                                        <span class="font-semibold text-gray-800">
                                            {{ $client->accountManager ? $client->accountManager->name : 'Sin Asignar' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between text-xs">
                                        <span class="text-gray-500"><i class="bi bi-graph-up-arrow mr-1"></i> Presupuesto Ads:</span>
                                        <span class="font-semibold text-gray-900">
                                            ${{ number_format($client->ads_budget ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 d-flex justify-content-between align-items-center">
                                <a href="{{ route('clients.show', $client) }}" class="text-xs font-bold text-[#511560] hover:text-[#380f42] d-flex align-items-center gap-1">
                                    Ver Detalle Completo
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="text-xs text-gray-400 hover:text-gray-600 font-medium">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 bg-white p-5 text-center rounded-2xl border border-gray-100 shadow-sm">
                        <i class="bi bi-people text-gray-300 text-5xl mb-3 d-block"></i>
                        <h3 class="text-lg font-bold text-gray-900">No se encontraron clientes</h3>
                        <p class="text-sm text-gray-500 mt-1">Registra tu primer cliente para comenzar el onboarding automatizado.</p>
                        <a href="{{ route('clients.create') }}" class="mt-3 pure-button pure-button-primary bg-[#511560] text-white rounded-xl text-sm font-semibold shadow hover:bg-[#41104d] transition inline-flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i> Crear Cliente
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $clients->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
