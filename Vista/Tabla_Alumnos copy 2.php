<?php
// 1. Obtener los alumnos del ciclo del tutor y sus posibles asignaciones
// Asumimos que $idCicloTutor ya viene definido desde el controlador/index
$queryAlumnos = "SELECT 
                    a.id_alumno, a.nombre, a.apellido1, a.apellido2, a.dni, a.sexo,
                    asig.id_asignacion, asig.fecha_inicio, asig.fecha_final, asig.horario, asig.horas_dia,
                    conv.nombre_empresa, conv.id_convenio, conv.direccion, conv.municipio
                 FROM alumnos a
                 LEFT JOIN asignaciones asig ON a.id_alumno = asig.id_alumno
                 LEFT JOIN convenios conv ON asig.id_convenio = conv.id_convenio
                 WHERE a.id_ciclo = ? 
                 ORDER BY a.apellido1, a.apellido2, a.nombre";

$stmt = $conexion->prepare($queryAlumnos);
$stmt->bind_param("i", $idCicloTutor);
$stmt->execute();
$resAlumnos = $stmt->get_result();
$alumnos = $resAlumnos->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
    <button class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md cursor-pointer">
      + Agregar Alumno
    </button>
  </div>
</div>

<div class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
  <div class="flex-1 relative">
    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
    <input type="text" placeholder="BUSCAR POR APELLIDOS O DNI..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
  </div>
  <div class="flex items-center gap-3">
    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</span>
    <select class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
      <option value="">TODOS LOS ESTADOS</option>
      <option value="sin-asignar">🔴 SIN ASIGNAR</option>
      <option value="en-proceso">🟡 EN PROCESO</option>
      <option value="completado">🟢 COMPLETADO</option>
    </select>
  </div>
</div>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
  <table class="w-full text-left table-tech border-collapse">
    <thead>
      <tr>
        <th>APELLIDOS, NOMBRE ALUMNO</th>
        <th class="w-10">SEXO</th>
        <th class="w-24 border-section">DNI / NIE</th>
        <th>NOMBRE EMPRESA</th>
        <th class="w-16">Nº CONV.</th>
        <th class="border-section">DIRECCIÓN CENTRO TRABAJO</th>
        <th class="w-20">F. INICIO</th>
        <th class="w-20">F. FINAL</th>
        <th class="w-24">HORARIO</th>
        <th class="w-12 border-section">H/DÍA</th>
        <th class="w-24">ESTADO</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 uppercase bg-white">
      
      <?php foreach ($alumnos as $al): 
          // Lógica de validación de datos
          $tieneEmpresa = !empty($al['id_convenio']);
          $tieneDireccion = !empty($al['direccion']);
          $tieneFechas = !empty($al['fecha_inicio']) && !empty($al['fecha_final']);
          $tieneHorario = !empty($al['horario']) && !empty($al['horas_dia']);

          // Determinar Estado
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
        <td class="font-bold"><?= $al['apellido1'] ?> <?= $al['apellido2'] ?>, <?= $al['nombre'] ?></td>
        <td class="text-center"><?= $al['sexo'] ?></td>
        <td class="text-center font-mono border-section"><?= $al['dni'] ?></td>

        <?php if (!$tieneEmpresa): ?>
          <td colspan="7" class="text-center bg-red-50/30 text-red-600 border-section tracking-[0.2em] font-black italic py-4">
            ⚠️ PENDIENTE DE ASIGNACIÓN DE PLAZA
          </td>
        <?php elseif (!$tieneDireccion || !$tieneFechas || !$tieneHorario): ?>
          <td class="text-orange-600"><?= $al['nombre_empresa'] ?></td>
          <td class="text-center"><?= str_pad($al['id_convenio'], 4, "0", STR_PAD_LEFT) ?></td>
          <td colspan="5" class="text-center bg-orange-50/30 text-orange-600 border-section tracking-wider font-black italic py-4">
            ⚠️ FALTA <?php 
              $faltas = [];
              if (!$tieneDireccion) $faltas[] = "DIRECCIÓN";
              if (!$tieneFechas) $faltas[] = "FECHAS";
              if (!$tieneHorario) $faltas[] = "HORARIO";
              echo implode(", ", $faltas);
            ?>
          </td>
        <?php else: ?>
          <td><?= $al['nombre_empresa'] ?></td>
          <td class="text-center"><?= str_pad($al['id_convenio'], 4, "0", STR_PAD_LEFT) ?></td>
          <td class="border-section text-[9px] lowercase font-medium"><?= $al['direccion'] ?>, <?= $al['municipio'] ?></td>
          <td class="text-center"><?= date("d/m/y", strtotime($al['fecha_inicio'])) ?></td>
          <td class="text-center"><?= date("d/m/y", strtotime($al['fecha_final'])) ?></td>
          <td class="text-center"><?= $al['horario'] ?></td>
          <td class="text-center border-section"><?= number_format($al['horas_dia'], 0) ?></td>
        <?php endif; ?>

        <td class="text-center">
          <span class="<?= $colorEstado ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
            <?= $estado ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>

    </tbody>
  </table>
</div>

<div class="mt-6 flex justify-between items-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
  <p>Mostrando <?= count($alumnos) ?> alumnos asignados a tu tutoría</p>
  <div class="flex gap-2">
    <button class="px-3 py-1 border border-slate-200 rounded hover:bg-white transition-colors cursor-not-allowed">Ant.</button>
    <button class="px-3 py-1 border border-slate-200 rounded hover:bg-white transition-colors cursor-not-allowed">Sig.</button>
  </div>
</div>
</body>
</html>