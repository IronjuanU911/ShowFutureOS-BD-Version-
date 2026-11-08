<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="p-2 bg-white border border-gray-200 text-gray-600 hover:text-[#511560] rounded-xl shadow-2xs transition">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                        Editar Empleado: {{ $user->name }}
                    </h2>
                    <p class="text-xs text-gray-500 font-medium">
                        Actualiza la información personal, estado de la cuenta y roles asignados en ShowFutureOS.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($user->isSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-purple-950 text-amber-300 border border-purple-800 shadow-xs">
                        <i class="bi bi-stars"></i> Super Administrador
                    </span>
                @elseif($user->isAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#511560] text-white shadow-xs">
                        <i class="bi bi-shield-check"></i> Administrador
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                        <i class="bi bi-person"></i> Operativo
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/40 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">Configuración de Usuario & Accesos</h3>
                        <p class="text-xs text-gray-500">Los cambios de permisos entrarán en vigor en la próxima acción del usuario.</p>
                    </div>
                    <span class="text-[11px] font-mono px-2 py-0.5 bg-gray-100 text-gray-500 rounded border border-gray-200">
                        ID: #{{ $user->id }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- 1. Datos Personales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nombre Completo -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Nombre Completo <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="Ej: Sofía Valenzuela">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Correo Electrónico (Login) <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="sofia@showfuture.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Teléfono / WhatsApp -->
                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Teléfono / WhatsApp
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="+57 300 123 4567">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <!-- Estado de la Cuenta -->
                        <div class="flex items-center pt-6">
                            @if($user->isSuperAdmin())
                                <input type="hidden" name="is_active" value="1">
                                <div class="flex items-center gap-2 text-xs text-gray-400 font-semibold">
                                    <i class="bi bi-shield-lock-fill text-purple-700 text-base"></i>
                                    <span>Cuenta de Super Administrador (Siempre Activa)</span>
                                </div>
                            @else
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-[#511560] focus:ring-[#511560] w-4 h-4">
                                    <span class="text-xs font-bold text-gray-700">Cuenta Activa (Permitir acceso a ShowFutureOS)</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Actualizar Contraseña (Opcional) -->
                    <div class="pt-4 border-t border-gray-100">
                        <div class="mb-3">
                            <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700">Cambiar Contraseña (Opcional)</h4>
                            <p class="text-[11px] text-gray-400">Deja ambos campos vacíos si no deseas modificar la contraseña actual del empleado.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="block text-xs font-medium text-gray-600 mb-1">
                                    Nueva Contraseña
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                       placeholder="Mínimo 8 caracteres si se va a cambiar">
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-600 mb-1">
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                       placeholder="Repite la nueva contraseña">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Asignación de Roles & Permisos -->
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div>
                            <h4 class="font-bold text-sm text-gray-900">Gestión de Roles & Permisos</h4>
                            <p class="text-xs text-gray-500">Selecciona o revoca las responsabilidades para este empleado.</p>
                            <x-input-error :messages="$errors->get('roles')" class="mt-1" />
                        </div>

                        <!-- Jerarquía & Políticas Informativas -->
                        @if($user->isSuperAdmin())
                            <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-xs text-purple-900 flex items-start gap-3">
                                <i class="bi bi-stars text-lg text-purple-700 mt-0.5"></i>
                                <div>
                                    <span class="font-bold">Cuenta Principal Super Administrador</span>
                                    <p class="text-[11px] text-purple-700 mt-0.5">El rol principal de Super Administrador está protegido para garantizar la gobernanza del sistema y no puede ser removido.</p>
                                </div>
                            </div>
                        @elseif($isSuperAdmin)
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 flex items-start gap-3">
                                <i class="bi bi-shield-check text-lg text-blue-600 mt-0.5"></i>
                                <div>
                                    <span class="font-bold">Privilegios de Super Administrador</span>
                                    <p class="text-[11px] text-blue-700 mt-0.5">Tienes la facultad exclusiva de <strong>otorgar o revocar</strong> el permiso de <span class="font-bold text-[#511560]">Administrador</span> para este usuario marcando o desmarcando la casilla correspondiente.</p>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-3">
                                <i class="bi bi-shield-exclamation text-lg text-amber-600 mt-0.5"></i>
                                <div>
                                    <span class="font-bold">Facultades de Administrador Estándar</span>
                                    <p class="text-[11px] text-amber-700 mt-0.5">Puedes gestionar los roles operativos del equipo (Cuentas, Ads, Creativo, Copy, etc.), pero solo el Super Administrador puede otorgar o revocar permisos de Administrador.</p>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                            @foreach($roles as $role)
                                @php
                                    $isChecked = in_array($role->id, old('roles', $userRoleIds));
                                    $isAdminCheckbox = ($role->slug === 'admin');
                                @endphp
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border transition cursor-pointer
                                    {{ $isChecked ? 'border-[#511560] bg-[#faf4fb]' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           {{ $isChecked ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-[#511560] focus:ring-[#511560] w-4 h-4">
                                    <div class="text-xs">
                                        <div class="flex items-center gap-2 font-bold text-gray-900">
                                            <span>{{ $role->name }}</span>
                                            @if($isAdminCheckbox)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#511560] text-white">Privilegio Admin</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $role->description }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            @if(!$user->isSuperAdmin() && auth()->user()->id !== $user->id)
                                <button type="button" 
                                        onclick="if(confirm('¿Confirmas el cambio de estado de este empleado?')) { document.getElementById('toggle-status-form').submit(); }"
                                        class="text-xs font-bold {{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                    <i class="bi {{ $user->is_active ? 'bi-person-slash' : 'bi-person-check' }}"></i>
                                    {{ $user->is_active ? 'Desactivar Cuenta' : 'Activar Cuenta' }}
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 rounded-xl text-xs font-bold transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-5 py-2.5 bg-[#511560] hover:bg-[#41104d] text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <i class="bi bi-save2 text-sm"></i>
                                <span>Guardar Cambios</span>
                            </button>
                        </div>
                    </div>
                </form>

                @if(!$user->isSuperAdmin() && auth()->user()->id !== $user->id)
                    <form id="toggle-status-form" method="POST" action="{{ route('admin.users.toggle', $user) }}" class="hidden">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
