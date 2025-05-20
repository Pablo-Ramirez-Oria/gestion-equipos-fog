<x-layouts.app :title="__('Inventario')">
    <h1 class="text-2xl">Inventario</h1>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Filtros y barra de búsqueda -->
        <div class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div class="flex space-x-4 items-center">
                <!-- Dropdown de Estado -->
                <div>
                    <button id="dropdownEstadoButton" data-dropdown-toggle="dropdownEstado" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                        <flux:icon.rocket-launch class="w-6 h-4 mr-2"/>
                        {{ $estados->firstWhere('id', request('estado'))?->nombre ?? 'Estado' }}
                        <svg class="w-2.5 h-2.5 ms-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" /></svg>
                    </button>
                    <div id="dropdownEstado" class="z-10 hidden w-48 bg-white rounded-lg shadow dark:bg-gray-700">
                        <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <a href="{{ route('inventario.index', ['ubicacion' => request('ubicacion'), 'busqueda' => request('busqueda')]) }}" class="block px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">Todos</a>
                            </li>
                            @foreach ($estados as $estado)
                                <li>
                                    <a href="{{ route('inventario.index', ['estado' => $estado->id, 'ubicacion' => request('ubicacion'), 'busqueda' => request('busqueda')]) }}" class="block px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">{{ $estado->nombre }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Dropdown de Ubicaciones -->
                <div>
                    <button id="dropdownUbicacionButton" data-dropdown-toggle="dropdownUbicacion" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                        <flux:icon.map-pin />
                        {{ $ubicaciones->firstWhere('id', request('ubicacion'))?->nombre ?? 'Ubicación' }}
                        <svg class="w-2.5 h-2.5 ms-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" /></svg>
                    </button>
                    <div id="dropdownUbicacion" class="z-10 hidden w-48 bg-white rounded-lg shadow dark:bg-gray-700">
                        <ul class="p-3 space-y-1 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <a href="{{ route('inventario.index', ['estado' => request('estado'), 'busqueda' => request('busqueda')]) }}" class="block px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">Todas</a>
                            </li>
                            @foreach ($ubicaciones as $ubicacion)
                                <li>
                                    <a href="{{ route('inventario.index', ['ubicacion' => $ubicacion->id, 'estado' => request('estado'), 'busqueda' => request('busqueda')]) }}" class="block px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">{{ $ubicacion->nombre }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if(request('estado') || request('ubicacion') || request('busqueda'))
                    <a href="{{ route('inventario.index') }}" class="text-sm text-red-600 hover:underline">
                        <i class="fi fi-rr-cross-small me-1"></i> Quitar filtros
                    </a>
                @endif
            </div>

            <!-- Formulario de búsqueda -->
            <form method="GET" action="{{ route('inventario.index') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <input type="hidden" name="ubicacion" value="{{ request('ubicacion') }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
                    </div>
                    <input type="text" name="busqueda" value="{{ request('busqueda') }}" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar por nombre o IP">
                </div>
            </form>
        </div>

        <!-- Tabla -->
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">IP</th>
                    <th class="px-4 py-3">Fecha de creación</th>
                    <th class="px-4 py-3">MAC</th>
                    <th class="px-4 py-3">Ubicación</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Finalidad</th>
                    <th class="px-4 py-3">Inventario</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($productos as $producto)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-4">{{ $producto['id_equipo'] }}</td>
                    <td class="px-4 py-4">{{ $producto['nombre'] ?: '-' }}</td>
                    <td class="px-4 py-4">{{ $producto['descripcion'] ?: '-' }}</td>
                    <td class="px-4 py-4">{{ $producto['ip'] ?: '-' }}</td>
                    <td class="px-4 py-4">{{ $producto['fecha_creacion'] ?: '-' }}</td>
                    <td class="px-4 py-4">{{ $producto['mac'] ?: '-' }}</td>
                    <td class="px-4 py-4">{{ $producto['ubicacion'] }}</td>
                    <td class="px-4 py-4">{{ $producto['estado'] }}</td>
                    <td class="px-4 py-4">{{ $producto['finalidad_actual'] }}</td>
                    <td class="px-4 py-4">
                        @if (!empty($producto['id_equipo']))
                            <button type="button" class="text-blue-600 dark:text-blue-500 hover:underline" data-modal-toggle="modal-{{ $producto['id_equipo'] }}">
                                Detalles de inventario
                            </button>

                            <div id="modal-{{ $producto['id_equipo'] }}" class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50">
                                <div class="relative w-full max-w-3xl p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                                    <button type="button" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" data-modal-hide="modal-{{ $producto['id_equipo'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <h2 class="text-xl font-bold mb-4">Detalles de Inventario: {{ $producto['nombre'] }}</h2>
                                    <div class="space-y-2">
                                        @foreach ($producto['inventory'] as $key => $value)
                                            <div class="flex justify-between">
                                                <span class="font-medium">{{ ucfirst($key) }}:</span>
                                                <span>{{ $value ?: '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if (!empty($producto['id_equipo']))
                            <button type="button" class="text-yellow-600 hover:underline" onclick="location.href='{{ route('inventario.edit', $producto['id_equipo']) }}'">
                                Editar
                            </button>
                            <span class="mx-2">|</span>
                            <button type="button" class="text-red-600 hover:underline" data-modal-toggle="modal-eliminar-{{ $producto['id_equipo'] }}">
                                Eliminar
                            </button>

                            <div id="modal-eliminar-{{ $producto['id_equipo'] }}" class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50">
                                <div class="relative w-full max-w-sm p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                                    <button type="button" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" data-modal-hide="modal-eliminar-{{ $producto['id_equipo'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <h2 class="text-lg font-bold mb-4">¿Estás seguro de que deseas eliminar <br> este producto?</h2>
                                    <p>Esta acción no se puede deshacer.</p>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" class="text-gray-500 dark:text-gray-400 hover:underline" data-modal-hide="modal-eliminar-{{ $producto['id_equipo'] }}">
                                            Cancelar
                                        </button>
                                        <form action="{{ route('inventario.destroy', $producto['id_equipo']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="px-6 py-3">
            {{ $productos->links() }}
        </div>
    </div>

    <!-- Script manual para manejar modales -->
    <script>
        document.querySelectorAll('[data-modal-toggle]').forEach(button => {
            const modalId = button.getAttribute('data-modal-toggle');
            if (!modalId) return;
            const modal = document.getElementById(modalId);
            if (!modal) return;
            button.addEventListener('click', () => {
                modal.classList.toggle('hidden');
            });
        });

        document.querySelectorAll('[data-modal-hide]').forEach(button => {
            const modalId = button.getAttribute('data-modal-hide');
            if (!modalId) return;
            const modal = document.getElementById(modalId);
            if (!modal) return;
            button.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-layouts.app>
