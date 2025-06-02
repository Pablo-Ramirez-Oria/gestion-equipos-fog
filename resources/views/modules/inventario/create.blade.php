<x-layouts.app :title="__('Gestión de equipos en el FOG - Crear equipo')">
    <h1 class="text-2xl mb-4">Crear equipo</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('inventario.store') }}" class="max-w-3xl space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf

        <!-- Campo Nombre -->
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                placeholder="Ej: pc-laboratorio-01"
                class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('nombre')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    El nombre es obligatorio, no puede contener espacios y debe tener menos de 15 caracteres.
                </p>
            @enderror
        </div>

        <!-- Campo Descripción -->
        <div>
            <label for="descripcion" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}"
                placeholder="Ej: Equipo del aula de redes"
                class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('descripcion')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">La descripción debe tener como máximo 255 caracteres.</p>
            @enderror
        </div>

        <!-- Campo MAC -->
        <div>
            <label for="mac" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Dirección MAC</label>
            <input type="text" id="mac" name="mac" value="{{ old('mac') }}"
                placeholder="Ej: 00:11:22:33:44:55"
                class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('mac')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    La dirección MAC debe tener un formato válido, por ejemplo: 00:11:22:33:44:55
                </p>
            @enderror
        </div>

        <!-- Campo Estado -->
        <div>
            <label for="estado_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Estado</label>
            <select name="estado_id" id="estado_id"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">Sin definir</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" @selected(old('estado_id') == $estado->id)>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
            @error('estado_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Debe seleccionar un estado válido o dejarlo en blanco.</p>
            @enderror
        </div>

        <!-- Campo Ubicación -->
        <div>
            <label for="ubicacion_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Ubicación</label>
            <select name="ubicacion_id" id="ubicacion_id"
                class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">Sin definir</option>
                @foreach ($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}" @selected(old('ubicacion_id') == $ubicacion->id)>
                        {{ $ubicacion->nombre }}
                    </option>
                @endforeach
            </select>
            @error('ubicacion_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Debe seleccionar una ubicación válida o dejarla en blanco.</p>
            @enderror
        </div>

        <!-- Campo Finalidad -->
        <div>
            <label for="finalidad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Finalidad</label>
            <input type="text" id="finalidad" name="finalidad" value="{{ old('finalidad') }}"
                placeholder="Ej: Uso docente, prácticas, administración..."
                class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500">
            @error('finalidad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">La finalidad debe tener como máximo 255 caracteres.</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex justify-end items-center space-x-4 mt-6">
            <a href="{{ route('inventario.index') }}" class="text-gray-500 hover:underline dark:text-gray-300">Cancelar</a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Crear equipo
            </button>
        </div>
    </form>
</x-layouts.app>
