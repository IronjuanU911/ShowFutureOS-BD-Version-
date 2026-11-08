<x-guest-layout>
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">ShowFuture OS</h2>
        <p class="text-xs text-gray-500">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="pure-form pure-form-stacked space-y-3">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="text-xs font-bold text-gray-700 mb-1"><i class="bi bi-envelope mr-1"></i> Correo Electrónico</label>
            <input id="email" class="pure-input-1 text-sm border-gray-200 rounded-xl" type="email" name="email" value="{{ old('email', 'admin@showfuture.com') }}" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="text-xs font-bold text-gray-700 mb-1"><i class="bi bi-lock mr-1"></i> Contraseña</label>
            <input id="password" class="pure-input-1 text-sm border-gray-200 rounded-xl" type="password" name="password" value="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="d-flex align-items-center justify-content-between pt-2">
            <label for="remember_me" class="pure-checkbox text-xs text-gray-600 d-flex align-items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#511560] focus:ring-[#511560]" name="remember">
                Recordarme
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs text-[#511560] hover:underline font-semibold" href="{{ route('password.request') }}">
                    ¿Olvidaste tu clave?
                </a>
            @endif
        </div>

        <div class="pt-3">
            <button type="submit" class="pure-button pure-button-primary bg-[#511560] hover:bg-[#41104d] text-white w-100 text-sm font-bold rounded-xl py-2.5 shadow-md hover:shadow-lg transition">
                <i class="bi bi-box-arrow-in-right mr-1"></i> Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>
