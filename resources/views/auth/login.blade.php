<x-guest-layout>
    <div class="mb-8">
        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-2xl lg:hidden">⚽</span>
        <p class="mt-4 text-sm font-extrabold uppercase tracking-[0.2em] text-green-700">Bienvenido de nuevo</p>
        <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Inicia sesión</h2>
        <p class="mt-2 text-slate-500">Ingresa tus datos para continuar al panel de administración.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Correo electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="admin@tuliga.com"
                   class="block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-green-600 focus:bg-white focus:ring-green-600">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="block text-sm font-bold text-slate-700">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-green-700 hover:text-green-800">¿La olvidaste?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   placeholder="Escribe tu contraseña"
                   class="block w-full rounded-xl border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-green-600 focus:bg-white focus:ring-green-600">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <label for="remember_me" class="flex items-center gap-3 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-green-700 focus:ring-green-600">
            Mantener sesión iniciada
        </label>

        <button type="submit" class="w-full rounded-xl bg-green-700 px-5 py-3.5 font-extrabold text-white shadow-lg shadow-green-900/15 transition hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-200">
            Entrar al panel
        </button>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
        ¿Necesitas acceso? Pídele una cuenta al administrador de la liga.
    </div>
</x-guest-layout>
