<?php
// Vista/Tutores/Steps/Seguimiento.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';
validarAcceso('tutor');

// Filtramos solo los alumnos con exportado = 1
$alumnosSeguimiento = array_filter($alumnosFirmados ?? [], fn($a) => $a['exportado'] == 1);

// Construir nombre de carpeta del ciclo (ej: "2DAW")
$nombreCursoSeg = strtolower(trim($cursoTutor ?? ''));
$numCursoSeg = match(true) {
    str_contains($nombreCursoSeg, 'primero') => '1',
    str_contains($nombreCursoSeg, 'segundo') => '2',
    str_contains($nombreCursoSeg, 'tercero') => '3',
    default                                   => '1',
};
$cicloSafe    = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($cicloTutor ?? 'CICLO'));
$carpetaCiclo = $numCursoSeg . $cicloSafe; // ej: "2DAW"

$baseDoc = $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Documentacion/';

// Función: nombre de carpeta saneado para un alumno
function carpetaAlumno(array $al): string {
    $ape1 = preg_replace('/[^A-Za-z0-9]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', strtoupper($al['apellido1'] ?? '')));
    $ape2 = preg_replace('/[^A-Za-z0-9]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', strtoupper($al['apellido2'] ?? '')));
    $nom  = preg_replace('/[^A-Za-z0-9]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', strtoupper($al['nombre'] ?? '')));
    return trim($ape1 . '_' . $ape2 . '_' . $nom, '_');
}

// Cuenta archivos reales de una carpeta
function contarArchivosSeg(string $ruta): int {
    if (!is_dir($ruta)) return 0;
    return count(array_filter(scandir($ruta), fn($f) => !in_array($f, ['.', '..']) && is_file($ruta . $f)));
}

// Estado global del ciclo
$hayAlgunPF     = false;
$hayAlgunaFicha = false;
foreach ($alumnosSeguimiento as $al) {
    $carpeta = carpetaAlumno($al);
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Plan_Formativo/') > 0) $hayAlgunPF     = true;
    if (contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Fichas/')          > 0) $hayAlgunaFicha = true;
}
if ($hayAlgunPF && $hayAlgunaFicha) {
    $estadoGlobal = 'Completado'; $estadoGlobalColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
} elseif ($hayAlgunPF || $hayAlgunaFicha) {
    $estadoGlobal = 'Parcial';    $estadoGlobalColor = 'bg-amber-100 text-amber-700 border-amber-200';
} else {
    $estadoGlobal = 'Pendiente';  $estadoGlobalColor = 'bg-red-100 text-red-700 border-red-200';
}
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Seguimiento de Documentación</h2>
        <p class="text-[10px] font-bold text-slate-400 mt-1">
            Alumnos exportados · Carpeta: <span class="text-orange-600"><?= htmlspecialchars($carpetaCiclo) ?></span>
        </p>
    </div>
    <span class="<?= $estadoGlobalColor ?> px-3 py-1.5 rounded-full text-[9px] border font-black whitespace-nowrap uppercase">
        Estado general: <?= $estadoGlobal ?>
    </span>
</div>

<?php if (empty($alumnosSeguimiento)): ?>
    <div class="text-center text-slate-400 italic py-20 uppercase font-black text-[10px] tracking-widest">
        No hay alumnos exportados aún. Cuando un alumno sea exportado aparecerá aquí.
    </div>
<?php else: ?>

<div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
    <table class="w-full text-left border-collapse bg-white">
        <thead>
            <tr class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase">
                <th class="p-4">Alumno</th>
                <th class="p-4 text-center w-52">Plan Formativo Firmado</th>
                <th class="p-4 text-center w-52">Fichas Firmadas</th>
                <th class="p-4 text-center w-36">Estado Subida</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-[10px]">
            <?php foreach ($alumnosSeguimiento as $al):
                $carpeta   = carpetaAlumno($al);
                $numPF     = contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Plan_Formativo/');
                $numFichas = contarArchivosSeg($baseDoc . $carpetaCiclo . '/' . $carpeta . '/Fichas/');

                if ($numPF > 0 && $numFichas > 0) {
                    $estadoLabel = 'Completado'; $estadoColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                } elseif ($numPF > 0 || $numFichas > 0) {
                    $estadoLabel = 'Parcial';    $estadoColor = 'bg-amber-100 text-amber-700 border-amber-200';
                } else {
                    $estadoLabel = 'Pendiente';  $estadoColor = 'bg-red-100 text-red-700 border-red-200';
                }

                // Nombre saneado para pasar a JS (safe para atributo HTML)
                $carpetaJs = htmlspecialchars($carpeta, ENT_QUOTES);
            ?>
            <tr class="hover:bg-slate-50/50 transition-colors uppercase">

                <td class="p-4 font-bold text-slate-700">
                    <?= htmlspecialchars($al['apellido1'] . ' ' . ($al['apellido2'] ?? '') . ', ' . $al['nombre']) ?>
                </td>

                <!-- Plan Formativo -->
                <td class="p-4 text-center">
                    <button type="button"
                        onclick="abrirModalDocumentos('plan_formativo', '<?= $carpetaJs ?>')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-[9px] font-black text-slate-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Ver Documentos
                        <?php if ($numPF > 0): ?>
                            <span class="ml-1 bg-orange-600 text-white rounded-full px-1.5 py-0.5 text-[8px] font-black"><?= $numPF ?></span>
                        <?php endif; ?>
                    </button>
                </td>

                <!-- Fichas -->
                <td class="p-4 text-center">
                    <button type="button"
                        onclick="abrirModalDocumentos('fichas', '<?= $carpetaJs ?>')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 text-[9px] font-black text-slate-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer uppercase tracking-wide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Ver Documentos
                        <?php if ($numFichas > 0): ?>
                            <span class="ml-1 bg-orange-600 text-white rounded-full px-1.5 py-0.5 text-[8px] font-black"><?= $numFichas ?></span>
                        <?php endif; ?>
                    </button>
                </td>

                <!-- Estado individual -->
                <td class="p-4 text-center">
                    <span class="<?= $estadoColor ?> px-3 py-1 rounded-full text-[8px] border font-black whitespace-nowrap">
                        <?= $estadoLabel ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<script>
    window.SEGUIMIENTO_CICLO = '<?= htmlspecialchars($carpetaCiclo, ENT_QUOTES) ?>';
</script>

<?php include __DIR__ . '/../Components/Modales_Seguimiento.php'; ?>
