<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    <i class="bi bi-ticket-perforated-fill text-[#511560] mr-2"></i> Crear Nuevo Ticket
                </h2>
                <p class="text-sm text-gray-500 mt-1">Registra un requerimiento estructurado para el equipo de producción y marketing.</p>
            </div>
            <a href="{{ route('tickets.index') }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                <i class="bi bi-arrow-left mr-1"></i> Volver al Tablero
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50/50 min-h-screen">
        <div class="container max-w-3xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl">
                        <p class="text-sm font-bold text-rose-800">Por favor corrige los errores:</p>
                        <ul class="mt-2 list-disc list-inside text-xs text-rose-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tickets.store') }}" class="pure-form pure-form-stacked space-y-4">
                    @csrf

                    <!-- Cliente -->
                    <div>
                        <label for="client_id" class="text-sm font-bold text-gray-800 mb-1">Cliente / Cuenta Asociada *</label>
                        <select name="client_id" id="client_id" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            <option value="">Selecciona un Cliente</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} (AM: {{ $client->accountManager ? $client->accountManager->name : 'Sin AM' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Título del Ticket -->
                    <div>
                        <label for="title" class="text-sm font-bold text-gray-800 mb-1">Título del Ticket / Requerimiento *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Ej: Video Reel Promocional Colección Verano" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>

                    <!-- Tipo y Prioridad -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="type" class="text-sm font-bold text-gray-800 mb-1">Tipo de Especialidad *</label>
                            <select name="type" id="type" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\TicketType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="priority" class="text-sm font-bold text-gray-800 mb-1">Prioridad *</label>
                            <select name="priority" id="priority" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\TicketPriority::cases() as $pri)
                                    <option value="{{ $pri->value }}" {{ old('priority', App\Enums\TicketPriority::Medium->value) == $pri->value ? 'selected' : '' }}>
                                        {{ $pri->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Asignado y Fecha Límite -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="assigned_to" class="text-sm font-bold text-gray-800 mb-1">Responsable Asignado</label>
                            <select name="assigned_to" id="assigned_to" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                <option value="">Sin Asignar</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="text-sm font-bold text-gray-800 mb-1">Fecha Límite de Entrega</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline', date('Y-m-d', strtotime('+3 days'))) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Formato y Especificaciones -->
                    <div>
                        <label for="format" class="text-sm font-bold text-gray-800 mb-1">Formato / Dimensiones</label>
                        <input type="text" name="format" id="format" value="{{ old('format') }}" placeholder="Ej: Video MP4 9:16 (Reels) / Carousel 1080x1080" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>

                    <!-- Descripción / Brief -->
                    <div>
                        <label for="description" class="text-sm font-bold text-gray-800 mb-1">Descripción Detallada / Brief Creativo</label>
                        <textarea name="description" id="description" rows="4" placeholder="Indica los detalles del gancho, CTA, propuesta visual o instrucciones de pauta..." class="pure-input-1 text-sm border-gray-200 rounded-xl">{{ old('description') }}</textarea>
                    </div>

                    <!-- Aprobación de Cliente -->
                    <div class="pt-2">
                        <label for="requires_client_approval" class="pure-checkbox text-sm font-semibold text-gray-800 d-flex align-items-center gap-2">
                            <input type="checkbox" name="requires_client_approval" id="requires_client_approval" value="1" {{ old('requires_client_approval') ? 'checked' : '' }} class="w-4 h-4 text-[#511560] rounded">
                            ¿Requiere Aprobación Directa del Cliente antes del Lanzamiento?
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('tickets.index') }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white rounded-xl text-sm font-semibold hover:bg-[#41104d] shadow-md transition">
                            <i class="bi bi-check-lg mr-1"></i> Crear Ticket
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
