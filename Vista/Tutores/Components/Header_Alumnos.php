<?php

// Vista/Tutores/Components/Header_Alumnos.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

?>
<div class="flex justify-between items-center mb-8">
  <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white text-sm">👥</span>
    Listado de Alumnado
  </h2>
  <div class="flex gap-2">
    <button onclick="document.getElementById('modalCargarAlumnos').style.display='flex'"
          class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md flex items-center gap-2 cursor-pointer">
      📥 Cargar Alumnos
    </button>
    <button onclick="document.getElementById('modalSeleccionarExportar').style.display='flex'" 
            class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
        📤 Exportar Alumnos
    </button>
    <button onclick="document.getElementById('modalAgregarAlumno').style.display='flex'"
            class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
      + Agregar Alumno
    </button>
  </div>
</div>

<form method="POST" action="index.php?controlador=Tutores&accion=mostrarPanel&tab=2" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center">
  <div class="flex-1 relative w-full">
    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
    <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR APELLIDOS O DNI..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
  </div>
  
  <div class="flex items-center gap-3 w-full md:w-auto">
    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Ordenar por:</span>
    <select name="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
        <option value="estado"          <?= ($_POST['ordenar'] ?? '') == 'estado'           ? 'selected' : '' ?>>ESTADO</option>
        <option value="nombre"          <?= ($_POST['ordenar'] ?? '') == 'nombre'           ? 'selected' : '' ?>>ALUMNO</option>
        <option value="mis_convenios"  <?= ($_POST['ordenar'] ?? '') == 'mis_convenios'   ? 'selected' : '' ?>>CONVENIO</option>
        <option value="fecha_inicio"   <?= ($_POST['ordenar'] ?? '') == 'fecha_inicio'     ? 'selected' : '' ?>>FECHA INICIO</option>
        <option value="fecha_final"    <?= ($_POST['ordenar'] ?? '') == 'fecha_final'      ? 'selected' : '' ?>>FECHA FINAL</option>
    </select>
  </div>

  <div class="flex items-center gap-3 w-full md:w-auto">
    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
    <select name="estado" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
      <option value="">TODOS LOS ESTADOS</option>
      <option value="SIN ASIGNAR" <?= ($_POST['estado'] ?? '') == 'SIN ASIGNAR' ? 'selected' : '' ?>>🔴 SIN ASIGNAR</option>
      <option value="EN PROCESO" <?= ($_POST['estado'] ?? '') == 'EN PROCESO' ? 'selected' : '' ?>>🟡 EN PROCESO</option>
      <option value="COMPLETADO" <?= ($_POST['estado'] ?? '') == 'COMPLETADO' ? 'selected' : '' ?>>🟢 COMPLETADO</option>
    </select>
  </div>

  <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-[10px] hover:bg-slate-800 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
    BUSCAR
  </button>
</form>


<script>

// Este script funciona para no tener que estar dandole a buscar todo el rato
// lo hace automático. Pero hay detalles por pulir, y el de arriba de debe cambiar,
// no basta sólo con descomentar lo de abajo.

/*

document.addEventListener('DOMContentLoaded', () => {
    const busqueda = document.getElementById('busquedaAlumnos');
    const estado = document.getElementById('estadoAlumnos');
    const ordenar = document.getElementById('ordenarAlumnos');
    const tabla = document.getElementById('tablaAlumnosBody');

    if (!tabla) return;

    // --- FILTRADO ---
    const filtrar = () => {
        const txt = busqueda.value.toLowerCase().trim();
        const est = estado.value.toLowerCase().trim();
        const filas = Array.from(tabla.querySelectorAll('tr'));

        filas.forEach(f => {
            const contenido = f.textContent.toLowerCase();
            const coincideTxt = txt === '' || contenido.includes(txt);
            const coincideEst = est === '' || contenido.includes(est);
            f.style.display = (coincideTxt && coincideEst) ? "" : "none";
        });
    };

    // --- ORDENACIÓN ---
    const reordenar = () => {
        const crit = ordenar.value;
        const filas = Array.from(tabla.querySelectorAll('tr'));
        
        // Mapeo de columnas: Si notas que ordena la que no es, cambia el número aquí.
        const cols = { 
            'estado': 0, 
            'nombre': 1, 
            'mis_convenios': 2, 
            'fecha_inicio': 3, 
            'fecha_final': 4 
        };
        
        const idx = cols[crit] !== undefined ? cols[crit] : 1;

        filas.sort((a, b) => {
            let tA = a.children[idx]?.innerText.trim() || "";
            let tB = b.children[idx]?.innerText.trim() || "";

            // Limpieza extra para fechas (elimina cualquier carácter que no sea número o /)
            if (crit === 'fecha_inicio' || crit === 'fecha_final') {
                const limpiarFecha = (s) => s.replace(/[^0-9/]/g, '').split('/').reverse().join('');
                const fA = limpiarFecha(tA);
                const fB = limpiarFecha(tB);
                return fA.localeCompare(fB);
            }

            // Para Estado y Nombre, comparamos normal
            return tA.localeCompare(tB, 'es', { sensitivity: 'base', numeric: true });
        });

        // Reinsertar de forma atómica para que el navegador "despierte"
        filas.forEach(f => tabla.appendChild(f));
    };

    // --- EVENTOS ---
    busqueda.addEventListener('input', filtrar);
    estado.addEventListener('change', filtrar);
    
    ordenar.addEventListener('change', () => {
        reordenar();
        filtrar(); 
    });

    // Forzar una ordenación inicial para que "Estado" funcione desde el primer clic
    reordenar();

    const form = document.getElementById('formAlumnos');
    if (form) {
        form.onsubmit = (e) => {
            e.preventDefault();
            filtrar();
        };
    }
});

*/

</script>