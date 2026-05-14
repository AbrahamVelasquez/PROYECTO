<?php 

// Vista/Tutores/Steps/Alumnos.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

// Incluimos el Header (Título y Filtros)
include __DIR__ . '/../Components/Header_Alumnos.php'; 

?>
<style>
    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        
        /* Lo posicionamos a la izquierda del candado */
        top: 50%;
        right: 125%; 
        transform: translateY(-50%);
        
        background: #1e293b; 
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: bold;
        z-index: 9999;
        
        /* Caja más estable */
        white-space: normal;
        width: 160px;
        text-align: center;
        line-height: 1.4;
        text-transform: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        pointer-events: none;
    }

    /* Flechita apuntando al candado (derecha) */
    [data-tooltip]:hover::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 105%;
        transform: translateY(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent transparent #1e293b;
        z-index: 9999;
    }
</style>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
  <table class="w-full text-left border-collapse bg-white">
    <thead>
      <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase">
        <th class="p-4 w-12 text-center">EDITAR</th>
        <th class="p-4">APELLIDOS, NOMBRE ALUMNO</th>
        <th class="w-10 text-center">SEXO</th>
        <th class="w-24 border-section text-center">DNI / NIE</th>
        <th class="p-4">NOMBRE EMPRESA</th>
        <th class="w-16 text-center">Nº CONV.</th>
        <th class="border-section p-4">DIRECCIÓN CENTRO TRABAJO</th>
        <th class="w-24 text-center">F. INICIO</th>
        <th class="w-24 text-center">F. FINAL</th>
        <th class="w-28 text-center">HORARIO</th>
        <th class="w-14 border-section text-center text-[9px]">H/DÍA</th>
        <th class="w-24 text-center p-4">ESTADO</th> 
        <th class="w-16 text-center">ENVIADO</th>
        <th class="w-16 border-section text-center">FIRMADO</th>
      </tr>
    </thead>
    <!-- id="tablaAlumnosBody" eso se colocó para funcionalidades de javascript que se usan en el header, para búsqueda -->
    <tbody id="tablaAlumnosBody" class="divide-y divide-slate-100 uppercase bg-white text-[10px]">
      <?php if (empty($alumnos)): ?>
        <tr><td colspan="14" class="py-10 text-center text-slate-400 italic">No hay resultados.</td></tr>
      <?php else: ?>
        <?php foreach ($alumnos as $al): 
            $tieneEmpresa = !empty($al['id_convenio']);
            $tieneDireccion = !empty($al['direccion']);
            $f_inicio = ($al['fecha_inicio'] && $al['fecha_inicio'] !== '0000-00-00') ? $al['fecha_inicio'] : null;
            $f_final = ($al['fecha_final'] && $al['fecha_final'] !== '0000-00-00') ? $al['fecha_final'] : null;
            $tieneFechas = ($f_inicio && $f_final);
            $tieneHorario = (!empty($al['horario']) && !empty($al['horas_dia']) && $al['horas_dia'] > 0);

            if (!$tieneEmpresa) {
                $estado = "SIN ASIGNAR"; $colorEstado = "bg-red-100 text-red-700 border-red-200";
            } elseif (!$tieneDireccion || !$tieneFechas || !$tieneHorario) {
                $estado = "EN PROCESO"; $colorEstado = "bg-amber-100 text-amber-700 border-amber-200";
            } else {
                $estado = "COMPLETADO"; $colorEstado = "bg-emerald-100 text-emerald-700 border-emerald-200";
            }
        ?>
        <tr class="alum-fila hover:bg-slate-50/50 transition-colors" data-id-alumno="<?= $al['id_alumno'] ?>" data-estado="<?= $estado ?>">
            <td class="p-3 text-center">
                <button type="button" onclick="abrirModalEditar(<?= $al['id_alumno'] ?>)" class="group p-2 rounded-lg hover:bg-orange-50 transition-all cursor-pointer border border-transparent hover:border-orange-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 group-hover:text-orange-600">
                      <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                    </svg>
                </button>
            </td>
            <td class="font-bold p-4 text-slate-700"><?= htmlspecialchars($al['apellido1'] . " " . $al['apellido2'] . ", " . $al['nombre']) ?></td>
            <td class="text-center text-slate-500"><?= $al['sexo'] ?? '-' ?></td>
            <td class="text-center font-mono border-section text-slate-600"><?= $al['dni'] ?></td>

            <?php if (!$tieneEmpresa): ?>
                <td colspan="7" class="text-center bg-red-50/30 text-red-500 border-section tracking-[0.2em] font-black italic py-4">
                    ⚠️ PENDIENTE DE ASIGNACIÓN
                </td>
                <td class="text-center p-4">
                    <span class="<?= $colorEstado ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
                        <?= $estado ?>
                    </span>
                </td>
                
                <td class="text-center">
                    <span class="text-slate-500 inline-flex items-center justify-center cursor-help" data-tooltip="Primero debe asignar un convenio al alumno">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                </td>
                <td class="text-center border-section">
                    <span class="text-slate-500 inline-flex items-center justify-center cursor-help" data-tooltip="Primero debe asignar un convenio al alumno">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                </td>
            <?php else: ?>
                <td class="p-4 text-slate-700"><?= htmlspecialchars($al['nombre_empresa']) ?></td>
                <td class="text-center text-slate-500"><?= str_pad($al['id_convenio'], 4, "0", STR_PAD_LEFT) ?></td>
                <td class="border-section p-4">
                    <?= $tieneDireccion ? '<div class="text-[9px] lowercase leading-tight text-slate-600">'.htmlspecialchars($al['direccion']).'<br><span class="font-bold text-slate-400">'.htmlspecialchars($al['municipio']).'</span></div>' 
                                      : '<span class="text-orange-500 font-black italic text-[8px]">⚠️ FALTA DIR.</span>' ?>
                </td>
                <td class="text-center"><?= $f_inicio ? date("d/m/y", strtotime($f_inicio)) : '<span class="text-orange-500 font-bold italic">--/--/--</span>' ?></td>
                <td class="text-center"><?= $f_final ? date("d/m/y", strtotime($f_final)) : '<span class="text-orange-500 font-bold italic">--/--/--</span>' ?></td>
                <td class="text-center">
                    <?= $tieneHorario ? '<span class="text-slate-600">'.htmlspecialchars($al['horario']).'</span>' 
                                     : '<span class="text-orange-500 font-black italic text-[8px]">⚠️ SIN HORARIO</span>' ?>
                </td>
                <td class="text-center border-section font-bold">
                    <?= $tieneHorario ? number_format($al['horas_dia'], 0) : '-' ?>
                </td>

                <td class="text-center p-4">
                  <span class="<?= $colorEstado ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
                      <?= $estado ?>
                  </span>
                </td>

                <?php 
                    $estaFirmado = $al['firmado']; 
                    $estaEnviado = $al['enviado']; 
                    $esCompletado = ($estado === "COMPLETADO");
                    
                    $mensajeBloqueo = ($estado === "SIN ASIGNAR") 
                        ? "Primero debe asignar un convenio al alumno" 
                        : "Primero debe completar todos los datos (fechas, horario, horas...)";
                ?>

                <td class="px-4 py-3 text-center">
                    <?php if ($esCompletado): ?>
                        <input type="checkbox" 
                            class="w-4 h-4 rounded border-slate-500 text-orange-600 focus:ring-orange-500 accent-orange-600 <?= $estaEnviado ? 'opacity-50' : 'cursor-pointer' ?>" 
                            <?= $estaEnviado ? 'checked disabled' : '' ?>
                            onclick="mostrarErrorExportar('<?= addslashes($al['nombre']) ?>', this);"> 
                    <?php else: ?>
                        <span class="text-slate-500 inline-flex items-center justify-center cursor-help" data-tooltip="<?= $mensajeBloqueo ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                    <?php endif; ?>
                </td>

                <td class="px-4 py-3 text-center border-section">
                    <?php if ($esCompletado): ?>
                        <input type="checkbox" 
                            <?= $estaFirmado ? 'checked disabled' : '' ?> 
                            onclick="prepararFirma(<?= $al['id_asignacion'] ?>, <?= $estaEnviado ?>, '<?= addslashes($al['nombre']) ?>', this)"
                            class="w-4 h-4 rounded border-slate-500 text-emerald-600 focus:ring-emerald-500 <?= $estaFirmado ? 'opacity-50' : 'cursor-pointer' ?>">
                    <?php else: ?>
                        <span class="text-slate-500 inline-flex items-center justify-center cursor-help" data-tooltip="<?= $mensajeBloqueo ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-60"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Barra paginación: Alumnos -->
