<style>
  /* Estilos para el tooltip */
.help-trigger {
    position: relative;
    display: inline-block;
}

.tooltip-box {
    display: none;
    position: absolute;
    bottom: 125%; /* Aparece arriba del signo ? */
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    background-color: #1e293b; /* slate-800 */
    color: white;
    text-align: center;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 10px;
    font-weight: bold;
    line-height: 1.4;
    text-transform: none; /* Para que no salga todo en mayúsculas */
    z-index: 100;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Flechita del tooltip */
.tooltip-box::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #1e293b transparent transparent transparent;
}

/* Mostrar al pasar el ratón */
.help-trigger:hover .tooltip-box {
    display: block;
}
</style>
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
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">H/DÍA</label>
          <input type="number" name="horas_dia" id="edit_horas_dia" min="0" max="24" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
      </div>

      <div id="bloque_enviado" class="mb-6 p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
          <div class="flex items-center gap-2">
              <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                  ¿Documentación enviada?
              </span>
              <div class="help-trigger relative">
                  <span class="cursor-help flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 text-[10px] text-slate-400 font-bold hover:bg-slate-100 transition-colors">?</span>
                  <div class="tooltip-box">
                      Quitar el check implica que se quiere volver a editar el alumno y volver a enviarlo
                  </div>
              </div>
          </div>
          
          <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" name="enviado" id="edit_enviado" value="1" 
                    class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600">
          </label>
      </div>

      <div class="flex gap-3 justify-end">
        <button type="button" onclick="document.getElementById('modalEditarAlumno').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancelar</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>
    </form>
  </div>
</div>

<div id="modalSeleccionarExportar" style="display:none" class="fixed inset-0 bg-black/50 z-[90] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📋</span>
                Seleccionar Alumnos
            </h3>
            <button onclick="document.getElementById('modalSeleccionarExportar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Alumnos con estado "Completado" no enviados</p>

        <div class="flex justify-between px-4 py-2 bg-slate-50 rounded-t-xl border-b border-slate-100 text-[9px] font-black text-slate-500 uppercase tracking-tighter">
            <span>Alumno</span>
            <span>Seleccionar</span>
        </div>

        <form id="formExportar" method="POST" action="index.php">
            <input type="hidden" name="accion" value="exportarAlumnos">
            <div class="max-h-60 overflow-y-auto mb-6 custom-scrollbar">
                <?php 
                $hayCandidatos = false;
                foreach ($alumnos as $al): 
                    // Lógica de filtrado: Estado calculado igual que en la tabla 
                    $tieneEmpresa = !empty($al['id_convenio']);
                    $tieneDireccion = !empty($al['direccion']);
                    $tieneFechas = ($al['fecha_inicio'] && $al['fecha_final'] && $al['fecha_inicio'] !== '0000-00-00');
                    $tieneHorario = (!empty($al['horario']) && $al['horas_dia'] > 0);
                    
                    $esCompletado = ($tieneEmpresa && $tieneDireccion && $tieneFechas && $tieneHorario);
                    
                    if ($esCompletado && $al['enviado'] == 0): 
                        $hayCandidatos = true;
                ?>
                    <div class="flex justify-between items-center px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                        <span class="text-xs font-bold text-slate-700 uppercase truncate pr-4">
                            <?= htmlspecialchars($al['apellido1'] . " " . $al['nombre']) ?>
                        </span>
                        <input type="checkbox" name="exportar_ids[]" value="<?= $al['id_alumno'] ?>" 
                               class="w-5 h-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600 cursor-pointer">
                    </div>
                <?php endif; endforeach; ?>

                <?php if (!$hayCandidatos): ?>
                    <div class="py-10 text-center text-slate-400 text-xs italic font-medium">
                        No hay alumnos pendientes de exportar.
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modalSeleccionarExportar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
                <?php if ($hayCandidatos): ?>
                    <button type="button" onclick="abrirConfirmacionFinal()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Exportar Selección</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div id="modalErrorFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">⚠️</span>
                ACCIÓN BLOQUEADA
            </h3>
            <button onclick="document.getElementById('modalErrorFirma').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Documentación pendiente</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                No se puede firmar la asignación de <span id="nombreAlumnoError" class="text-slate-900"></span> porque la documentación aún no ha sido <span class="text-orange-600">ENVIADA</span>.
            </p>
        </div>

        <div class="flex justify-center">
            <button onclick="document.getElementById('modalErrorFirma').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">
                Entendido
            </button>
        </div>
    </div>
</div>

<div id="modalYaFirmado" style="display:none" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-500 text-white text-xs">🔒</span>
                YA REGISTRADO
            </h3>
            <button onclick="document.getElementById('modalYaFirmado').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <div class="flex flex-col items-center mb-6">
            <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-2">Firma confirmada</p>
            <p class="text-xs font-bold text-slate-600 text-center leading-relaxed">
                La asignación de <span id="nombreAlumnoFirmado" class="text-slate-900"></span> ya consta como <span class="text-orange-600">FIRMADA</span> en la base de datos.
            </p>
        </div>

        <div class="flex justify-center">
            <button onclick="document.getElementById('modalYaFirmado').style.display='none'" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 transition-colors cursor-pointer">
                Entendido
            </button>
        </div>
    </div>
</div>

<div id="modalConfirmarFirma" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">✍️</span>
                CONFIRMAR FIRMA
            </h3>
            <button onclick="cerrarModalFirma()" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Confirmar que este alumno está firmado?</p>
        <p id="modalFirmaNombre" class="text-sm font-black text-slate-900 mb-6 text-center uppercase"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="cerrarModalFirma()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button id="btnConfirmarFirmaAccion" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">
                Sí, confirmar
            </button>
        </div>
    </div>
</div>

<div id="modalConfirmarExportar" style="display:none" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">📤</span>
                Exportar Alumnos
            </h3>
            <button onclick="document.getElementById('modalConfirmarExportar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-6 text-center uppercase tracking-widest leading-relaxed">¿Seguro que quieres exportar los alumnos seleccionados?</p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarExportar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="document.getElementById('formExportar').submit()" 
                    class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">
                Sí, exportar
            </button>
        </div>
    </div>
</div>