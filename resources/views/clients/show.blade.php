<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-14 h-14 bg-gradient-to-br from-[#511560] to-[#7a2290] rounded-2xl d-flex align-items-center justify-center text-white text-2xl font-black shadow-md">
                    {{ strtoupper(substr($client->name, 0, 2)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            {{ $client->name }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($client->status === App\Enums\ClientStatus::Onboarding) bg-amber-100 text-amber-800 border border-amber-200
                            @elseif($client->status === App\Enums\ClientStatus::Active) bg-emerald-100 text-emerald-800 border border-emerald-200
                            @elseif($client->status === App\Enums\ClientStatus::Paused) bg-orange-100 text-orange-800 border border-orange-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            <i class="bi bi-circle-fill text-[8px] mr-1"></i> {{ $client->status->label() }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#faf4fb] text-[#511560] border border-[#d6a5e4]">
                            Nivel: {{ $client->level->label() }}
                        </span>
                        @if($client->has_sla)
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-extrabold rounded-lg d-flex align-items-center gap-1 border border-emerald-300">
                                <i class="bi bi-shield-check text-emerald-600"></i> SLA Garantizado
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1 d-flex align-items-center gap-3 flex-wrap">
                        <span><i class="bi bi-person-badge"></i> AM: <strong class="text-gray-800">{{ $client->accountManager ? $client->accountManager->name : 'Sin Asignar' }}</strong></span>
                        <span><i class="bi bi-cash-stack"></i> Pauta Ads: <strong class="text-[#511560]">${{ number_format($client->ads_budget ?? 0, 2) }} / mes</strong></span>
                        @if($client->start_date)
                            <span><i class="bi bi-calendar-event"></i> Inicio: <strong class="text-gray-800">{{ $client->start_date->format('d/m/Y') }}</strong></span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('clients.edit', $client) }}" class="pure-button bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-pencil-square mr-1"></i> Editar
                </a>
                <a href="{{ route('clients.index') }}" class="pure-button bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                    <i class="bi bi-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50/50 min-h-screen" x-data="{ activeTab: '{{ request('tab', 'overview') }}' }">
        <div class="container-fluid max-w-7xl space-y-4">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl shadow-sm d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
                        <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Tab Navigation Header -->
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 d-flex flex-wrap gap-2 mb-4">
                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill"></i> Resumen General
                </button>

                <button @click="activeTab = 'onboarding'" :class="activeTab === 'onboarding' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2 relative">
                    <i class="bi bi-card-checklist"></i> Checklist Onboarding (1-7 Días)
                    @if($client->onboarding)
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full {{ $client->onboarding->progress_percentage == 100 ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900' }}">
                            {{ $client->onboarding->progress_percentage }}%
                        </span>
                    @endif
                </button>

                <button @click="activeTab = 'contacts'" :class="activeTab === 'contacts' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-person-lines-fill"></i> Contactos Clave ({{ $client->contacts->count() }})
                </button>

                <button @click="activeTab = 'accesses'" :class="activeTab === 'accesses' ? 'bg-[#511560] text-white font-bold shadow-sm' : 'text-gray-600 hover:bg-gray-100 font-medium'" class="pure-button px-4 py-2 rounded-xl text-sm transition d-flex align-items-center gap-2">
                    <i class="bi bi-key-fill"></i> Bóveda de Accesos {{ $client->access_platform ? '(1)' : '(0)' }}
                </button>
            </div>

            <!-- TAB 1: OVERVIEW -->
            <div x-show="activeTab === 'overview'" class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-file-text-fill text-[#511560]"></i> Detalles de la Cuenta
                        </h3>

                        <div class="row g-4">
                            <div class="col-12 col-sm-6">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Plataforma Principal</p>
                                <p class="text-sm font-bold text-gray-800 mt-1"><i class="bi bi-globe mr-1 text-gray-400"></i> {{ $client->main_platform ?: 'No especificada' }}</p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Presupuesto Mensual de Pauta</p>
                                <p class="text-base font-extrabold text-[#511560] mt-1">${{ number_format($client->ads_budget ?? 0, 2) }} USD</p>
                            </div>

                            <div class="col-12">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Objetivo de Negocio</p>
                                <p class="text-sm font-medium text-gray-800 mt-1 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    {{ $client->main_objective ?: 'Sin objetivo registrado.' }}
                                </p>
                            </div>

                            <div class="col-12">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Reglas de Comunicación y Notas</p>
                                <p class="text-sm text-gray-700 mt-1 bg-gray-50 p-3 rounded-xl border border-gray-100 whitespace-pre-line">
                                    {{ $client->notes ?: 'Sin reglas o notas especiales cargadas.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 space-y-4">
                    <!-- Account Manager Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3"><i class="bi bi-person-circle mr-1"></i> Account Manager Asignado</h4>
                        @if($client->accountManager)
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-10 h-10 bg-[#f3e6f6] text-[#511560] rounded-full d-flex align-items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($client->accountManager->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $client->accountManager->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->accountManager->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No se ha asignado un AM a esta cuenta.</p>
                        @endif
                    </div>

                    <!-- SLA Status Indicator -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3"><i class="bi bi-clock-history mr-1"></i> Respuesta SLA</h4>
                        @if($client->has_sla)
                            <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-xl d-flex align-items-center gap-3">
                                <i class="bi bi-shield-check text-emerald-600 text-2xl"></i>
                                <div>
                                    <p class="text-xs font-bold text-emerald-900">SLA Garantizado Activo</p>
                                    <p class="text-xs text-emerald-700">Tiempo de respuesta prioritario < 24 horas.</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-gray-200 p-3 rounded-xl">
                                <p class="text-xs font-medium text-gray-600">SLA Estándar (Atención habitual).</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 2: ONBOARDING CHECKLIST -->
            <div x-show="activeTab === 'onboarding'" class="space-y-4">
                @if($client->onboarding)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-b border-gray-100 pb-4 mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 d-flex align-items-center gap-2">
                                    <i class="bi bi-card-checklist text-[#511560]"></i> Checklist de Onboarding Automatizado (7 Días)
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">Completa las tareas para avanzar el estado del cliente a Activo automáticamente.</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-end">
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Progreso Total</p>
                                    <p class="text-2xl font-black text-[#511560]">{{ $client->onboarding->progress_percentage }}%</p>
                                </div>
                                <div class="w-32 bg-gray-100 h-3 rounded-full overflow-hidden">
                                    <div class="bg-[#511560] h-3 rounded-full transition-all duration-500" style="width: {{ $client->onboarding->progress_percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Checklist Items list grouped by Day -->
                        <div class="space-y-4">
                            @foreach($client->onboarding->items->groupBy('day_number') as $dayNumber => $items)
                                <div class="border border-gray-100 rounded-xl overflow-hidden mb-3">
                                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-100 d-flex justify-content-between align-items-center">
                                        <span class="text-xs font-bold uppercase tracking-wider text-[#511560] bg-[#faf4fb] px-2.5 py-1 rounded-lg">
                                            Día {{ $dayNumber }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-medium">
                                            {{ $items->where('is_completed', true)->count() }} / {{ $items->count() }} completadas
                                        </span>
                                    </div>
                                    <div class="divide-y divide-gray-100">
                                        @foreach($items as $item)
                                            <div class="p-3 d-flex items-start gap-3 hover:bg-gray-50/50 transition">
                                                <form method="POST" action="{{ route('clients.onboarding.toggle', ['client' => $client->id, 'item' => $item->id]) }}">
                                                    @csrf
                                                    <input type="checkbox" onchange="this.form.submit()" {{ $item->is_completed ? 'checked' : '' }} class="w-5 h-5 mt-0.5 text-[#511560] rounded border-gray-300 focus:ring-[#511560] cursor-pointer">
                                                </form>
                                                <div class="flex-grow-1">
                                                    <p class="text-sm font-bold {{ $item->is_completed ? 'line-through text-gray-400' : 'text-gray-900' }}">
                                                        {{ $item->title }}
                                                    </p>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->description }}</p>
                                                    @endif
                                                </div>
                                                @if($item->is_completed && $item->completed_at)
                                                    <span class="text-xs text-emerald-600 font-semibold d-flex align-items-center gap-1 bg-emerald-50 px-2 py-1 rounded-lg">
                                                        <i class="bi bi-check2-circle"></i> Completado {{ $item->completed_at->format('d/m H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @else
                    <div class="bg-white p-5 text-center rounded-2xl border border-gray-100">
                        <p class="text-gray-500 text-sm">No se ha inicializado el proceso de onboarding para esta cuenta.</p>
                    </div>
                @endif
            </div>

            <!-- TAB 3: CONTACTS -->
            <div x-show="activeTab === 'contacts'" class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="d-flex justify-content-between align-items-center border-b border-gray-100 pb-4 mb-4" x-data="{ showAddContact: false }">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900"><i class="bi bi-person-lines-fill text-[#511560] mr-1"></i> Contactos Clave del Cliente</h3>
                            <p class="text-xs text-gray-500 mt-1">Personas de contacto para comunicaciones operativas y estratégicas.</p>
                        </div>
                        <button @click="showAddContact = !showAddContact" class="pure-button pure-button-primary bg-[#511560] text-white text-xs font-semibold rounded-xl hover:bg-[#41104d] transition">
                            <i class="bi bi-plus-lg mr-1"></i> Agregar Contacto
                        </button>

                        <!-- Add Contact Form Modal -->
                        <div x-show="showAddContact" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm d-flex align-items-center justify-content-center p-4" x-cloak>
                            <div @click.away="showAddContact = false" class="bg-white rounded-2xl shadow-xl max-w-md w-100 p-6 space-y-4">
                                <h4 class="text-base font-bold text-gray-900">Nuevo Contacto</h4>
                                <form method="POST" action="{{ route('clients.contacts.store', $client) }}" class="pure-form pure-form-stacked space-y-3">
                                    @csrf
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Nombre Completo *</label>
                                        <input type="text" name="name" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Rol / Cargo</label>
                                        <input type="text" name="role" placeholder="Ej: Gerente de Marketing" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Correo Electrónico</label>
                                        <input type="email" name="email" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Teléfono / WhatsApp</label>
                                        <input type="text" name="phone" placeholder="+57 300 000 0000" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="is_primary" id="is_primary" value="1" class="rounded border-gray-300 text-[#511560]">
                                        <label for="is_primary" class="text-xs font-medium text-gray-700">Contacto Principal</label>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 pt-3 border-t">
                                        <button type="button" @click="showAddContact = false" class="pure-button bg-gray-100 text-gray-600 text-xs rounded-xl font-semibold">Cancelar</button>
                                        <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white text-xs rounded-xl font-semibold">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Contacts Grid (Bootstrap Grid: col-12 col-md-6) -->
                    <div class="row g-3">
                        @forelse($client->contacts as $contact)
                            <div class="col-12 col-md-6">
                                <div class="p-4 border border-gray-100 rounded-xl d-flex justify-content-between align-items-start bg-gray-50/50">
                                    <div class="space-y-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="text-sm font-bold text-gray-900">{{ $contact->name }}</p>
                                            @if($contact->is_primary)
                                                <span class="px-2 py-0.5 bg-[#f3e6f6] text-[#380f42] text-[10px] font-bold rounded-md">Principal</span>
                                            @endif
                                        </div>
                                        @if($contact->role)
                                            <p class="text-xs text-gray-500 font-medium">{{ $contact->role }}</p>
                                        @endif
                                        @if($contact->email)
                                            <p class="text-xs text-gray-600 d-flex align-items-center gap-1">
                                                <i class="bi bi-envelope text-gray-400"></i>
                                                <a href="mailto:{{ $contact->email }}" class="hover:underline">{{ $contact->email }}</a>
                                            </p>
                                        @endif
                                        @if($contact->phone)
                                            <p class="text-xs text-gray-600 d-flex align-items-center gap-1">
                                                <i class="bi bi-whatsapp text-emerald-500"></i>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}" target="_blank" class="hover:underline text-emerald-600 font-semibold">{{ $contact->phone }}</a>
                                            </p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('clients.contacts.destroy', ['client' => $client->id, 'contact' => $contact->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Eliminar contacto?');" class="text-gray-400 hover:text-rose-600 transition pure-button bg-transparent border-0 p-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-xs text-gray-400 italic">No se han registrado contactos.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TAB 4: ACCESSES VAULT (INTEGRADO EN CLIENT) -->
            <div x-show="activeTab === 'accesses'" class="space-y-4" x-data="{ showAddAccess: false }">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="d-flex justify-content-between align-items-center border-b border-gray-100 pb-4 mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900"><i class="bi bi-key-fill text-[#511560] mr-1"></i> Bóveda de Accesos y Credenciales</h3>
                            <p class="text-xs text-gray-500 mt-1">Registro seguro de accesos a Meta Ads, BM, Google, Tiendas y Drive integrado en la ficha del cliente.</p>
                        </div>
                        <button @click="showAddAccess = true" class="pure-button pure-button-primary bg-[#511560] text-white text-xs font-semibold rounded-xl hover:bg-[#41104d] transition">
                            <i class="bi {{ $client->access_platform ? 'bi-pencil-square' : 'bi-plus-lg' }} mr-1"></i>
                            {{ $client->access_platform ? 'Editar Credenciales' : 'Registrar Acceso' }}
                        </button>

                        <!-- Add/Edit Access Form Modal -->
                        <div x-show="showAddAccess" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm d-flex align-items-center justify-content-center p-4" x-cloak>
                            <div @click.away="showAddAccess = false" class="bg-white rounded-2xl shadow-xl max-w-md w-100 p-6 space-y-4">
                                <h4 class="text-base font-bold text-gray-900">
                                    {{ $client->access_platform ? 'Actualizar Credencial de Acceso' : 'Nueva Credencial de Acceso' }}
                                </h4>
                                <form method="POST" action="{{ route('clients.accesses.store', $client) }}" class="pure-form pure-form-stacked space-y-3">
                                    @csrf
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Plataforma / Servicio *</label>
                                        <input type="text" name="platform" value="{{ old('platform', $client->access_platform) }}" required placeholder="Ej: Meta Business Manager / Shopify" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">URL de Acceso</label>
                                        <input type="url" name="url" value="{{ old('url', $client->access_url) }}" placeholder="https://business.facebook.com" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Usuario / Email</label>
                                        <input type="text" name="username" value="{{ old('username', $client->access_username) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Contraseña / Token</label>
                                        <input type="text" name="password" placeholder="{{ $client->access_password ? '•••••••• (dejar vacío para conservar actual)' : 'Ingresa contraseña o token' }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-700 mb-1">Notas / ID de Cuenta</label>
                                        <textarea name="notes" rows="2" class="pure-input-1 text-sm border-gray-200 rounded-xl">{{ old('notes', $client->access_notes) }}</textarea>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 pt-3 border-t">
                                        <button type="button" @click="showAddAccess = false" class="pure-button bg-gray-100 text-gray-600 text-xs rounded-xl font-semibold">Cancelar</button>
                                        <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white text-xs rounded-xl font-semibold">Guardar Credenciales</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Access Information Card -->
                    @if($client->access_platform)
                        <div class="p-5 border border-gray-200/80 rounded-2xl bg-gray-50/50 space-y-3 max-w-2xl" x-data="{ showPass: false }">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-sm font-bold text-[#2c0b34] bg-[#faf4fb] px-3 py-1 rounded-lg border border-[#e8ceef]">
                                    <i class="bi bi-shield-lock-fill mr-1 text-[#511560]"></i> {{ $client->access_platform }}
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" @click="showAddAccess = true" class="text-gray-500 hover:text-[#511560] text-xs font-bold pure-button bg-white border border-gray-200 rounded-lg px-2.5 py-1">
                                        <i class="bi bi-pencil mr-1"></i> Editar
                                    </button>
                                    <form method="POST" action="{{ route('clients.accesses.destroy', $client) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Eliminar la credencial de acceso de este cliente?');" class="text-rose-500 hover:text-rose-700 text-xs font-bold pure-button bg-white border border-rose-200 rounded-lg px-2.5 py-1">
                                            <i class="bi bi-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($client->access_url)
                                <p class="text-xs text-[#511560] font-medium truncate">
                                    <a href="{{ $client->access_url }}" target="_blank" class="hover:underline d-inline-flex align-items-center gap-1">
                                        {{ $client->access_url }} <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                    </a>
                                </p>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs">
                                <div class="p-2.5 bg-white rounded-xl border border-gray-100">
                                    <span class="text-gray-400 text-[10px] uppercase font-bold block">Usuario / Email</span>
                                    <strong class="text-gray-900 font-mono">{{ $client->access_username ?: 'Sin especificar' }}</strong>
                                </div>
                                <div class="p-2.5 bg-white rounded-xl border border-gray-100">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="text-gray-400 text-[10px] uppercase font-bold">Contraseña</span>
                                        @if($client->access_password)
                                            <button type="button" @click="showPass = !showPass" class="text-[#511560] text-[10px] font-bold underline pure-button bg-transparent border-0 p-0">
                                                <span x-text="showPass ? 'Ocultar' : 'Ver'"></span>
                                            </button>
                                        @endif
                                    </div>
                                    <strong class="text-gray-900 font-mono">
                                        @if($client->access_password)
                                            <span x-text="showPass ? '{{ $client->access_password }}' : '••••••••'"></span>
                                        @else
                                            <span class="text-gray-400 font-sans italic">No registrada</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            @if($client->access_notes)
                                <div class="bg-white p-3 rounded-xl border border-gray-100 text-xs text-gray-600">
                                    <span class="text-gray-400 text-[10px] uppercase font-bold block mb-1">Notas / ID de Cuenta</span>
                                    <p class="italic">{{ $client->access_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                            <i class="bi bi-key text-3xl text-gray-300 mb-2 d-block"></i>
                            <p class="text-xs text-gray-500 font-medium">No se han registrado credenciales de acceso para este cliente.</p>
                            <button type="button" @click="showAddAccess = true" class="mt-3 pure-button pure-button-primary bg-[#511560] text-white text-xs font-semibold rounded-xl hover:bg-[#41104d] transition">
                                <i class="bi bi-plus-lg mr-1"></i> Registrar Acceso Ahora
                            </button>
                        </div>
                    @endif
                </div>
            </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
