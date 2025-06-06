<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Gestión de equipos en el FOG')" 
        :description="__('Introduce tus datos para acceder al sistema')" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="example@iesmartinezm.es"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Contraseña')"
            />

            {{-- @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </flux:link>
            @endif --}}
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Recuérdame')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesión') }}</flux:button>
        </div>
    </form>

    {{-- @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('¿No tienes una cuenta?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Regístrate') }}</flux:link>
        </div>
    @endif --}}
</div>
