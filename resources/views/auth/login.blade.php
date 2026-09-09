<x-guest-layout>
    <!-- Título estilo Liga Española -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-extrabold text-red-600 tracking-widest uppercase">
            Liga Española
        </h1>
        <p class="text-red-600 mt-1 text-sm">
            Acceso para administradores
        </p>
    </div>

    <!-- Estado de sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="email"
                class="block mt-1 w-full rounded-lg border-black dark:border-black bg-white dark:bg-gray-800 text-black dark:text-white shadow-md focus:ring-red-600 focus:border-red-600"
                type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>



        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300" />
            <x-text-input id="password"
                class="block mt-1 w-full rounded-lg border-black dark:border-black bg-white dark:bg-gray-800 text-black dark:text-white shadow-md focus:ring-red-600 focus:border-red-600"
                type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Botón de ingresar -->
        <button class="w-full py-2 bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-lg transition">
            Ingresar
        </button>
    </form>

    <!-- Footer -->
    <p class="text-center text-gray-500 dark:text-gray-400 text-sm mt-6">
        &copy; {{ date('Y') }} Liga Española de Fútbol. Todos los derechos reservados.
    </p>
</x-guest-layout>
