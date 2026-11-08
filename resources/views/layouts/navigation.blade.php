<nav x-data="{ open: false, quickOpen: false, clientsDropdown: false, ticketsDropdown: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm backdrop-blur-md bg-white/95">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo Show Future -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::check() ? route('dashboard') : url('/') }}" class="flex items-center gap-2 group transition">
                        <x-application-logo variant="purple" class="h-8 w-auto transition-transform group-hover:scale-105" />
                    </a>
                </div>

                @auth
                    <!-- Navigation Links for Authenticated Users -->
                    <div class="hidden space-x-1 lg:space-x-2 md:-my-px md:ms-8 md:flex items-center">
                        <!-- 1. Dashboard 360 -->
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <i class="bi bi-speedometer2 text-base mr-1.5 {{ request()->routeIs('dashboard') ? 'text-[#511560]' : 'text-gray-400 group-hover:text-[#511560]' }}"></i>
                            <span>{{ __('Dashboard') }}</span>
                        </x-nav-link>

                        <!-- 2. Menú Clientes (CRM) -->
                        <div class="relative" @click.outside="clientsDropdown = false">
                            <button @click="clientsDropdown = !clientsDropdown; ticketsDropdown = false" 
                                    class="inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition duration-150 ease-in-out {{ request()->routeIs('clients.*') ? 'border-[#511560] text-[#511560]' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-[#C6C6C6]' }}">
                                <i class="bi bi-people-fill text-base mr-1.5 {{ request()->routeIs('clients.*') ? 'text-[#511560]' : 'text-gray-400' }}"></i>
                                <span>{{ __('Clientes') }}</span>
                                <i class="bi bi-chevron-down ms-1 text-xs transition-transform duration-200" :class="{ 'rotate-180': clientsDropdown }"></i>
                            </button>

                            <div x-show="clientsDropdown" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-64 rounded-2xl shadow-xl bg-white ring-1 ring-black/5 divide-y divide-gray-100 z-50 py-1.5 border border-gray-100"
                                 x-cloak>
                                <div class="py-1">
                                    <a href="{{ route('clients.index') }}" class="group flex items-center px-4 py-2.5 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-medium transition">
                                        <div class="w-8 h-8 rounded-lg bg-[#faf4fb] text-[#511560] flex items-center justify-center mr-3 group-hover:bg-[#511560] group-hover:text-white transition">
                                            <i class="bi bi-person-lines-fill text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold">Directorio de Clientes</p>
                                            <p class="text-[10px] text-gray-400">Ver todas las cuentas, niveles y SLA</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('clients.create') }}" class="group flex items-center px-4 py-2.5 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-medium transition">
                                        <div class="w-8 h-8 rounded-lg bg-[#faf4fb] text-[#511560] flex items-center justify-center mr-3 group-hover:bg-[#511560] group-hover:text-white transition">
                                            <i class="bi bi-person-plus-fill text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold">Registrar Nuevo Cliente</p>
                                            <p class="text-[10px] text-gray-400">Iniciar checklist de 7 días</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Menú Tickets (Kanban & QA) -->
                        <div class="relative" @click.outside="ticketsDropdown = false">
                            <button @click="ticketsDropdown = !ticketsDropdown; clientsDropdown = false" 
                                    class="inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition duration-150 ease-in-out {{ request()->routeIs('tickets.*') ? 'border-[#511560] text-[#511560]' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-[#C6C6C6]' }}">
                                <i class="bi bi-kanban-fill text-base mr-1.5 {{ request()->routeIs('tickets.*') ? 'text-[#511560]' : 'text-gray-400' }}"></i>
                                <span>{{ __('Tickets') }}</span>
                                <i class="bi bi-chevron-down ms-1 text-xs transition-transform duration-200" :class="{ 'rotate-180': ticketsDropdown }"></i>
                            </button>

                            <div x-show="ticketsDropdown" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-2 w-64 rounded-2xl shadow-xl bg-white ring-1 ring-black/5 divide-y divide-gray-100 z-50 py-1.5 border border-gray-100"
                                 x-cloak>
                                <div class="py-1">
                                    <a href="{{ route('tickets.index') }}" class="group flex items-center px-4 py-2.5 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-medium transition">
                                        <div class="w-8 h-8 rounded-lg bg-[#faf4fb] text-[#511560] flex items-center justify-center mr-3 group-hover:bg-[#511560] group-hover:text-white transition">
                                            <i class="bi bi-grid-3x3-gap-fill text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold">Tablero Kanban</p>
                                            <p class="text-[10px] text-gray-400">Flujo de producción por estados</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('tickets.create') }}" class="group flex items-center px-4 py-2.5 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-medium transition">
                                        <div class="w-8 h-8 rounded-lg bg-[#faf4fb] text-[#511560] flex items-center justify-center mr-3 group-hover:bg-[#511560] group-hover:text-white transition">
                                            <i class="bi bi-plus-circle-fill text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold">Crear Nuevo Ticket</p>
                                            <p class="text-[10px] text-gray-400">Creativo, Ads, Copy, Comercial</p>
                                        </div>
                                    </a>
                                    <a href="{{ route('tickets.index', ['status' => 'qa']) }}" class="group flex items-center px-4 py-2.5 text-xs text-gray-700 hover:bg-amber-50 hover:text-amber-800 font-medium transition">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mr-3 group-hover:bg-amber-500 group-hover:text-white transition">
                                            <i class="bi bi-shield-check text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-amber-900">Control de Calidad QA</p>
                                            <p class="text-[10px] text-amber-700/80">Revisión de 3 bloques y aprobación</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Gestión de Empleados & Permisos (Solo Administradores) -->
                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <i class="bi bi-people-gear text-base mr-1.5 {{ request()->routeIs('admin.users.*') ? 'text-[#511560]' : 'text-gray-400 group-hover:text-[#511560]' }}"></i>
                                <span>{{ __('Equipo & Roles') }}</span>
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Right Side: Actions & User Dropdown or Guest Login -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Botón de Acción Rápida "+ Nuevo" -->
                    <div class="relative hidden sm:block" x-data="{ newDropdown: false }" @click.outside="newDropdown = false">
                        <button @click="newDropdown = !newDropdown" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#511560] hover:bg-[#41104d] active:bg-[#2c0b34] text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition">
                            <i class="bi bi-plus-lg text-sm"></i>
                            <span>Nuevo</span>
                            <i class="bi bi-chevron-down text-[10px] ms-0.5"></i>
                        </button>

                        <div x-show="newDropdown" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 rounded-2xl shadow-xl bg-white border border-gray-100 py-1.5 z-50 divide-y divide-gray-100"
                             x-cloak>
                            <div class="py-1">
                                <a href="{{ route('tickets.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-semibold transition">
                                    <i class="bi bi-ticket-perforated-fill text-[#511560]"></i>
                                    <span>Nuevo Ticket de Trabajo</span>
                                </a>
                                <a href="{{ route('clients.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-gray-700 hover:bg-[#faf4fb] hover:text-[#511560] font-semibold transition">
                                    <i class="bi bi-building-add text-[#511560]"></i>
                                    <span>Registrar Nuevo Cliente</span>
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-[#511560] hover:bg-[#faf4fb] font-semibold transition border-t border-gray-100">
                                        <i class="bi bi-person-plus-fill text-[#511560]"></i>
                                        <span>Registrar Empleado</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Menú de Usuario Autenticado -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 text-xs font-semibold rounded-xl text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150 shadow-2xs">
                                <div class="w-6 h-6 rounded-full bg-[#511560] text-white flex items-center justify-center font-bold text-[10px]">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left hidden sm:block">
                                    <div class="font-bold leading-tight">{{ Auth::user()->name }}</div>
                                </div>
                                <i class="bi bi-chevron-down text-gray-400 text-[10px]"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/50">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Conectado como</p>
                                <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <x-dropdown-link :href="route('dashboard')" class="flex items-center gap-2 text-xs">
                                <i class="bi bi-speedometer2 text-[#511560]"></i>
                                {{ __('Dashboard 360') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('clients.index')" class="flex items-center gap-2 text-xs">
                                <i class="bi bi-people-fill text-[#511560]"></i>
                                {{ __('Directorio de Clientes') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('tickets.index')" class="flex items-center gap-2 text-xs">
                                <i class="bi bi-kanban-fill text-[#511560]"></i>
                                {{ __('Tablero Kanban') }}
                            </x-dropdown-link>

                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link :href="route('admin.users.index')" class="flex items-center gap-2 text-xs font-semibold text-[#511560] bg-[#faf4fb]/60 hover:bg-[#faf4fb]">
                                    <i class="bi bi-people-gear text-[#511560]"></i>
                                    {{ __('Gestión de Empleados') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-100 my-1"></div>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 text-xs">
                                <i class="bi bi-person-gear text-gray-400"></i>
                                {{ __('Mi Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="flex items-center gap-2 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                    <i class="bi bi-box-arrow-right"></i>
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                    <!-- Hamburger Mobile -->
                    <div class="-me-2 flex items-center md:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @else
                    <!-- Enlaces para Invitados (No Autenticados) -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#511560] hover:bg-[#41104d] text-white text-xs font-bold rounded-xl shadow-sm transition">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Iniciar Sesión</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @auth
        <!-- Responsive Navigation Menu (Mobile) -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-b border-gray-200 bg-white shadow-lg">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-xl flex items-center gap-2 font-semibold">
                    <i class="bi bi-speedometer2 text-base text-[#511560]"></i>
                    {{ __('Dashboard 360') }}
                </x-responsive-nav-link>

                <div class="pt-2 pb-1">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Clientes (CRM)</p>
                    <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.index')" class="rounded-xl flex items-center gap-2 font-medium">
                        <i class="bi bi-people-fill text-[#511560]"></i>
                        {{ __('Directorio de Clientes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('clients.create')" :active="request()->routeIs('clients.create')" class="rounded-xl flex items-center gap-2 font-medium">
                        <i class="bi bi-person-plus-fill text-[#511560]"></i>
                        {{ __('Registrar Nuevo Cliente') }}
                    </x-responsive-nav-link>
                </div>

                <div class="pt-2 pb-1">
                    <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tareas y Operaciones</p>
                    <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')" class="rounded-xl flex items-center gap-2 font-medium">
                        <i class="bi bi-kanban-fill text-[#511560]"></i>
                        {{ __('Tablero Tickets (Kanban)') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')" class="rounded-xl flex items-center gap-2 font-medium">
                        <i class="bi bi-plus-circle-fill text-[#511560]"></i>
                        {{ __('Crear Nuevo Ticket') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tickets.index', ['status' => 'qa'])" class="rounded-xl flex items-center gap-2 font-medium text-amber-700">
                        <i class="bi bi-shield-check text-amber-600"></i>
                        {{ __('Control de Calidad QA') }}
                    </x-responsive-nav-link>
                </div>

                @if(Auth::user()->isAdmin())
                    <div class="pt-2 pb-1">
                        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Administración</p>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="rounded-xl flex items-center gap-2 font-medium text-[#511560]">
                            <i class="bi bi-people-gear text-[#511560]"></i>
                            {{ __('Gestión de Empleados & Roles') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.create')" :active="request()->routeIs('admin.users.create')" class="rounded-xl flex items-center gap-2 font-medium">
                            <i class="bi bi-person-plus-fill text-[#511560]"></i>
                            {{ __('Registrar Empleado') }}
                        </x-responsive-nav-link>
                    </div>
                @endif
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-3 border-t border-gray-100 bg-gray-50/70 px-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-[#511560] text-white flex items-center justify-center font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" class="rounded-xl flex items-center gap-2 text-xs font-semibold">
                        <i class="bi bi-person-gear"></i>
                        {{ __('Mi Perfil') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="rounded-xl flex items-center gap-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50">
                            <i class="bi bi-box-arrow-right"></i>
                            {{ __('Cerrar Sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</nav>
