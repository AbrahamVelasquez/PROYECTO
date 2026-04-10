<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        
        <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="busqueda" 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                    placeholder="Buscar alumno...">
            </div>
            
            <div class="flex items-center gap-2">
                <label for="filtroEstado" class="text-sm font-medium text-gray-700">Estado:</label>
                <select id="filtroEstado" 
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md border">
                    <option value="todos">Todos</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Editar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumno</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="tablaCuerpo">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button 
                                onclick="mostrarEdicion({id: 1, nombre: 'Juan Pérez', email: 'juan.perez@email.com'})"
                                class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-lg transition cursor-pointer">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Juan Pérez</div>
                            <div class="text-sm text-gray-500">juan.perez@email.com</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Activo
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputBusqueda = document.querySelector('#busqueda');
        const selectEstado = document.querySelector('#filtroEstado');
        const filas = document.querySelectorAll('#tablaCuerpo tr');

        const filtrarTabla = () => {
            const texto = inputBusqueda.value.toLowerCase();
            const estado = selectEstado.value.toLowerCase();

            filas.forEach(fila => {
                const nombreAlumno = fila.children[1].textContent.toLowerCase();
                const estadoAlumno = fila.children[2].textContent.toLowerCase();

                const coincideNombre = nombreAlumno.includes(texto);
                const coincideEstado = estado === 'todos' || estadoAlumno.includes(estado);

                if (coincideNombre && coincideEstado) {
                    fila.classList.remove('hidden');
                } else {
                    fila.classList.add('hidden');
                }
            });
        };

        inputBusqueda.addEventListener('input', filtrarTabla);
        selectEstado.addEventListener('change', filtrarTabla);
    });
</script>