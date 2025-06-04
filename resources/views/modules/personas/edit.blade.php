<x-layouts.app :title="__('Editar persona')">
    <h1 class="text-2xl mb-4">Editar persona</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('personas.update', $persona) }}" class="max-w-3xl space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <!-- Nombre completo -->
        <div>
            <label for="nombre_completo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo', $persona->nombre_completo) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('nombre_completo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mayor de edad -->
        <div>
            <label for="mayor_edad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Mayor de edad</label>
            <select name="mayor_edad" id="mayor_edad" class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="1" @selected(old('mayor_edad', $persona->mayor_edad) == 1)>Sí</option>
                <option value="0" @selected(old('mayor_edad', $persona->mayor_edad) == 0)>No</option>
            </select>
            @error('mayor_edad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Correo -->
        <div>
            <label for="correo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Correo electrónico</label>
            <input type="email" id="correo" name="correo" value="{{ old('correo', $persona->correo) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('correo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Teléfono -->
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $persona->telefono) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('telefono')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Curso -->
        <div>
            <label for="curso" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Curso</label>
            <input type="text" id="curso" name="curso" value="{{ old('curso', $persona->curso) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('curso')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Unidad -->
        <div>
            <label for="unidad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Unidad</label>
            <input type="text" id="unidad" name="unidad" value="{{ old('unidad', $persona->unidad) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('unidad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tipo -->
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Tipo</label>
            <select name="tipo" id="tipo" class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="alumno" @selected(old('tipo', $persona->tipo) === 'alumno')>Alumno</option>
                <option value="profesor" @selected(old('tipo', $persona->tipo) === 'profesor')>Profesor</option>
                <option value="otro" @selected(old('tipo', $persona->tipo) === 'otro')>Otro</option>
            </select>
            @error('tipo')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex justify-end items-center space-x-4 mt-6">
            <a href="{{ route('personas.index') }}" class="text-gray-500 hover:underline dark:text-gray-300">Cancelar</a>
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Guardar cambios
            </button>
        </div>
    </form>
</x-layouts.app>
