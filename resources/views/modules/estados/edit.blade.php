<x-layouts.app :title="__('Gestión de equipos en el FOG - Editar Estado')">
    <h1 class="text-2xl mb-4">Editar Estado</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('estados.update', $estado) }}" class="max-w-3xl space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Nombre</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                value="{{ old('nombre', $estado->nombre) }}"
                placeholder="Ej: En uso, Disponible..."
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
            @error('nombre')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Este campo es obligatorio.</p>
            @enderror
        </div>

        <div class="flex justify-end items-center space-x-4 mt-6">
            <a href="{{ route('estados.index') }}" class="text-gray-500 hover:underline dark:text-gray-300">Cancelar</a>
            <button type="submit"
                class="inline-block px-5 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Actualizar Estado
            </button>
        </div>
    </form>
</x-layouts.app>