<div class="flex items-center justify-between mt-3">
    <span id="alum-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
    <div id="alum-paginacion" class="hidden flex items-center gap-1.5">
        <button id="alum-prev" onclick="alumCambiarPagina(alumPaginaActual - 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Anterior
        </button>
        <div id="alum-paginas" class="flex items-center gap-1.5"></div>
        <button id="alum-next" onclick="alumCambiarPagina(alumPaginaActual + 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
    <button type="button" onclick="abrirModalPag('alum')" title="Configurar filas por página"
        class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span id="alum-pag-label">10/pág</span>
    </button>
</div>

<?php
// Incluimos todos los Modales
include __DIR__ . '/../Components/Modales_Alumnos.php';
?>

<script>
let checkboxPendiente = null;

function prepararFirma(idAsig, enviado, nombre, elemento) {
    if (enviado == 0) {
        if (elemento) elemento.checked = false;
        document.getElementById('nombreAlumnoError').innerText = nombre;
        document.getElementById('modalErrorFirma').style.display = 'flex';
        return false;
    }

    // Consultamos al controlador si ya existe en asignaciones_firmadas
    fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `accion=obtenerAlumno&id_asignacion=${idAsig}&verificar_firma=true`
    })
    .then(r => r.json())
    .then(res => {
        if (res.yaFirmado) {
            if (elemento) {
                elemento.checked = true;
                elemento.disabled = true;
            }
            document.getElementById('nombreAlumnoFirmado').innerText = nombre;
            document.getElementById('modalYaFirmado').style.display = 'flex';
        } else {
            document.getElementById('modalFirmaNombre').innerText = nombre;
            
            // --- CAMBIO AQUÍ: Limpiamos el input del anexo antes de mostrar el modal ---
            document.getElementById('inputFirmaAnexo').value = ''; 
            
            document.getElementById('modalConfirmarFirma').style.display = 'flex';
            window.checkboxActual = elemento; 

            const btnConfirmar = document.getElementById('btnConfirmarFirmaAccion');
            btnConfirmar.onclick = function() {
                // --- CAMBIO AQUÍ: Capturamos el valor que el usuario escribió ---
                const valorAnexo = document.getElementById('inputFirmaAnexo').value;

                const f = document.createElement('form');
                f.method = 'POST';
                f.action = 'index.php';
                f.innerHTML = `
                    <input type="hidden" name="accion" value="firmarAlumno">
                    <input type="hidden" name="id_asignacion" value="${idAsig}">
                    <input type="hidden" name="enviado_estado" value="${enviado}">
                    <input type="hidden" name="anexo" value="${valorAnexo}">
                `;
                document.body.appendChild(f);
                f.submit();
            };
        }
    })
    .catch(e => console.error("Error en validación de firma:", e));
}

