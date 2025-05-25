<x-layouts.app :title="__('Editar equipo')">
    <h1 class="text-2xl mb-4">Editar equipo: {{ $producto['nombre'] }}</h1>

    <form id="edit-form" method="POST" action="{{ route('inventario.update', $producto['id_equipo']) }}" class="max-w-3xl space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        @php
            $fields = [
                'nombre' => 'Nombre',
                'descripcion' => 'Descripción',
                'ip' => 'Dirección IP',
                'mac' => 'Dirección MAC',
            ];
        @endphp

        @foreach ($fields as $key => $label)
            @php
                $isDisabled = in_array($key, ['ip', 'mac']);
            @endphp
            <div>
                <label for="{{ $key }}" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">
                    {{ $label }}
                </label>
                <input type="text"
                    id="{{ $key }}"
                    name="{{ $key }}"
                    value="{{ old($key, $producto[$key]) }}"
                    @if($isDisabled) disabled @endif
                    class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none
                            {{ $isDisabled 
                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-gray-600 dark:text-gray-400' 
                                : 'bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white' }}
                            focus:ring-2 focus:ring-blue-500 placeholder-gray-400 dark:placeholder-gray-400">
                @error($key)
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                        @switch($key)
                            @case('nombre')
                                El nombre es obligatorio, no puede contener espacios y debe tener menos de 15 caracteres.
                                @break
                            @case('descripcion')
                                La descripción debe tener como máximo 255 caracteres.
                                @break
                        @endswitch
                    </p>
                @enderror
            </div>
        @endforeach

        <div>
            <label for="estado_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Estado</label>
            <select name="estado_id" id="estado_id"
                    class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 
                        bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Sin definir</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" @selected(old('estado_id', $detalle->estado_id) == $estado->id)>
                        {{ $estado->nombre }}
                    </option>
                @endforeach
            </select>
            @error('estado_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Debe seleccionar un estado válido o dejarlo en blanco.</p>
            @enderror
        </div>

        <div>
            <label for="ubicacion_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Ubicación</label>
            <select name="ubicacion_id" id="ubicacion_id"
                    class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 
                        bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Sin definir</option>
                @foreach ($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}" @selected(old('ubicacion_id', $detalle->ubicacion_id) == $ubicacion->id)>
                        {{ $ubicacion->nombre }}
                    </option>
                @endforeach
            </select>
            @error('ubicacion_id')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Debe seleccionar una ubicación válida o dejarla en blanco.</p>
            @enderror
        </div>

        <div>
            <label for="finalidad" class="block text-sm font-medium text-gray-900 dark:text-white mb-1">Finalidad</label>
            <input type="text" id="finalidad" name="finalidad" value="{{ old('finalidad', $detalle->finalidad_actual) }}"
                class="w-full px-4 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 
                       bg-gray-50 border-gray-300 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @error('finalidad')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    El campo finalidad debe tener como máximo 255 caracteres.
                </p>
            @enderror
        </div>

        <div class="flex justify-end items-center space-x-4 mt-6">
            <a href="{{ route('inventario.index') }}" class="text-gray-500 hover:underline dark:text-gray-300">Cancelar</a>
            <button type="button" id="confirm-button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Guardar cambios
            </button>
        </div>

        <!-- Modal de Confirmación -->
        <div id="confirm-modal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-xl w-full relative">
                <h2 class="text-lg font-bold mb-4">Confirmar cambios</h2>
                <ul id="changes-list" class="space-y-2 text-sm text-gray-700 dark:text-gray-200 mb-6"></ul>
                <div class="flex justify-end mt-6">
                    <button type="button" class="text-gray-600 dark:text-gray-300 hover:underline me-4" onclick="toggleModal(false)">
                        Cancelar
                    </button>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Confirmar
                    </button>
                </div>
                <button class="absolute top-2 right-2 text-gray-500 dark:text-gray-400" onclick="toggleModal(false)">
                    ✕
                </button>
            </div>
        </div>
    </form>

    @php
        $originalValues = [
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'],
            'ip' => $producto['ip'],
            'mac' => $producto['mac'],
            'estado_id' => $detalle->estado_id,
            'ubicacion_id' => $detalle->ubicacion_id,
            'finalidad' => $detalle->finalidad_actual,
        ];
    @endphp

    <script>
        const estadoNombres = @json($estados->pluck('nombre', 'id'));
        const ubicacionNombres = @json($ubicaciones->pluck('nombre', 'id'));
        const originalValues = @json($originalValues);

        document.getElementById('confirm-button').addEventListener('click', () => {
            const changes = [];
            const fields = ['nombre', 'descripcion', 'estado_id', 'ubicacion_id', 'finalidad'];

            fields.forEach(field => {
                const input = document.getElementById(field);
                if (!input) return;

                const newValue = input.value.trim();
                const originalValue = String(originalValues[field] ?? '').trim();

                if (newValue !== originalValue) {
                    const label = input.previousElementSibling?.innerText ?? field;

                    let oldDisplay = originalValue || '-';
                    let newDisplay = newValue || '-';

                    if (field === 'estado_id') {
                        oldDisplay = estadoNombres[originalValues[field]] || '-';
                        newDisplay = estadoNombres[newValue] || '-';
                    } else if (field === 'ubicacion_id') {
                        oldDisplay = ubicacionNombres[originalValues[field]] || '-';
                        newDisplay = ubicacionNombres[newValue] || '-';
                    }

                    changes.push(`<li><strong>${label}:</strong> <em>${oldDisplay}</em> → <strong>${newDisplay}</strong></li>`);
                }
            });

            const changesList = document.getElementById('changes-list');
            if (changesList) {
                changesList.innerHTML = changes.length ? changes.join('') : '<li>No hay cambios.</li>';
                toggleModal(true);
            }
        });

        function toggleModal(show) {
            const modal = document.getElementById('confirm-modal');
            if (modal) modal.classList.toggle('hidden', !show);
        }
    </script>
</x-layouts.app>
