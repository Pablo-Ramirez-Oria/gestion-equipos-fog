<x-layouts.app :title="__('Gestión de equipos en el FOG - Editar préstamo')">
    <h1 class="text-2xl mb-4">Editar préstamo</h1>

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('prestamos.update', $prestamo->id) }}">
        @csrf
        @method('PUT')
        <table class="w-full max-w-4xl mx-auto bg-white dark:bg-gray-800" style="border-collapse: collapse;">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="p-3 text-left text-gray-700 dark:text-gray-200">Datos del préstamo</th>
                    <th class="p-3 text-left text-gray-700 dark:text-gray-200">Persona del préstamo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top p-4" style="vertical-align: top;">
                        <!-- fog_id (solo lectura) -->
                        <div class="mb-4">
                            <label for="fog_id" class="block mb-1 font-medium text-gray-900 dark:text-white">ID del equipo FOG</label>
                            <input type="number" id="fog_id" name="fog_id" value="{{ $prestamo->fog_id }}" readonly
                                class="w-full px-3 py-2 border rounded-md bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-not-allowed" />
                        </div>

                        <!-- tipo_prestamo -->
                        <div class="mb-4">
                            <label for="tipo_prestamo" class="block mb-1 font-medium text-gray-900 dark:text-white">Tipo de préstamo</label>
                            <select name="tipo_prestamo" id="tipo_prestamo"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="clase" @selected($prestamo->tipo_prestamo === 'clase')>Clase</option>
                                <option value="casa" @selected($prestamo->tipo_prestamo === 'casa')>Casa</option>
                            </select>
                        </div>

                        <!-- fecha_inicio -->
                        <div class="mb-4">
                            <label for="fecha_inicio" class="block mb-1 font-medium text-gray-900 dark:text-white">Fecha de inicio</label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio"
                                value="{{ \Carbon\Carbon::parse($prestamo->fecha_inicio)->format('Y-m-d\TH:i') }}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        </div>

                        <!-- fecha_estimacion -->
                        <div class="mb-4">
                            <label for="fecha_estimacion" class="block mb-1 font-medium text-gray-900 dark:text-white">Fecha estimada de devolución</label>
                            <input type="datetime-local" id="fecha_estimacion" name="fecha_estimacion"
                                value="{{ \Carbon\Carbon::parse($prestamo->fecha_estimacion)->format('Y-m-d\TH:i') }}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        </div>

                        <!-- fecha_entrega -->
                        <div class="mb-4">
                            <label for="fecha_entrega" class="block mb-1 font-medium text-gray-900 dark:text-white">Fecha de devolución real</label>
                            <input type="datetime-local" id="fecha_entrega" name="fecha_entrega"
                                value="{{ $prestamo->fecha_entrega ? \Carbon\Carbon::parse($prestamo->fecha_entrega)->format('Y-m-d\TH:i') : '' }}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        </div>
                    </td>

                    <td class="align-top p-4" style="vertical-align: top;">
                        <!-- persona (solo selección) -->
                        <div class="mb-4">
                            <label for="persona_prestamo_id" class="block mb-1 font-medium text-gray-900 dark:text-white">Persona del préstamo</label>
                            <select name="persona_prestamo_id" id="persona_prestamo_id"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">-- Selecciona una persona --</option>
                                @foreach ($personas as $persona)
                                    <option value="{{ $persona->id }}" @selected($prestamo->persona_prestamo_id == $persona->id)>
                                        {{ $persona->nombre_completo }} ({{ $persona->tipo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-6 flex justify-end max-w-4xl mx-auto">
            <a href="{{ route('prestamos.index') }}"
                class="mr-4 inline-block px-4 py-2 border rounded-md text-gray-700 dark:text-white border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                Cancelar
            </a>
            <button type="submit"
                class="inline-block px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                Guardar cambios
            </button>
        </div>
    </form>
</x-layouts.app>
