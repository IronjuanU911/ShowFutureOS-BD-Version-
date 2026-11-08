<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2c0b34] to-[#511560] text-white flex items-center justify-center shadow-md">
                    <i class="bi bi-shield-lock-fill text-xl"></i>
                </span>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                        Gestión de Empleados, Roles & Permisos
                    </h2>
                    <p class="text-xs text-gray-500 font-medium">
                        Panel de control exclusivo para administración del equipo, asignación de responsabilidades y jerarquía de accesos.
                    </p>
                </div>
            </div>

            <!-- Botón Registrar Empleado -->
            <div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#511560] hover:bg-[#41104d] text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition">
                    <i class="bi bi-person-plus-fill text-sm"></i>
                    <span>Registrar Nuevo Empleado</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Mensajes de Estado / Notificaciones -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                        <span class="text-xs font-bold text-emerald-900">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg"></i>
                        <span class="text-xs font-bold text-rose-900">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. TARJETAS KPI DE EQUIPO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Equipo -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block">Total Empleados</span>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $metrics['total'] }}</p>
                    <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-2 font-medium">
                        <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full">{{ $metrics['active'] }} Activos</span>
                        @if($metrics['inactive'] > 0)
                            <span class="text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded-full">{{ $metrics['inactive'] }} Inactivos</span>
                        @endif
                    </div>
                </div>

                <!-- Administradores -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block">Administradores</span>
                        <span class="w-6 h-6 rounded-md bg-[#faf4fb] text-[#511560] flex items-center justify-center text-xs">
                            <i class="bi bi-shield-shaded"></i>
                        </span>
                    </div>
                    <p class="text-3xl font-extrabold text-[#511560] mt-2">{{ $metrics['admins'] }}</p>
                    <p class="mt-2 text-[11px] text-gray-500 font-medium">
                        {{ $metrics['super_admins'] }} Super Admin / {{ $metrics['admins'] - $metrics['super_admins'] }} Admin
                    </p>
                </div>

                <!-- Account Managers -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block">Account Managers</span>
                    <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $metrics['account_managers'] }}</p>
                    <p class="mt-2 text-[11px] text-gray-500 font-medium">Liderazgo de Cuentas & QA</p>
                </div>

                <!-- Especialistas y Producción -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400 block">Producción & Ads</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $metrics['specialists'] }}</p>
                    <p class="mt-2 text-[11px] text-gray-500 font-medium">Creativos, Copy, Media Buyers</p>
                </div>

                <!-- Jerarquía de Acceso -->
                <div class="bg-gradient-to-br from-[#2c0b34] to-[#511560] p-5 rounded-2xl shadow-sm text-white flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-white/70 block">Tu Nivel de Privilegio</span>
                        <div class="mt-2 flex items-center gap-2">
                            <i class="bi {{ auth()->user()->isSuperAdmin() ? 'bi-shield-check text-amber-300' : 'bi-shield text-white' }} text-xl"></i>
                            <span class="font-extrabold text-sm">
                                {{ auth()->user()->isSuperAdmin() ? 'Super Administrador' : 'Administrador' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-[10px] text-white/80 mt-2">
                        @if(auth()->user()->isSuperAdmin())
                            Facultado para crear, promover y revocar Administradores.
                        @else
                            Gestión del equipo general activa.
                        @endif
                    </p>
                </div>
            </div>

            <!-- AVISO DE JERARQUÍA DE SEGURIDAD -->
            <div class="p-4 bg-purple-50/70 border border-[#e8ceef] rounded-2xl flex items-start gap-3">
                <span class="w-7 h-7 rounded-lg bg-[#511560] text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                    <i class="bi bi-info-circle-fill"></i>
                </span>
                <div class="text-xs text-[#380f42] leading-relaxed">
                    <strong class="font-bold">Regla de Seguridad & Gobernanza:</strong>
                    El <span class="font-extrabold text-[#511560]">Super Administrador</span> es la única cuenta facultada para crear o promover usuarios al rol de <span class="font-bold">Administrador</span> y para revocar dicha jerarquía. Los administradores estándar pueden registrar y gestionar roles operativos de los empleados (Account Manager, Creativo, Copywriter, Ads Manager, etc.), pero tienen restringido crear o remover otros administradores.
                </div>
            </div>

            <!-- 2. BARRA DE FILTROS Y BÚSQUEDA -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                    <!-- Búsqueda -->
                    <div class="lg:col-span-5">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Buscar Empleado</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Nombre, correo electrónico, teléfono..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560] transition">
                        </div>
                    </div>

                    <!-- Filtro por Rol -->
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Filtrar por Rol</label>
                        <select name="role" class="w-full py-2 px-3 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos los Roles</option>
                            <option value="super-admin" {{ request('role') === 'super-admin' ? 'selected' : '' }}>Super Administrador</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                            @foreach($roles as $r)
                                @if(!in_array($r->slug, ['super-admin', 'admin']))
                                    <option value="{{ $r->slug }}" {{ request('role') === $r->slug ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Estado</label>
                        <select name="status" class="w-full py-2 px-3 text-xs border-gray-200 rounded-xl focus:ring-[#511560] focus:border-[#511560]">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="lg:col-span-2 flex items-end gap-2 pt-4 lg:pt-0">
                        <button type="submit" class="flex-1 py-2 px-3 bg-[#511560] hover:bg-[#41104d] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i>
                            Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition" title="Limpiar Filtros">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 3. TABLA DE EMPLEADOS Y ASIGNACIÓN DE ROLES -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">Listado Oficial de Empleados</h3>
                        <p class="text-xs text-gray-500">Haz clic en "Gestionar Permisos" para cambiar roles o promover/revocar accesos.</p>
                    </div>
                    <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-full">
                        {{ $users->total() }} registrados
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100 text-[10px] font-extrabold uppercase tracking-wider text-gray-400">
                                <th class="py-3 px-5">Empleado / Identidad</th>
                                <th class="py-3 px-4">Roles & Privilegios</th>
                                <th class="py-3 px-4">Cuentas / Tareas</th>
                                <th class="py-3 px-4">Estado</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $emp)
                                <tr class="hover:bg-gray-50/60 transition {{ !$emp->is_active ? 'opacity-60 bg-gray-50/30' : '' }}">
                                    <!-- Identidad -->
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-2xs shrink-0
                                                @if($emp->isSuperAdmin()) bg-gradient-to-br from-[#2c0b34] to-[#511560] text-amber-300 ring-2 ring-amber-400/50
                                                @elseif($emp->isAdmin()) bg-[#511560] text-white
                                                @else bg-gray-100 text-gray-700 @endif">
                                                {{ substr($emp->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-1.5 font-bold text-gray-900 text-sm">
                                                    <span>{{ $emp->name }}</span>
                                                    @if($emp->id === auth()->id())
                                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-gray-100 text-gray-600 font-bold">Tú</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-gray-500 font-mono flex items-center gap-2 mt-0.5">
                                                    <span><i class="bi bi-envelope mr-1"></i>{{ $emp->email }}</span>
                                                    @if($emp->phone)
                                                        <span>&bull;</span>
                                                        <span><i class="bi bi-telephone mr-1"></i>{{ $emp->phone }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Roles y Permisos -->
                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap gap-1.5 max-w-xs">
                                            @forelse($emp->roles as $r)
                                                @if($r->slug === 'super-admin')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#2c0b34] text-amber-300 border border-amber-400/40 shadow-2xs">
                                                        <i class="bi bi-crown-fill text-[9px]"></i> Super Admin
                                                    </span>
                                                @elseif($r->slug === 'admin')
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#faf4fb] text-[#511560] border border-[#e8ceef]">
                                                        <i class="bi bi-shield-fill text-[9px]"></i> Administrador
                                                    </span>
                                                @elseif($r->slug === 'account-manager')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                        Account Manager
                                                    </span>
                                                @elseif($r->slug === 'ads-manager')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Ads Manager
                                                    </span>
                                                @elseif($r->slug === 'creative')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                                        Creativo
                                                    </span>
                                                @elseif($r->slug === 'copywriter')
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        Copywriter
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                        {{ $r->name }}
                                                    </span>
                                                @endif
                                            @empty
                                                <span class="text-gray-400 italic text-[11px]">Sin roles asignados</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Cuentas / Tareas -->
                                    <td class="py-4 px-4">
                                        <div class="text-xs space-y-0.5 text-gray-600 font-medium">
                                            <div><i class="bi bi-buildings mr-1 text-[#511560]"></i>{{ $emp->managed_clients_count }} clientes como AM</div>
                                            <div><i class="bi bi-ticket-perforated mr-1 text-indigo-500"></i>{{ $emp->assigned_tickets_count }} tickets asignados</div>
                                        </div>
                                    </td>

                                    <!-- Estado -->
                                    <td class="py-4 px-4">
                                        @if($emp->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Acciones -->
                                    <td class="py-4 px-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <!-- Gestionar / Editar -->
                                            @if($emp->isSuperAdmin() && auth()->id() !== $emp->id)
                                                <span class="text-[10px] text-gray-400 italic px-2">Protegido</span>
                                            @elseif($emp->isAdmin() && !auth()->user()->isSuperAdmin() && auth()->id() !== $emp->id)
                                                <span class="text-[10px] text-gray-400 italic px-2">Admin Protegido</span>
                                            @else
                                                <a href="{{ route('admin.users.edit', $emp) }}" 
                                                   class="px-2.5 py-1 bg-white hover:bg-[#faf4fb] hover:text-[#511560] border border-gray-200 rounded-lg text-xs font-bold text-gray-700 shadow-2xs transition inline-flex items-center gap-1">
                                                    <i class="bi bi-pencil-square text-[#511560]"></i> Permisos
                                                </a>

                                                <!-- Toggle Activo / Inactivo -->
                                                @if(!$emp->isSuperAdmin() && auth()->id() !== $emp->id)
                                                    <form method="POST" action="{{ route('admin.users.toggle', $emp) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                title="{{ $emp->is_active ? 'Desactivar Empleado' : 'Activar Empleado' }}"
                                                                class="p-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs text-gray-600 transition">
                                                            <i class="bi {{ $emp->is_active ? 'bi-pause-circle text-amber-600' : 'bi-play-circle text-emerald-600' }}"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Eliminar (Solo Super Admin) -->
                                                @if(auth()->user()->isSuperAdmin() && !$emp->isSuperAdmin() && auth()->id() !== $emp->id)
                                                    <form method="POST" action="{{ route('admin.users.destroy', $emp) }}" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a {{ $emp->name }}? Esta acción no se puede deshacer.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 rounded-lg border border-gray-200 hover:bg-rose-50 text-xs text-rose-600 transition" title="Eliminar registro">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400">
                                        No se encontraron empleados con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
