<div class="flex justify-between items-center mb-8">
  <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white text-sm">👥</span>
    Listado de Alumnado
  </h2>
  <div class="flex gap-2">
    <button class="bg-slate-50 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-2 cursor-pointer">
      📥 Cargar Alumnos
    </button>
    <button onclick="document.getElementById('modalConfirmarExportar').style.display='flex'" class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
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
        <option value="">SIN ORDENAR</option>
        <option value="nombre"          <?= ($_POST['ordenar'] ?? '') == 'nombre'           ? 'selected' : '' ?>>NOMBRE</option>
        <option value="mis_convenios"  <?= ($_POST['ordenar'] ?? '') == 'mis_convenios'   ? 'selected' : '' ?>>CONVENIO</option>
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