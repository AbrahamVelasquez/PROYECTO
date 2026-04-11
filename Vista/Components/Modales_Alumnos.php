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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-sm p-8 border border-slate-100">
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
            <button onclick="document.getElementById('formExportar').submit()" class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">Sí, exportar</button>
        </div>
    </div>
</div>