<?php 
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
        <tr class="hover:bg-slate-50/50 transition-colors">
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
            // Caso 1: Ya está firmado en la DB
            if (elemento) {
                elemento.checked = true;
                elemento.disabled = true;
            }
            document.getElementById('nombreAlumnoFirmado').innerText = nombre;
            document.getElementById('modalYaFirmado').style.display = 'flex';
        } else {
            // Caso 2: No está firmado, procedemos al modal naranja
            document.getElementById('modalFirmaNombre').innerText = nombre;
            document.getElementById('modalConfirmarFirma').style.display = 'flex';
            window.checkboxActual = elemento; 

            const btnConfirmar = document.getElementById('btnConfirmarFirmaAccion');
            btnConfirmar.onclick = function() {
                const f = document.createElement('form');
                f.method = 'POST';
                f.action = 'index.php';
                f.innerHTML = `
                    <input type="hidden" name="accion" value="firmarAlumno">
                    <input type="hidden" name="id_asignacion" value="${idAsig}">
                    <input type="hidden" name="enviado_estado" value="${enviado}">
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
        alert("Por favor, selecciona al menos un alumno.");
        return;
    }

    // Cerramos el selector y abrimos la confirmación
    document.getElementById('modalSeleccionarExportar').style.display = 'none';
    document.getElementById('modalConfirmarExportar').style.display = 'flex';
}

function mostrarErrorExportar(nombreAlumno, checkbox) {
    // Si el checkbox no está deshabilitado (es decir, no ha sido enviado realmente aún)
    if (!checkbox.disabled) {
        // Forzamos que se mantenga desmarcado
        checkbox.checked = false;
        
        // Inyectamos el nombre en el modal de exportación
        const spanNombre = document.getElementById('nombreAlumnoExportError');
        if (spanNombre) spanNombre.innerText = nombreAlumno;
        
        // Mostramos el modal
        const modal = document.getElementById('modalErrorExportar');
        if (modal) modal.style.display = 'flex';
    }
    return false;
}

</script>