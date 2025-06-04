<x-layouts.app :title="__('Gestión de equipos en el FOG - Nueva persona')">
    <h1 class="text-2xl mb-4">Registrar persona para préstamo</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('personas.store') }}" class="max-w-3xl space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf

        <!-- Campo Nombre Completo -->
        <div>
            <label for="nombre_completo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}"
                placeholder="Ej: Ana Martínez Ruiz"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('nombre_completo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Este campo es obligatorio.</p>
            @enderror
        </div>

        <!-- Campo Mayor de Edad -->
        <div>
            <label for="mayor_edad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">¿Es mayor de edad?</label>
            <select id="mayor_edad" name="mayor_edad"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Elige...</option>
                <option value="1" @selected(old('mayor_edad') === '1')>Sí</option>
                <option value="0" @selected(old('mayor_edad') === '0')>No</option>
            </select>
            @error('mayor_edad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Selecciona una opción válida.</p>
            @enderror
        </div>

        <!-- Campo Correo -->
        <div>
            <label for="correo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Correo electrónico</label>
            <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                placeholder="Ej: ejemplo@centroeducativo.es"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('correo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Introduce un correo válido.</p>
            @enderror
        </div>

        <!-- Campo Teléfono -->
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"
                placeholder="Ej: 600123456"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('telefono')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Introduce un número válido.</p>
            @enderror
        </div>

        <!-- Campo Curso -->
        <div>
            <label for="curso" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Curso</label>
            <input type="text" id="curso" name="curso" value="{{ old('curso') }}"
                placeholder="Ej: 1º SMR, 2º DAW..."
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('curso')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Este campo es opcional.</p>
            @enderror
        </div>

        <!-- Campo Unidad -->
        <div>
            <label for="unidad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Unidad</label>
            <input type="text" id="unidad" name="unidad" value="{{ old('unidad') }}"
                placeholder="Ej: A, B, C..."
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('unidad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Este campo es opcional.</p>
            @enderror
        </div>

        <!-- Campo Tipo -->
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Tipo</label>
            <select name="tipo" id="tipo"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Seleccionar tipo</option>
                <option value="alumno" @selected(old('tipo') === 'alumno')>Alumno</option>
                <option value="profesor" @selected(old('tipo') === 'profesor')>Profesor</option>
            </select>
            @error('tipo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Este campo es obligatorio.</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex justify-end items-center space-x-4 mt-6">
            <a href="{{ route('personas.index') }}" class="text-gray-500 hover:underline dark:text-gray-300">Cancelar</a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Registrar persona
            </button>
        </div>
    </form>
</x-layouts.app>
