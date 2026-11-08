<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    <i class="bi bi-pencil-square text-[#511560] mr-2"></i> Editar Ticket {{ $ticket->ticket_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Actualiza las especificaciones, asignación o fechas del ticket.</p>
            </div>
            <a href="{{ route('tickets.show', $ticket) }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                <i class="bi bi-arrow-left mr-1"></i> Volver al Detalle
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

                <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="pure-form pure-form-stacked space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Cliente -->
                    <div>
                        <label for="client_id" class="text-sm font-bold text-gray-800 mb-1">Cliente / Cuenta Asociada *</label>
                        <select name="client_id" id="client_id" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $ticket->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Título -->
                    <div>
                        <label for="title" class="text-sm font-bold text-gray-800 mb-1">Título del Ticket *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $ticket->title) }}" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>

                    <!-- Tipo y Prioridad -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="type" class="text-sm font-bold text-gray-800 mb-1">Tipo de Especialidad *</label>
                            <select name="type" id="type" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\TicketType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $ticket->type ? $ticket->type->value : '') == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="priority" class="text-sm font-bold text-gray-800 mb-1">Prioridad *</label>
                            <select name="priority" id="priority" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\TicketPriority::cases() as $pri)
                                    <option value="{{ $pri->value }}" {{ old('priority', $ticket->priority ? $ticket->priority->value : '') == $pri->value ? 'selected' : '' }}>
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
                                    <option value="{{ $member->id }}" {{ old('assigned_to', $ticket->assigned_to) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="text-sm font-bold text-gray-800 mb-1">Fecha Límite de Entrega</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $ticket->deadline ? $ticket->deadline->format('Y-m-d') : '') }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Formato y Especificaciones -->
                    <div>
                        <label for="format" class="text-sm font-bold text-gray-800 mb-1">Formato / Dimensiones</label>
                        <input type="text" name="format" id="format" value="{{ old('format', $ticket->format) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="text-sm font-bold text-gray-800 mb-1">Descripción / Brief</label>
                        <textarea name="description" id="description" rows="4" class="pure-input-1 text-sm border-gray-200 rounded-xl">{{ old('description', $ticket->description) }}</textarea>
                    </div>

                    <!-- Checkbox Aprobación -->
                    <div class="pt-2">
                        <label for="requires_client_approval" class="pure-checkbox text-sm font-semibold text-gray-800 d-flex align-items-center gap-2">
                            <input type="checkbox" name="requires_client_approval" id="requires_client_approval" value="1" {{ old('requires_client_approval', $ticket->requires_client_approval) ? 'checked' : '' }} class="w-4 h-4 text-[#511560] rounded">
                            ¿Requiere Aprobación Directa del Cliente?
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex align-items-center justify-content-between pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('¿Archivar este ticket?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                Archivar Ticket
                            </button>
                        </form>

                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('tickets.show', $ticket) }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white rounded-xl text-sm font-semibold hover:bg-[#41104d] shadow-md transition">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
