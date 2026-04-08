<div class="flex justify-between items-center mb-8">
  <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white text-sm">👥</span>
    Listado de Alumnado
  </h2>
  <div class="flex gap-2">
    <button class="bg-indigo-50 text-indigo-600 px-5 py-2.5 rounded-xl font-bold text-xs border border-indigo-100 hover:bg-indigo-100 transition-all flex items-center gap-2 cursor-pointer">
      📤 Importar Alumnos
    </button>
    <button class="bg-slate-50 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-2 cursor-pointer">
      📥 Cargar Alumnos
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
    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Filtrar por:</span>
    <select name="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
        <option value="">SIN ORDENAR</option>
        <option value="nombre"          <?= ($_POST['ordenar'] ?? '') == 'nombre'           ? 'selected' : '' ?>>NOMBRE</option>
        <option value="mis_convenios"  <?= ($_POST['ordenar'] ?? '') == 'mis_convenios'   ? 'selected' : '' ?>>Nº CONVENIO</option>
        <option value="estado"          <?= ($_POST['ordenar'] ?? '') == 'estado'           ? 'selected' : '' ?>>ESTADO</option>
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
                <button onclick="abrirModalEditar(<?= $al['id_alumno'] ?>)" class="group p-2 rounded-lg hover:bg-orange-50 transition-all cursor-pointer border border-transparent hover:border-orange-100">
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
                        <input type="checkbox" 
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

<div id="modalAgregarAlumno" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">👤</span>
        NUEVO ALUMNO
      </h3>
      <button onclick="document.getElementById('modalAgregarAlumno').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
    </div>
    <form method="POST" action="index.php">
      <input type="hidden" name="accion" value="agregarAlumno">
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Primer Apellido <span class="text-red-500">*</span></label>
        <input type="text" name="apellido1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Segundo Apellido</label>
        <input type="text" name="apellido2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="flex gap-3 mb-4">
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE <span class="text-red-500">*</span></label>
          <input type="text" name="dni" required maxlength="9" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
        <div class="w-28">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sexo <span class="text-red-500">*</span></label>
          <select name="sexo" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
            <option value="">--</option><option value="H">H</option><option value="M">M</option>
          </select>
        </div>
      </div>
      <div class="mb-6">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
        <input type="email" name="correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="flex gap-3 justify-end">
        <button type="button" onclick="document.getElementById('modalAgregarAlumno').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancelar</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Guardar Alumno</button>
      </div>
    </form>
  </div>
</div>

<div id="modalEditarAlumno" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 border border-slate-100 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">✏️</span>
        EDITAR ALUMNO
      </h3>
      <button onclick="document.getElementById('modalEditarAlumno').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
    </div>
    <form method="POST" action="index.php" id="formEditarAlumno">
      <input type="hidden" name="accion" value="editarAlumno">
      <input type="hidden" name="id_alumno" id="edit_id_alumno">
      <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Datos del Alumno</p>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Primer Apellido <span class="text-red-500">*</span></label>
        <input type="text" name="apellido1" id="edit_apellido1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Segundo Apellido</label>
        <input type="text" name="apellido2" id="edit_apellido2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="nombre" id="edit_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <div class="flex gap-3 mb-4">
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE <span class="text-red-500">*</span></label>
          <input type="text" name="dni" id="edit_dni" required maxlength="9" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
        <div class="w-28">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sexo <span class="text-red-500">*</span></label>
          <select name="sexo" id="edit_sexo" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
            <option value="">--</option><option value="H">H</option><option value="M">M</option>
          </select>
        </div>
      </div>
      <div class="mb-6">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
        <input type="email" name="correo" id="edit_correo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>
      <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-100 pb-2">Asignación de Empresa</p>
      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Convenio / Empresa</label>
        <select name="id_convenio" id="edit_id_convenio" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
          <option value="">-- Sin asignar --</option>
          <?php foreach ($misConvenios as $conv): ?>
            <option value="<?= $conv['id_convenio'] ?>"><?= str_pad($conv['id_convenio'], 4, "0", STR_PAD_LEFT) ?> — <?= htmlspecialchars($conv['nombre_empresa']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex gap-3 mb-4">
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">F. Inicio</label>
          <input type="date" name="fecha_inicio" id="edit_fecha_inicio" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">F. Final</label>
          <input type="date" name="fecha_final" id="edit_fecha_final" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
      </div>
      <div class="flex gap-3 mb-6">
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Horario</label>
          <input type="text" name="horario" id="edit_horario" placeholder="08:00-15:00" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
        <div class="w-28">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">H/Día</label>
          <input type="number" name="horas_dia" id="edit_horas_dia" min="0" max="24" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
      </div>
      <div class="flex gap-3 justify-end">
        <button type="button" onclick="document.getElementById('modalEditarAlumno').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancelar</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<div id="modalConfirmarFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500 text-white text-xs">✍️</span>
                CONFIRMAR FIRMA
            </h3>
            <button onclick="cerrarModalFirma()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar que este alumno está firmado?</p>
        <p id="modalFirmaNombre" class="text-sm font-black text-slate-900 mb-6 text-center uppercase"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="cerrarModalFirma()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
            <button id="btnConfirmarFirmaAccion" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 cursor-pointer">Sí, confirmar</button>
        </div>
    </div>
</div>

<script>
let checkboxPendiente = null;

function solicitarConfirmacionFirma(idAlumno, nombreCompleto, checkbox) {
    if (!checkbox.checked) return; // Si se desmarca, no pedimos confirmación (opcional)

    // Desmarcamos temporalmente hasta que confirme
    checkbox.checked = false;
    checkboxPendiente = checkbox;

    document.getElementById('modalFirmaNombre').textContent = nombreCompleto;
    document.getElementById('modalConfirmarFirma').style.display = 'flex';

    document.getElementById('btnConfirmarFirmaAccion').onclick = function() {
        if (checkboxPendiente) {
            checkboxPendiente.checked = true;
            // Aquí puedes disparar un form.submit() o un fetch si quieres guardar automático
        }
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