// Función para cerrar y limpiar si cancelan
function cerrarModalFirma() {
    document.getElementById('modalConfirmarFirma').style.display = 'none';
    // --- CAMBIO AQUÍ: También limpiamos el input al cerrar por seguridad ---
    document.getElementById('inputFirmaAnexo').value = ''; 
    if (window.checkboxActual) window.checkboxActual.checked = false;
}

// Variable global para controlar el estado de firma del alumno abierto en el modal
let global_alumnoYaFirmado = false;

function abrirModalEditar(idAlumno) {
    fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=obtenerAlumno&id_alumno=' + idAlumno
    })
    .then(r => r.json())
    .then(al => {
        // Sincronizar nombre para el modal de advertencia
        document.getElementById('nombreAlumnoFirmado').innerText = (al.nombre || '') + ' ' + (al.apellido1 || '');
        
        // --- ADICIÓN QUIRÚRGICA: Guardar estado de firma ---
        global_alumnoYaFirmado = al.yaFirmado ?? false;
        // --------------------------------------------------

        document.getElementById('edit_id_alumno').value = al.id_alumno;
        document.getElementById('edit_apellido1').value = al.apellido1 ?? '';
        document.getElementById('edit_apellido2').value = al.apellido2 ?? '';
        document.getElementById('edit_nombre').value = al.nombre ?? '';
        document.getElementById('edit_dni').value = al.dni ?? '';
        document.getElementById('edit_sexo').value = al.sexo ?? '';
        document.getElementById('edit_correo').value = al.correo ?? '';
        document.getElementById('edit_telefono').value = al.telefono || '';
        document.getElementById('edit_id_convenio').value = al.id_convenio ?? '';
        document.getElementById('edit_fecha_inicio').value = al.fecha_inicio && al.fecha_inicio !== '0000-00-00' ? al.fecha_inicio : '';
        document.getElementById('edit_fecha_final').value = al.fecha_final && al.fecha_final !== '0000-00-00' ? al.fecha_final : '';
        document.getElementById('edit_horario').value = al.horario ?? '';
        document.getElementById('edit_horas_dia').value = al.horas_dia ?? '';
        document.getElementById('edit_horas_totales').value = al.num_total_horas ?? '';
        document.getElementById('edit_nombre_tutor_empresa').value = al.nombre_tutor_empresa ?? '';
        document.getElementById('edit_correo_tutor_empresa').value = al.correo_tutor_empresa ?? '';
        document.getElementById('edit_tel_tutor_empresa').value = al.tel_tutor_empresa ?? '';

        const excepcionesJson = al.horario_excepciones ?? '';
        document.getElementById('edit_horario_excepciones').value = excepcionesJson;
        if (typeof haRestaurarResumenEdicion === 'function') haRestaurarResumenEdicion(excepcionesJson);

        const bloque = document.getElementById('bloque_enviado');
        const checkbox = document.getElementById('edit_enviado');

        if (al.enviado == 1) {
            bloque.style.display = 'flex';
            checkbox.checked = true;
        } else {
            bloque.style.display = 'none';
            checkbox.checked = false;
        }

        document.getElementById('modalEditarAlumno').style.display = 'flex';
    })
    .catch(e => alert('Error al cargar datos del alumno'));
}

