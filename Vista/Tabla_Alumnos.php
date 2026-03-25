<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto</title>
</head>
<body>
<div class="flex justify-between items-center mb-8">
  <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white text-sm">👥</span>
    Listado de Alumnado
  </h2>
  <div class="flex gap-2">
    <button class="bg-slate-50 text-slate-600 px-5 py-2.5 rounded-xl font-bold text-xs border border-slate-200 hover:bg-slate-100 transition-all flex items-center gap-2 cursor-pointer">
      📥 Cargar Alumnos
    </button>
    <button onclick="document.getElementById('modalAgregarAlumno').style.display='flex'"
            class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
      + Agregar Alumno
    </button>
  </div>
</div>

<form method="POST" action="index.php?controlador=Tutores&accion=mostrarPanel" class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100 items-center">
  <div class="flex-1 relative w-full">
    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
    <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR APELLIDOS O DNI..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
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
        <th class="w-24 text-center p-4">ESTADO</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 uppercase bg-white text-[10px]">
      <?php if (empty($alumnos)): ?>
        <tr><td colspan="12" class="py-10 text-center text-slate-400 italic">No hay resultados.</td></tr>
      <?php else: ?>
        <?php foreach ($alumnos as $al): 
            // VALIDACIÓN ROBUSTA DE DATOS
            $tieneEmpresa = !empty($al['id_convenio']);
            $tieneDireccion = !empty($al['direccion']);
            
            // Fix para fechas 0000-00-00
            $f_inicio = ($al['fecha_inicio'] && $al['fecha_inicio'] !== '0000-00-00') ? $al['fecha_inicio'] : null;
            $f_final = ($al['fecha_final'] && $al['fecha_final'] !== '0000-00-00') ? $al['fecha_final'] : null;
            $tieneFechas = ($f_inicio && $f_final);
            
            $tieneHorario = (!empty($al['horario']) && !empty($al['horas_dia']) && $al['horas_dia'] > 0);

            if (!$tieneEmpresa) {
                $estado = "SIN ASIGNAR";
                $colorEstado = "bg-red-100 text-red-700 border-red-200";
            } elseif (!$tieneDireccion || !$tieneFechas || !$tieneHorario) {
                $estado = "EN PROCESO";
                $colorEstado = "bg-amber-100 text-amber-700 border-amber-200";
            } else {
                $estado = "COMPLETADO";
                $colorEstado = "bg-emerald-100 text-emerald-700 border-emerald-200";
            }
        ?>
        <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="p-3 text-center">
                <button class="group p-2 rounded-lg hover:bg-orange-50 transition-all cursor-pointer border border-transparent hover:border-orange-100">
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
            <?php endif; ?>

            <td class="text-center p-4">
              <span class="<?= $colorEstado ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
                  <?= $estado ?>
              </span>
            </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<div id="modalAgregarAlumno" style="display:none" 
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) this.style.display='none'">

  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100">
    
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">👤</span>
        NUEVO ALUMNO
      </h3>
      <button onclick="document.getElementById('modalAgregarAlumno').style.display='none'"
              class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
    </div>

    <form method="POST" action="index.php">
      <input type="hidden" name="accion" value="agregarAlumno">

      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Primer Apellido <span class="text-red-500">*</span></label>
        <input type="text" name="apellido1" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>

      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Segundo Apellido</label>
        <input type="text" name="apellido2"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>

      <div class="mb-4">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
        <input type="text" name="nombre" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>

      <div class="flex gap-3 mb-4">
        <div class="flex-1">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE <span class="text-red-500">*</span></label>
          <input type="text" name="dni" required maxlength="9"
                 class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
        </div>
        <div class="w-28">
          <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sexo <span class="text-red-500">*</span></label>
          <select name="sexo" required
                  class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all cursor-pointer">
            <option value="">--</option>
            <option value="H">H</option>
            <option value="M">M</option>
          </select>
        </div>
      </div>

      <div class="mb-6">
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</label>
        <input type="email" name="correo"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
      </div>

      <div class="flex gap-3 justify-end">
        <button type="button" onclick="document.getElementById('modalAgregarAlumno').style.display='none'"
                class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
          Cancelar
        </button>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer">
          Guardar Alumno
        </button>
      </div>
    </form>
  </div>
</div>
</body>
</html>