<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="p-2 bg-white border border-gray-200 text-gray-600 hover:text-[#511560] rounded-xl shadow-2xs transition">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                        Registrar Nuevo Empleado
                    </h2>
                    <p class="text-xs text-gray-500 font-medium">
                        Crea una cuenta interna para el equipo y define sus roles y responsabilidades.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/40">
                    <h3 class="font-bold text-base text-gray-900">Datos Personales y de Acceso</h3>
                    <p class="text-xs text-gray-500">Ingresa la información básica para el acceso al sistema ShowFutureOS.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <!-- 1. Datos Personales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nombre Completo -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Nombre Completo <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="Ej: Sofía Valenzuela">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Correo Electrónico (Login) <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="sofia@showfuture.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Teléfono / WhatsApp -->
                        <div>
                            <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Teléfono / WhatsApp (Opcional)
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="+57 300 123 4567">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <!-- Estado Inicial -->
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-[#511560] focus:ring-[#511560] w-4 h-4">
                                <span class="text-xs font-bold text-gray-700">Cuenta Activa (Permitir inicio de sesión inmediato)</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Contraseña -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Contraseña <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="Mínimo 8 caracteres">
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                                Confirmar Contraseña <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:ring-[#511560] focus:border-[#511560] transition"
                                   placeholder="Repite la contraseña">
                        </div>
                    </div>

                    <!-- 3. Asignación de Roles & Permisos -->
                    <div class="pt-6 border-t border-gray-100 space-y-4">
                        <div>
                            <h4 class="font-bold text-sm text-gray-900">Asignación de Roles & Permisos</h4>
                            <p class="text-xs text-gray-500">Selecciona al menos un rol para este empleado. Puedes seleccionar múltiples roles si cumple funciones combinadas.</p>
                            <x-input-error :messages="$errors->get('roles')" class="mt-1" />
                        </div>

                        @if(!$isSuperAdmin)
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2">
                                <i class="bi bi-shield-exclamation text-base text-amber-600"></i>
                                <span>Como Administrador estándar, puedes asignar roles operativos pero <strong>no tienes facultades para crear o nombrar a otros Administradores</strong> (función exclusiva del Super Administrador).</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                            @foreach($roles as $role)
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border transition cursor-pointer
                                    {{ in_array($role->id, old('roles', [])) ? 'border-[#511560] bg-[#faf4fb]' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                           class="mt-1 rounded border-gray-300 text-[#511560] focus:ring-[#511560] w-4 h-4">
                                    <div class="text-xs">
                                        <div class="flex items-center gap-2 font-bold text-gray-900">
                                            <span>{{ $role->name }}</span>
                                            @if($role->slug === 'admin')
                                                <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-[#511560] text-white">Privilegio Admin</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">{{ $role->description }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 hover:bg-gray-100 rounded-xl text-xs font-bold transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-[#511560] hover:bg-[#41104d] text-white rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                            <i class="bi bi-check-lg text-sm"></i>
                            <span>Registrar Empleado</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
