<x-layouts.app :title="'Gestión de equipos en el FOG - Ubicaciones'">

    <h1 class="text-2xl mb-4">Ubicaciones</h1>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

        <div class="flex items-center justify-between pb-4">

            @role('admin')
                <a href="{{ route('ubicaciones.create') }}" 
                    class="inline-flex items-center py-2 text-sm font-medium text-white">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Ubicación
                </a>
            @endrole

            <form method="GET" action="{{ route('ubicaciones.index') }}">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="text" name="busqueda" value="{{ $busqueda }}" 
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-72 bg-gray-50 
                               focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                               dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="Buscar por nombre">
                </div>
            </form>
        </div>

        @if (session('success'))
            <div id="alert-success" class="flex items-center justify-between p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-green-200 dark:text-green-900" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4l5-5-1.414-1.414L9 11.172 7.414 9.586 6 11l3 3z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('alert-success').remove()" class="ml-4 text-green-800 hover:underline dark:text-green-900">
                    ✕
                </button>
            </div>
        @endif

        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Nombre</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ubicaciones as $ubicacion)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $ubicacion->nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center space-x-2 text-sm">
                                <a href="{{ route('ubicaciones.show', $ubicacion) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Ver
                                </a>

                                @role('admin')
                                    <span class="text-gray-400">|</span>
                                    <a href="{{ route('ubicaciones.edit', $ubicacion) }}" class="text-yellow-600 hover:underline">
                                        Editar
                                    </a>
                                    <span class="text-gray-400">|</span>
                                    <button type="button" class="text-red-600 hover:underline" data-modal-toggle="modal-eliminar-{{ $ubicacion->id }}">
                                        Eliminar
                                    </button>

                                    <!-- Modal eliminar -->
                                    <div id="modal-eliminar-{{ $ubicacion->id }}" class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50">
                                        <div class="relative w-full max-w-sm p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                                            <button type="button" class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" data-modal-hide="modal-eliminar-{{ $ubicacion->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                            <h2 class="text-lg font-bold mb-4">
                                                ¿Estás seguro de que deseas eliminar <br> esta ubicación?
                                            </h2>
                                            <p>Esta acción no se puede deshacer.</p>
                                            <div class="mt-4 flex justify-end space-x-2">
                                                <button type="button" class="text-gray-500 dark:text-gray-400 hover:underline" data-modal-hide="modal-eliminar-{{ $ubicacion->id }}">
                                                    Cancelar
                                                </button>
                                                <form action="{{ route('ubicaciones.destroy', $ubicacion) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endrole
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No se encontraron ubicaciones.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $ubicaciones->links() }}
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
