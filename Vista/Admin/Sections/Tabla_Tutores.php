<?php 

// Vista/Admin/Sections/Tabla_Tutores.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Personal Docente</h2>
        <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            Total registrados: <?= count($tutores) ?>
        </p>
    </div>
    <div class="flex items-center gap-4">
        <form action="index.php" method="POST">
            <input type="hidden" name="accion" value="mostrarPanel">
            <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-orange-600 transition-all cursor-pointer">
                <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
            </button>
        </form>
        <button onclick="document.getElementById('modalAgregarTutor').style.display='flex'" 
                class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md shadow-orange-100 cursor-pointer">
            + Agregar Tutor
        </button>
    </div>
</div>

<form method="POST" action="index.php" class="flex flex-col lg:flex-row gap-4 mb-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 items-center">
    <input type="hidden" name="accion" value="mostrarTutores">

    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR NOMBRE O APELLIDOS..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Ordenar por:</span>
        <select name="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="id"        <?= (!isset($_POST['ordenar']) || $_POST['ordenar'] == 'id') ? 'selected' : '' ?>>Nº REGISTRO (ID)</option>
            <option value="apellidos" <?= ($_POST['ordenar'] ?? '') == 'apellidos' ? 'selected' : '' ?>>APELLIDOS (A-Z)</option>
            <option value="ciclo"     <?= ($_POST['ordenar'] ?? '') == 'ciclo'     ? 'selected' : '' ?>>CURSO Y CICLO</option>
        </select>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Curso:</span>
        <select name="filtro_curso" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="">TODOS LOS CURSOS</option>
            <option value="Primero" <?= ($_POST['filtro_curso'] ?? '') == 'Primero' ? 'selected' : '' ?>>1º CURSO</option>
            <option value="Segundo" <?= ($_POST['filtro_curso'] ?? '') == 'Segundo' ? 'selected' : '' ?>>2º CURSO</option>
        </select>
    </div>

    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold text-[10px] hover:bg-orange-600 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
    <button type="button" onclick="limpiarFormTutores(this)"
        class="flex items-center gap-1.5 px-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold text-slate-500 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide whitespace-nowrap">
        Mostrar todos
    </button>
</form>
<script>
function limpiarFormTutores(btn) {
    const f = btn.closest('form');
    f.querySelector('[name=busqueda]').value = '';
    f.querySelector('[name=filtro_curso]').value = '';
    f.querySelector('[name=ordenar]').value = 'id';
    f.submit();
}
</script>

<!-- Barra superior: contador + config paginación -->
<div class="flex items-center justify-between mb-2">
    <span id="tut-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
    <button type="button" onclick="abrirModalPag('tut')" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span id="tut-pag-label">10/pág</span>
    </button>