// --- Listener para bloquear el desmarcado si ya está firmado ---
document.getElementById('edit_enviado').addEventListener('click', function(e) {
    if (global_alumnoYaFirmado && !this.checked) {
        // Impedir que se desmarque
        this.checked = true; 
        // Mostrar tu modal de aviso 🔒
        document.getElementById('modalYaFirmado').style.display = 'flex';
    }
});

function abrirConfirmacionFinal() {
    const seleccionados = document.querySelectorAll('input[name="exportar_ids[]"]:checked');

    if (seleccionados.length === 0) {
        document.getElementById('modalSinSeleccion').style.display = 'flex';
        return;
    }

    // Cerramos el selector y abrimos la confirmación
    document.getElementById('modalSeleccionarExportar').style.display = 'none';
    document.getElementById('modalConfirmarExportar').style.display = 'flex';
}

function seleccionarTodosExportar(master) {
    document.querySelectorAll('#formExportar input[name="exportar_ids[]"]').forEach(cb => {
        cb.checked = master.checked;
    });
}

function mostrarErrorExportar(nombreAlumno, checkbox) {
    if (!checkbox.disabled) {
        checkbox.checked = false;
        const spanNombre = document.getElementById('nombreAlumnoExportError');
        if (spanNombre) spanNombre.innerText = nombreAlumno;
        const modal = document.getElementById('modalErrorExportar');
        if (modal) modal.style.display = 'flex';
    }
    return false;
}

// ─── PAGINACIÓN: ALUMNOS ─────────────────────────────────────────────────────
let alumPorPagina = parseInt(localStorage.getItem('pag_alum_porPagina')) || 10;
let alumPaginaActual = 1;

function alumInicializar() {
    const filas = Array.from(document.querySelectorAll('#tablaAlumnosBody .alum-fila'));
    const total = filas.length;
    const label = document.getElementById('alum-pag-label');
    if (label) label.textContent = alumPorPagina + '/pág';
    const pag = document.getElementById('alum-paginacion');
    const contador = document.getElementById('alum-contador');
    if (total <= alumPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} alumno${total !== 1 ? 's' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    alumRenderizar();
}

function alumCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#tablaAlumnosBody .alum-fila');
    const totalPaginas = Math.ceil(filas.length / alumPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    alumPaginaActual = nuevaPagina;
    alumRenderizar();
}

function alumRenderizar() {
    const filas = Array.from(document.querySelectorAll('#tablaAlumnosBody .alum-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / alumPorPagina);
    const inicio = (alumPaginaActual - 1) * alumPorPagina;
    const fin    = Math.min(inicio + alumPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('alum-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('alum-prev').disabled = alumPaginaActual === 1;
    document.getElementById('alum-next').disabled = alumPaginaActual === totalPaginas;

    const contenedor = document.getElementById('alum-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, alumPaginaActual, alumPaginaActual - 1, alumPaginaActual + 1]
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
        btn.onclick = () => alumCambiarPagina(p);
        btn.className = p === alumPaginaActual
            ? 'w-8 h-8 rounded-lg bg-orange-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', alumInicializar);

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['alum'] = function(n) { alumPorPagina = n; alumPaginaActual = 1; alumInicializar(); };

function abrirModalPag(prefix) {
    const val = parseInt(localStorage.getItem('pag_' + prefix + '_porPagina')) || 10;
    document.getElementById('input-pag-' + prefix).value = val;
    document.getElementById('modal-pag-' + prefix).style.display = 'flex';
}
function cerrarModalPag(prefix) {
    document.getElementById('modal-pag-' + prefix).style.display = 'none';
}
function setPagPreset(prefix, n) {
    document.getElementById('input-pag-' + prefix).value = n;
}
function aplicarPag(prefix) {
    const val = parseInt(document.getElementById('input-pag-' + prefix).value);
    if (!val || val < 1) return;
    localStorage.setItem('pag_' + prefix + '_porPagina', val);
    const label = document.getElementById(prefix + '-pag-label');
    if (label) label.textContent = val + '/pág';
    cerrarModalPag(prefix);
    if (window._pagCallbacks[prefix]) window._pagCallbacks[prefix](val);
}
// ─────────────────────────────────────────────────────────────────────────────

</script>

<!-- ─── Modal Configurar Paginación: Alumnos ──────────────────────────────── -->
<div id="modal-pag-alum" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('alum')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('alum')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('alum', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('alum', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('alum', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('alum', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('alum', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('alum', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-alum" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('alum')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('alum')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('alum')" class="px-4 py-2 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>