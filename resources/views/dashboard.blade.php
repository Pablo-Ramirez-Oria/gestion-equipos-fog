<x-layouts.app :title="__('Gestión de equipos en el FOG - Dashboard')">
    <div class="space-y-6 max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        {{-- Estadísticas generales --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <div class="text-sm text-gray-500 dark:text-gray-300">Préstamos activos</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                    {{ $prestamosActivos }}
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <div class="text-sm text-gray-500 dark:text-gray-300">Préstamos tardíos</div>
                <div class="mt-1 text-3xl font-semibold text-red-600 dark:text-red-400">
                    {{ $prestamosTardios }}
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <div class="text-sm text-gray-500 dark:text-gray-300">FOG</div>
                <div class="mt-1 text-xl font-semibold {{ $fogAccessible ? 'text-green-600' : 'text-red-600' }}">
                    {{ $fogAccessible ? 'Accesible' : 'No accesible' }}
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <div class="text-sm text-gray-500 dark:text-gray-300">MySQL</div>
                <div class="mt-1 text-xl font-semibold {{ $mysqlAccessible ? 'text-green-600' : 'text-red-600' }}">
                    {{ $mysqlAccessible ? 'Conectado' : 'Error' }}
                </div>
            </div>
        </div>

        {{-- Actividad reciente y próximos a vencer --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Actividad reciente --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-h-64 overflow-y-auto">
                <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Actividad reciente</h2>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    @forelse ($actividadReciente as $item)
                        <li class="flex justify-between">
                            <span>
                                @if ($item['tipo'] === 'prestamo')
                                    Préstamo de <strong>{{ $item['nombre'] }}</strong> actualizado
                                @else
                                    <strong>{{ $item['nombre'] }}</strong> actualizado
                                @endif
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($item['updated_at'])->diffForHumans() }}
                            </span>
                        </li>
                    @empty
                        <li>No hay actividad reciente.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Préstamos próximos a vencer --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 max-h-64 overflow-y-auto">
                <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">
                    Préstamos próximos a vencer <span class="text-sm text-gray-400">(en 7 días)</span>
                </h2>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    @forelse ($proximosAVencer as $prestamo)
                        <li class="flex justify-between">
                            <span><strong>{{ $prestamo->persona->nombre_completo ?? 'Sin nombre' }}</strong></span>
                            <span class="text-xs text-gray-400">
                                {{ $prestamo->fecha_estimacion->format('d/m/Y') }}
                            </span>
                        </li>
                    @empty
                        <li>No hay préstamos próximos a vencer.</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</x-layouts.app>