</div>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-slate-700">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">ID</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-32">DNI</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tutor</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Información de Contacto</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-48">Ciclo</th>
                <th class="py-5 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-28">Acciones</th>
            </tr>
        </thead>
        <tbody id="tut-tbody" class="divide-y divide-slate-100">
            <?php if (empty($tutores)): ?>
                <tr>
                    <td colspan="6" class="py-20 text-center">
                        <p class="text-slate-400 text-sm italic font-medium">No se han encontrado tutores registrados.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tutores as $fila): ?>
                <tr class="tut-fila hover:bg-slate-50/40 transition-all duration-200 group">
                    <td class="py-5 px-6">
                        <span class="font-mono text-[11px] font-bold text-slate-300 group-hover:text-orange-400 transition-colors">#<?= $fila['id_tutor'] ?></span>
                    </td>
                    <td class="py-5 px-6">
                        <span class="font-mono text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-1 rounded-md"><?= $fila['dni'] ?></span>
                    </td>
                    <td class="py-5 px-6">
                        <span class="font-bold text-slate-800 text-sm uppercase tracking-tight">
                            <?= $fila['apellidos'] ?> <?= $fila['nombre'] ?> 
                        </span>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="font-medium"><?= $fila['email'] ?></span>
                            <span class="text-slate-200">|</span>
                            <span class="font-bold text-slate-400"><?= $fila['telefono'] ?></span>
                        </div>
                    </td>
                    <td class="py-5 px-6">
                        <?php if (!empty($fila['nombre_ciclo'])): ?>
                            <?php 
                                $cursoLimpio = mb_strtolower(trim($fila['nombre_curso']));
                                $abreviatura = ($cursoLimpio == 'primero') ? "1º" : (($cursoLimpio == 'segundo') ? "2º" : $fila['nombre_curso']);
                            ?>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-50 border border-orange-100 text-orange-700">
                                <span class="text-[11px] font-black italic"><?= $abreviatura ?></span>
                                <span class="text-[10px] font-bold uppercase tracking-tight"><?= $fila['nombre_ciclo'] ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-300 text-[10px] font-bold uppercase tracking-widest">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-5 px-6">
                        <div class="flex justify-center gap-1">
                            <button onclick='abrirEditarTutor(<?= json_encode($fila) ?>)' 
                                    title="Editar" 
                                    class="p-2 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button type="button" 
                                    onclick='abrirModalEliminar(<?= json_encode($fila) ?>)' 
                                    title="Eliminar" 
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="tut-paginacion" class="hidden flex items-center justify-center mt-3 gap-1.5">
    <button id="tut-prev" onclick="tutCambiarPagina(tutPaginaActual - 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Anterior
    </button>
    <div id="tut-paginas" class="flex items-center gap-1.5"></div>
    <button id="tut-next" onclick="tutCambiarPagina(tutPaginaActual + 1)"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
        Siguiente
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<script>
// ─── PAGINACIÓN: PERSONAL DOCENTE ─────────────────────────────────────────────
let tutPorPagina = _leerPagStorage('tut');
let tutPaginaActual = 1;

function tutInicializar() {
    const filas = Array.from(document.querySelectorAll('#tut-tbody .tut-fila'));
    const total = filas.length;
    const label = document.getElementById('tut-pag-label');
    if (label) label.textContent = tutPorPagina === 0 ? 'Todos' : tutPorPagina + '/pág';
    const pag = document.getElementById('tut-paginacion');
    const contador = document.getElementById('tut-contador');
    if (tutPorPagina === 0 || total <= tutPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} tutor${total !== 1 ? 'es' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    tutRenderizar();
}

function tutCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#tut-tbody .tut-fila');
    const totalPaginas = Math.ceil(filas.length / tutPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    tutPaginaActual = nuevaPagina;
    tutRenderizar();
}

function tutRenderizar() {
    const filas = Array.from(document.querySelectorAll('#tut-tbody .tut-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / tutPorPagina);
    const inicio = (tutPaginaActual - 1) * tutPorPagina;
    const fin    = Math.min(inicio + tutPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('tut-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('tut-prev').disabled = tutPaginaActual === 1;
    document.getElementById('tut-next').disabled = tutPaginaActual === totalPaginas;

    const contenedor = document.getElementById('tut-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, tutPaginaActual, tutPaginaActual - 1, tutPaginaActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas));
    [...pagsMostrar].sort((a, b) => a - b).forEach((p, idx, arr) => {
        const prev = arr[idx - 1];
        if (prev !== undefined && p - prev > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => tutCambiarPagina(p);
        btn.className = p === tutPaginaActual
            ? 'w-8 h-8 rounded-lg bg-orange-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', tutInicializar);

window._pagCallbacks['tut'] = function(n) { tutPorPagina = n; tutPaginaActual = 1; tutInicializar(); };
// ─────────────────────────────────────────────────────────────────────────────
</script>

<?php $pag_prefix = 'tut'; $pag_color = 'orange'; include 'Vista/Shared/Modal_Paginacion.php'; ?>

<?php include 'Vista/Admin/Components/Modales_Tutores.php'; ?>