<?php

/**
 * Vista/Tutores/Components/Header_Alumnos.php — Cabecera de la tabla de alumnos (paso 2)
 *
 * Barra de acciones del listado de alumnos: título de sección y dos botones:
 *   - Cargar Alumnos: abre el modal de importación Excel (modalCargarAlumnos).
 *   - Exportar Word: envía el formulario de exportación a Exportar_Alumnos_Word.php
 *     con los IDs seleccionados o exportar_todo=1 si no hay selección.
 *
 * El formulario de exportación y la lógica de selección viven en Steps/Alumnos.php;
 * este componente solo renderiza la barra superior de la tabla.
 */

require_once __DIR__ . '/../../../Seguridad/Control_Accesos.php';

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

<form method="POST" action="index.php?tab=2&pp_alum=<?= (int)($pp_alum ?? 10) ?>" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center" id="form-busqueda-alumno" autocomplete="off">
    <input type="hidden" name="accion" value="mostrarPanel">

    <!-- Search bar dropdown — Alumnos -->
    <div class="flex-1 relative w-full" id="alumno-dropdown-wrapper">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">🔍</span>
        <input
            type="text"
            name="busqueda"
            id="alumno-search-input"
            value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>"
            placeholder="BUSCAR POR APELLIDOS O DNI..."
            autocomplete="off"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase"
        >
        <!-- Dropdown list -->
        <ul id="alumno-dropdown"
            class="hidden absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-72 overflow-y-auto">
        </ul>
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
  <button type="button" onclick="limpiarFormAlumnos(this)"
      class="flex items-center gap-1.5 px-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold text-slate-500 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
      Mostrar todos
  </button>
</form>


<?php
// Ruta base del proyecto (igual que en Convenios.php)
$_alumno_ruta_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$_alumno_ruta_base = preg_replace('/\/Vista(\/Tutores(\/Components)?)?$/i', '', $_alumno_ruta_base);
$alumno_ajax_url   = $_alumno_ruta_base . '/Public/ajax/Autocompletar.php';
?>
<script>

function limpiarFormAlumnos(btn) {
    const f = btn.closest('form');
    f.querySelector('[name=busqueda]').value = '';
    f.querySelector('[name=estado]').value = '';
    f.querySelector('[name=ordenar]').value = 'estado';
    f.submit();
}

// ── Search bar dropdown — Alumnos ──────────────────────────────────────────
(function () {
    const input    = document.getElementById('alumno-search-input');
    const dropdown = document.getElementById('alumno-dropdown');
    const form     = document.getElementById('form-busqueda-alumno');

    const ajaxUrl = '<?= $alumno_ajax_url ?>';

    let timer      = null;
    let activeIndex = -1;

    function mostrarDropdown(sugerencias) {
        dropdown.innerHTML = '';
        activeIndex = -1;

        if (!sugerencias.length) { ocultarDropdown(); return; }

        sugerencias.forEach((s, i) => {
            const li = document.createElement('li');
            li.setAttribute('data-index', i);
            li.className = 'px-5 py-3 cursor-pointer hover:bg-orange-50 transition-colors border-b border-slate-100 last:border-b-0';
            li.innerHTML = `
                <div class="text-[11px] font-black text-slate-800 uppercase tracking-wide">${resaltar(s.etiqueta, input.value)}</div>
                <div class="text-[10px] font-bold text-slate-400 font-mono mt-0.5">${s.sublabel}</div>
            `;
            li.addEventListener('mousedown', (e) => {
                e.preventDefault();
                seleccionar(s.valor);
            });
            dropdown.appendChild(li);
        });

        dropdown.classList.remove('hidden');
    }

    function ocultarDropdown() {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        activeIndex = -1;
    }

    function seleccionar(valor) {
        input.value = valor;
        ocultarDropdown();
        form.submit();
    }

    function resaltar(texto, busqueda) {
        if (!busqueda.trim()) return texto;
        const esc = busqueda.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const re  = new RegExp('(' + esc + ')', 'gi');
        return texto.replace(re, '<mark class="bg-orange-100 text-orange-700 rounded px-0.5">$1</mark>');
    }

    function actualizarResaltado() {
        dropdown.querySelectorAll('li').forEach((li, i) => {
            li.classList.toggle('bg-orange-50', i === activeIndex);
        });
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { ocultarDropdown(); return; }

        timer = setTimeout(async () => {
            try {
                const res  = await fetch(`${ajaxUrl}?tipo=alumno&q=${encodeURIComponent(q)}`);
                const data = await res.json();
                mostrarDropdown(data);
            } catch (e) { ocultarDropdown(); }
        }, 250);
    });

    input.addEventListener('keydown', (e) => {
        const items = dropdown.querySelectorAll('li');
        if (!items.length && e.key !== 'Enter') return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, items.length - 1);
            actualizarResaltado();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, -1);
            actualizarResaltado();
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && items[activeIndex]) {
                e.preventDefault();
                input.value = items[activeIndex].querySelector('div:first-child').textContent.trim();
                ocultarDropdown();
                form.submit();
            }
        } else if (e.key === 'Escape') {
            ocultarDropdown();
        }
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('alumno-dropdown-wrapper').contains(e.target)) {
            ocultarDropdown();
        }
    });

    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 && !dropdown.children.length) {
            input.dispatchEvent(new Event('input'));
        }
    });
})();

</script>
