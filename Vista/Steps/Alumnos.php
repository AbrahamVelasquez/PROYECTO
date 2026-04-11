<?php 
// Incluimos el Header (Título y Filtros)
include __DIR__ . '/../Components/Header_Alumnos.php'; 
?>

<form id="formExportar" method="POST" action="index.php?controlador=Tutores&accion=exportarAlumnos">
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
        <th class="w-24 text-center p-4">ESTADO</th> <th class="w-16 text-center">ENVIADO</th>
        <th class="w-16 border-section text-center">FIRMADO</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 uppercase bg-white text-[10px]">
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
                <td class="text-center">-</td>
                <td class="text-center border-section">-</td>
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

                <td class="text-center">
                    <?php if ($estado === "COMPLETADO"): ?>
                        <input type="checkbox" name="exportar_id[]" value="<?= $al['id_alumno'] ?>"
                               class="w-4 h-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 cursor-pointer accent-orange-600" 
                               <?= ($al['enviado'] ?? false) ? 'checked' : '' ?>>
                    <?php else: ?>
                        <span class="text-slate-400">-</span>
                    <?php endif; ?>
                </td>

                <td class="text-center border-section">
                    <?php if ($estado === "COMPLETADO"): ?>
                        <input type="checkbox" 
                               class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer accent-emerald-600" 
                               <?= ($al['firmado'] ?? false) ? 'checked' : '' ?>
                               onclick="solicitarConfirmacionFirma(<?= $al['id_alumno'] ?>, '<?= htmlspecialchars($al['apellido1'] . ' ' . $al['nombre']) ?>', this)">
                    <?php else: ?>
                        <span class="text-slate-400">-</span>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</form>

<?php 
// Incluimos todos los Modales
include __DIR__ . '/../Components/Modales_Alumnos.php'; 
?>

<script>
let checkboxPendiente = null;

function solicitarConfirmacionFirma(idAlumno, nombreCompleto, checkbox) {
    if (!checkbox.checked) return;
    checkbox.checked = false;
    checkboxPendiente = checkbox;
    document.getElementById('modalFirmaNombre').textContent = nombreCompleto;
    document.getElementById('modalConfirmarFirma').style.display = 'flex';
    document.getElementById('btnConfirmarFirmaAccion').onclick = function() {
        if (checkboxPendiente) { checkboxPendiente.checked = true; }
        cerrarModalFirma();
    };
}

function cerrarModalFirma() {
    document.getElementById('modalConfirmarFirma').style.display = 'none';
    checkboxPendiente = null;
}

function abrirModalEditar(idAlumno) {
    fetch('index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=obtenerAlumno&id_alumno=' + idAlumno
    })
    .then(r => r.json())
    .then(al => {
        document.getElementById('edit_id_alumno').value = al.id_alumno;
        document.getElementById('edit_apellido1').value = al.apellido1 ?? '';
        document.getElementById('edit_apellido2').value = al.apellido2 ?? '';
        document.getElementById('edit_nombre').value = al.nombre ?? '';
        document.getElementById('edit_dni').value = al.dni ?? '';
        document.getElementById('edit_sexo').value = al.sexo ?? '';
        document.getElementById('edit_correo').value = al.correo ?? '';
        document.getElementById('edit_id_convenio').value = al.id_convenio ?? '';
        document.getElementById('edit_fecha_inicio').value = al.fecha_inicio && al.fecha_inicio !== '0000-00-00' ? al.fecha_inicio : '';
        document.getElementById('edit_fecha_final').value = al.fecha_final && al.fecha_final !== '0000-00-00' ? al.fecha_final : '';
        document.getElementById('edit_horario').value = al.horario ?? '';
        document.getElementById('edit_horas_dia').value = al.horas_dia ?? '';
        document.getElementById('modalEditarAlumno').style.display = 'flex';
    })
    .catch(e => alert('Error al cargar datos del alumno'));
}
</script>