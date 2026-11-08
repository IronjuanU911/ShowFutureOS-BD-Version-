<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    <i class="bi bi-pencil-square text-[#511560] mr-2"></i> Editar Cliente: {{ $client->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Actualiza la configuración, estado y asignaciones del cliente.</p>
            </div>
            <a href="{{ route('clients.show', $client) }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                <i class="bi bi-arrow-left mr-1"></i> Volver al detalle
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

                <form method="POST" action="{{ route('clients.update', $client) }}" class="pure-form pure-form-stacked space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nombre del cliente -->
                    <div>
                        <label for="name" class="text-sm font-bold text-gray-800 mb-1">Nombre de la Empresa / Cliente *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                    </div>

                    <!-- Estado y Nivel -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="status" class="text-sm font-bold text-gray-800 mb-1">Estado de la Cuenta *</label>
                            <select name="status" id="status" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\ClientStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $client->status->value) == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="level" class="text-sm font-bold text-gray-800 mb-1">Nivel del Cliente *</label>
                            <select name="level" id="level" required class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                @foreach(App\Enums\ClientLevel::cases() as $level)
                                    <option value="{{ $level->value }}" {{ old('level', $client->level->value) == $level->value ? 'selected' : '' }}>
                                        {{ $level->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- AM Asignado y Presupuesto Ads -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="account_manager_id" class="text-sm font-bold text-gray-800 mb-1">Account Manager Asignado</label>
                            <select name="account_manager_id" id="account_manager_id" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                                <option value="">Sin Asignar</option>
                                @foreach($accountManagers as $am)
                                    <option value="{{ $am->id }}" {{ old('account_manager_id', $client->account_manager_id) == $am->id ? 'selected' : '' }}>
                                        {{ $am->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="ads_budget" class="text-sm font-bold text-gray-800 mb-1">Presupuesto Ads Mensual ($ USD)</label>
                            <input type="number" step="0.01" min="0" name="ads_budget" id="ads_budget" value="{{ old('ads_budget', $client->ads_budget) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Plataforma y Objetivo principal -->
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="main_platform" class="text-sm font-bold text-gray-800 mb-1">Plataforma Principal</label>
                            <input type="text" name="main_platform" id="main_platform" value="{{ old('main_platform', $client->main_platform) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="main_objective" class="text-sm font-bold text-gray-800 mb-1">Objetivo de Negocio</label>
                            <input type="text" name="main_objective" id="main_objective" value="{{ old('main_objective', $client->main_objective) }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Fechas y SLA -->
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-6">
                            <label for="start_date" class="text-sm font-bold text-gray-800 mb-1">Fecha de Inicio de Contrato</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $client->start_date ? $client->start_date->format('Y-m-d') : '') }}" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                        </div>

                        <div class="col-12 col-md-6 pt-md-3">
                            <label for="has_sla" class="pure-checkbox text-sm font-semibold text-gray-800 d-flex align-items-center gap-2">
                                <input type="checkbox" name="has_sla" id="has_sla" value="1" {{ old('has_sla', $client->has_sla) ? 'checked' : '' }} class="w-4 h-4 text-[#511560] rounded">
                                ¿Cuenta con Garantía de SLA (Respuesta Prioritaria)?
                            </label>
                        </div>
                    </div>

                    <!-- Notas / Reglas de comunicación -->
                    <div>
                        <label for="notes" class="text-sm font-bold text-gray-800 mb-1">Reglas de Comunicación y Notas</label>
                        <textarea name="notes" id="notes" rows="3" class="pure-input-1 text-sm border-gray-200 rounded-xl">{{ old('notes', $client->notes) }}</textarea>
                    </div>

                    <!-- Bóveda de Accesos Integrada -->
                    <div class="pt-3 border-t border-gray-100">
                        <div class="mb-3">
                            <h4 class="text-sm font-bold text-[#2c0b34] d-flex align-items-center gap-1.5">
                                <i class="bi bi-key-fill text-[#511560]"></i> Bóveda de Accesos y Credenciales
                            </h4>
                            <p class="text-xs text-gray-400">Credenciales de la cuenta principal del cliente. Si no deseas cambiar la contraseña, déjala vacía.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="access_platform" class="text-xs font-bold text-gray-700 mb-1">Plataforma / Servicio</label>
                                <input type="text" name="access_platform" id="access_platform" value="{{ old('access_platform', $client->access_platform) }}" placeholder="Ej: Meta Business Manager" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="access_url" class="text-xs font-bold text-gray-700 mb-1">URL de Acceso</label>
                                <input type="url" name="access_url" id="access_url" value="{{ old('access_url', $client->access_url) }}" placeholder="https://business.facebook.com" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="access_username" class="text-xs font-bold text-gray-700 mb-1">Usuario / Email</label>
                                <input type="text" name="access_username" id="access_username" value="{{ old('access_username', $client->access_username) }}" placeholder="admin@cliente.com" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="access_password" class="text-xs font-bold text-gray-700 mb-1">Nueva Contraseña (Opcional)</label>
                                <input type="password" name="access_password" id="access_password" placeholder="•••••••• (dejar en blanco para no cambiar)" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            </div>
                            <div class="col-12">
                                <label for="access_notes" class="text-xs font-bold text-gray-700 mb-1">Notas del Acceso / ID de Cuenta</label>
                                <input type="text" name="access_notes" id="access_notes" value="{{ old('access_notes', $client->access_notes) }}" placeholder="Ej: ID BM: 1049283921" class="pure-input-1 text-sm border-gray-200 rounded-xl">
                            </div>
                        </div>
                    </div>

                    <!-- Submit & Archive Actions -->
                    <div class="d-flex align-items-center justify-content-between pt-4 border-t border-gray-100">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('clients.show', $client) }}" class="pure-button bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                                Cancelar
                            </a>
                            <button type="submit" class="pure-button pure-button-primary bg-[#511560] text-white rounded-xl text-sm font-semibold hover:bg-[#41104d] shadow-md transition">
                                <i class="bi bi-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
