<div class="flex justify-between items-center mb-6 mt-2"> 
    <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600 text-white text-sm">📋</span>
        Gestión de Planes de Formación
    </h2>
</div>

<form id="formFiltros" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" id="busqueda" 
            placeholder="BUSCAR POR NOMBRE O EMPRESA..." 
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Ordenar por:</span>
        <select id="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="alumno">ALUMNO</option>
            <option value="empresa">EMPRESA</option>
            <option value="estado">ESTADO</option>
        </select>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
        <select id="filtroEstado" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="todos">TODOS</option>
            <option value="exportado">🟢 EXPORTADO</option>
            <option value="no exportado">🔴 NO EXPORTADO</option>
        </select>
    </div>

    <button type="button" id="btnBuscar" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-[10px] hover:bg-slate-800 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
</form>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
    <table class="w-full text-left border-collapse bg-white table-fixed">
        <thead>
            <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase tracking-wider">
                <th class="p-4 w-24 text-center">EDITAR</th>
                <th class="p-4 w-1/3">ALUMNO</th>
                <th class="p-4 w-1/3">EMPRESA</th>
                <th class="p-4 w-32 text-center">ESTADO ENVÍO</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 uppercase bg-white text-[10px]" id="tablaCuerpo">
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="p-3 text-center">
                    <button onclick="mostrarEdicion({id: 1, nombre: 'Zaragoza, Alberto'})" 
                        class="group p-2 rounded-lg hover:bg-orange-50 transition-all border border-transparent hover:border-orange-100 mx-auto flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-orange-600">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                        </svg>
                    </button>
                </td>
                <td class="p-4 font-bold text-slate-700">Zaragoza, Alberto</td>
                <td class="p-4 text-slate-600 text-[9px]">Inditex S.A.</td>
                <td class="p-4 text-center">
                    <span class="px-3 py-1 rounded-full text-[8px] border font-black bg-emerald-100 text-emerald-700 border-emerald-200">EXPORTADO</span>
                </td>
            </tr>
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="p-3 text-center">
                    <button onclick="mostrarEdicion({id: 2, nombre: 'Alonso, Beatriz'})" 
                        class="group p-2 rounded-lg hover:bg-orange-50 transition-all border border-transparent hover:border-orange-100 mx-auto flex items-center justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-orange-600">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                        </svg>
                    </button>
                </td>
                <td class="p-4 font-bold text-slate-700">Alonso, Beatriz</td>
                <td class="p-4 text-slate-600 text-[9px]">Sistemas Globales</td>
                <td class="p-4 text-center">
                    <span class="px-3 py-1 rounded-full text-[8px] border font-black bg-red-100 text-red-700 border-red-200">NO EXPORTADO</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnBuscar = document.querySelector('#btnBuscar');
        const selectOrdenar = document.querySelector('#ordenar');
        const tablaCuerpo = document.querySelector('#tablaCuerpo');

        const realizarBusqueda = () => {
            const texto = document.querySelector('#busqueda').value.toLowerCase();
            const estadoFiltro = document.querySelector('#filtroEstado').value.toLowerCase();
            const filas = Array.from(tablaCuerpo.querySelectorAll('tr'));

            filas.forEach(fila => {
                const nombre = fila.children[1].textContent.toLowerCase();
                const empresa = fila.children[2].textContent.toLowerCase();
                const estado = fila.children[3].textContent.toLowerCase();

                const coincideTexto = nombre.includes(texto) || empresa.includes(texto);
                const coincideEstado = estadoFiltro === 'todos' || estado.includes(estadoFiltro);

                fila.style.display = (coincideTexto && coincideEstado) ? "" : "none";
            });
        };

        const ordenarTabla = () => {
            const criterio = selectOrdenar.value;
            const filas = Array.from(tablaCuerpo.querySelectorAll('tr'));
            
            const indice = {
                'alumno': 1,
                'empresa': 2,
                'estado': 3
            }[criterio];

            filas.sort((a, b) => {
                const valA = a.children[indice].textContent.trim();
                const valB = b.children[indice].textContent.trim();
                return valA.localeCompare(valB);
            });

            filas.forEach(fila => tablaCuerpo.appendChild(fila));
        };

        btnBuscar.addEventListener('click', realizarBusqueda);
        selectOrdenar.addEventListener('change', ordenarTabla);
        
        document.querySelector('#busqueda').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                realizarBusqueda();
            }
        });
    });
</script>