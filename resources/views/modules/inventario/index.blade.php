<x-layouts.app :title="__('Inventario')">
    <h1 class="text-2xl">Inventario</h1>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <!-- Filtros y barra de búsqueda (opcional) -->
        <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
            <div>
                <!-- Aquí puedes poner tus filtros si es necesario -->
            </div>
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                </div>
                <input type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
            </div>
        </div>

        <!-- Tabla -->
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 whitespace-nowrap">ID</th>
                    <th class="px-4 py-3 whitespace-nowrap">Nombre</th>
                    <th class="px-4 py-3 whitespace-nowrap">Descripción</th>
                    <th class="px-4 py-3 whitespace-nowrap">IP</th>
                    <th class="px-4 py-3 whitespace-nowrap">Fecha de creación</th>
                    <th class="px-4 py-3 whitespace-nowrap">MAC</th>
                    <th class="px-4 py-3 whitespace-nowrap">Ubicación</th>
                    <th class="px-4 py-3 whitespace-nowrap">Estado</th>
                    <th class="px-4 py-3 whitespace-nowrap">Inventario</th>
                    <th class="px-4 py-3 whitespace-nowrap">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    @php
                        // Aquí simulamos valores de Ubicación y Estado TODO: Reemplazar con datos reales
                        $ubicacion = ['Oficina', 'Almacén', 'Laboratorio', 'Sala 1'][rand(0, 3)];
                        $estado = ['Activo', 'Inactivo', 'En mantenimiento', 'Pendiente'][rand(0, 3)];
                    @endphp
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['id_equipo'] }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['nombre'] ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['descripcion'] ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['ip'] ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['fecha_creacion'] ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $producto['mac'] ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $ubicacion }}</td> <!-- Ubicación generada -->
                        <td class="px-4 py-4 whitespace-nowrap">{{ $estado }}</td> <!-- Estado generado -->
                        <td class="px-4 py-4 whitespace-nowrap">
                            <!-- Botón de detalles de inventario -->
                            <button type="button" class="text-blue-600 dark:text-blue-500 hover:underline" data-modal-toggle="modal-{{ $producto['id_equipo'] }}">
                                Detalles de inventario
                            </button>

                            <!-- Modal de detalles de inventario -->
                            <div id="modal-{{ $producto['id_equipo'] }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50">
                                <div class="relative w-full max-w-3xl p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                                    <button type="button" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" data-modal-toggle="modal-{{ $producto['id_equipo'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <h2 class="text-xl font-bold mb-4">Detalles de Inventario: {{ $producto['nombre'] }}</h2>
                                    <div class="space-y-2">
                                        <!-- Mostrar detalles del inventario, reemplazamos valores vacíos por "-" -->
                                        @foreach ($producto['inventory'] as $key => $value)
                                            <div class="flex justify-between">
                                                <span class="font-medium">{{ ucfirst($key) }}:</span>
                                                <span>{{ $value ?: '-' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- Botones de Editar y Eliminar -->
                            <button type="button" class="text-yellow-600 hover:underline" onclick="location.href='{{ route('inventario.edit', $producto['id_equipo']) }}'">
                                Editar
                            </button>
                            <span class="mx-2">|</span>
                            <button type="button" class="text-red-600 hover:underline" data-modal-toggle="modal-eliminar-{{ $producto['id_equipo'] }}">
                                Eliminar
                            </button>

                            <!-- Modal de Confirmación de Eliminación -->
                            <div id="modal-eliminar-{{ $producto['id_equipo'] }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50">
                                <div class="relative w-full max-w-sm p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                                    <button type="button" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" data-modal-toggle="modal-eliminar-{{ $producto['id_equipo'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        </button>
                                    <h2 class="text-lg font-bold mb-4">¿Estás seguro de que deseas eliminar <br> este producto?</h2>
                                    <p>Esta acción no se puede deshacer.</p>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <button type="button" class="text-gray-500 dark:text-gray-400 hover:underline" data-modal-toggle="modal-eliminar-{{ $producto['id_equipo'] }}">Cancelar</button>
                                        <form action="{{ route('inventario.destroy', $producto['id_equipo']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

    <!-- Script para abrir y cerrar los modales -->
    <script>
        // Para abrir y cerrar los modales
        document.querySelectorAll('[data-modal-toggle]').forEach(button => {
            button.addEventListener('click', (e) => {
                const modalId = button.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);
                modal.classList.toggle('hidden');
            });
        });

        // Cerrar el modal si se hace clic fuera del contenido
        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-layouts.app>
