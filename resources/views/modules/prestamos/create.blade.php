<x-layouts.app :title="__('Gestión de equipos en el FOG - Crear préstamo')">
    <h1 class="text-2xl mb-4">Crear nuevo préstamo</h1>

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

    <form method="POST" action="{{ route('prestamos.store') }}" @submit.prevent>
        @csrf
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
                        <!-- fog_id -->
                        <div class="mb-4">
                            <label for="fog_id" class="block mb-1 font-medium text-gray-900 dark:text-white">ID del equipo FOG</label>
                            <input type="number" id="fog_id" name="fog_id" value="{{ old('fog_id') }}"
                                placeholder="Ej: 123"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('fog_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- tipo_prestamo -->
                        <div class="mb-4">
                            <label for="tipo_prestamo" class="block mb-1 font-medium text-gray-900 dark:text-white">Tipo de préstamo</label>
                            <select name="tipo_prestamo" id="tipo_prestamo"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="clase" @selected(old('tipo_prestamo') === 'clase')>Clase</option>
                                <option value="casa" @selected(old('tipo_prestamo') === 'casa')>Casa</option>
                            </select>
                            @error('tipo_prestamo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- fecha_inicio -->
                        <div class="mb-4">
                            <label for="fecha_inicio" class="block mb-1 font-medium text-gray-900 dark:text-white">Fecha de inicio</label>
                            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- fecha_estimacion -->
                        <div>
                            <label for="fecha_estimacion" class="block mb-1 font-medium text-gray-900 dark:text-white">Fecha estimada de devolución</label>
                            <input type="datetime-local" id="fecha_estimacion" name="fecha_estimacion" value="{{ old('fecha_estimacion') }}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @error('fecha_estimacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </td>
                    <td class="align-top p-4" style="vertical-align: top;" x-data="{ personaExistente: true }">
                        <div class="mb-4 flex items-center gap-3">
                            <input type="checkbox" id="personaExistente" x-model="personaExistente"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
                            <label for="personaExistente" class="font-medium text-gray-900 dark:text-white">¿El usuario ya existe?</label>
                        </div>

                        <div class="mb-4">
                            <label for="persona_prestamo_id" class="block mb-1 font-medium text-gray-900 dark:text-white">Seleccionar usuario existente</label>
                            <select name="persona_prestamo_id" id="persona_prestamo_id"
                                :disabled="!personaExistente"
                                :class="{'opacity-50 cursor-not-allowed': !personaExistente, 'opacity-100 cursor-pointer': personaExistente}"
                                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">-- Selecciona una persona --</option>
                                @foreach ($personas as $persona)
                                    <option value="{{ $persona->id }}" @selected(old('persona_prestamo_id') == $persona->id)>
                                        {{ $persona->nombre_completo }} ({{ $persona->tipo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('persona_prestamo_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <fieldset :class="{ 'opacity-50 pointer-events-none': personaExistente }" style="margin-top: 1rem;">
                            <legend class="font-semibold text-gray-900 dark:text-white mb-3">Crear nueva persona</legend>

                            <div class="mb-4">
                                <label for="nombre_completo" class="block mb-1 font-medium text-gray-900 dark:text-white">Nombre completo</label>
                                <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}"
                                    placeholder="Ej: Juan Pérez"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div class="mb-4">
                                <label for="correo" class="block mb-1 font-medium text-gray-900 dark:text-white">Correo</label>
                                <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                                    placeholder="Opcional"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div class="mb-4">
                                <label for="telefono" class="block mb-1 font-medium text-gray-900 dark:text-white">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                                    placeholder="Opcional"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div class="mb-4">
                                <label for="curso" class="block mb-1 font-medium text-gray-900 dark:text-white">Curso</label>
                                <input type="text" id="curso" name="curso" value="{{ old('curso') }}"
                                    placeholder="Ej: 2ºSMR"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div class="mb-4">
                                <label for="unidad" class="block mb-1 font-medium text-gray-900 dark:text-white">Unidad</label>
                                <input type="text" id="unidad" name="unidad" value="{{ old('unidad') }}"
                                    placeholder="Ej: A"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div>
                                <label for="tipo" class="block mb-1 font-medium text-gray-900 dark:text-white">Tipo</label>
                                <select name="tipo" id="tipo"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="alumno" @selected(old('tipo') === 'alumno')>Alumno</option>
                                    <option value="profesor" @selected(old('tipo') === 'profesor')>Profesor</option>
                                </select>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="p-4 text-center space-x-4">
                        <a href="{{ route('prestamos.index') }}"
                            class="inline-block mr-4 px-5 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-block px-5 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Crear préstamo
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('personaForm', () => ({
                personaExistente: true,
            }))
        })
    </script>
</x-layouts.app>
