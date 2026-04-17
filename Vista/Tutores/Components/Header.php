<div class="pt-6 px-8 pb-0"> 
    <div class="flex justify-between items-start mb-6">
        <div class="flex-1 min-w-0"> 
            <h1 class="text-4xl font-extrabold tracking-tight">
                Gestión de <span class="text-orange-600">Prácticas FFE</span>
            </h1>
            <p class="mt-1 text-slate-500 text-sm max-w-2xl truncate">
                Interfaz de gestión interna FFE. Selecciona una fase para gestionar el registro, asignación o seguimiento.
            </p>
        </div>

        <div class="relative ml-4 flex-shrink-0">
            <button id="userMenuBtn" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2 pr-4 hover:bg-slate-50 transition-all shadow-sm cursor-pointer">
                <div class="h-8 w-8 rounded-lg bg-orange-600 flex items-center justify-center text-white font-bold text-xs shadow-inner">
                    <?= strtoupper(substr($nombreTutor, 0, 1)) ?>
                </div>
                <span class="text-xs font-bold text-slate-700"><?= $nombreTutor ?></span>
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Tutoría Asignada</p>
                    <p class="text-[11px] font-bold text-slate-800 uppercase italic">
                        <?php 
                        $cursoLimpio = mb_strtolower(trim($cursoTutor));
                        $abreviatura = (str_contains($cursoLimpio, 'primero')) ? "1º" : ((str_contains($cursoLimpio, 'segundo')) ? "2º" : $cursoTutor);
                        echo $abreviatura . " " . $cicloTutor; 
                        ?>
                    </p>
                </div>
                <div class="p-1">
                    <form action="index.php" method="POST">
                        <button type="submit" name="btnLogOut" class="w-full text-left px-4 py-3 text-[10px] font-black text-red-500 hover:bg-red-50 rounded-lg transition-colors uppercase flex items-center justify-between">
                            <span>Cerrar Sesión</span>
                            <span>✕</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative mb-6">
        <div class="hidden md:block absolute top-2.5 left-0 w-full h-0.5 bg-slate-100 -z-0"></div>
        <?php 
        $pasos = [
            1 => ['tit' => 'Convenios', 'desc' => 'Gestión de acuerdos legales con empresas colaboradoras.'],
            2 => ['tit' => 'Alumnos', 'desc' => 'Base de datos de alumnos matriculados y asignación a plazas.'],
            3 => ['tit' => 'Plan Formativo', 'desc' => 'Definición de las tareas y competencias FFE.'],
            4 => ['tit' => 'Seguimiento', 'desc' => 'Valoración del tutor y cierre de expediente.']
        ];
        foreach($pasos as $num => $info): ?>
        <div class="step-label relative z-10 flex flex-col items-center text-center" data-step="<?= $num ?>">
            <div class="step-circle flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 transition-all duration-300 mb-2 border-2 border-white shadow-sm">
                <span class="text-[12px] font-bold"><?= $num ?></span>
            </div>
            <div class="flex items-center gap-2">
                <h3 class="step-heading text-slate-400 text-sm transition-all duration-300 uppercase tracking-wider"><?= $info['tit'] ?></h3>
                <div class="help-trigger">
                    <span class="cursor-help flex h-4 w-4 items-center justify-center rounded-full border border-slate-300 text-[10px] text-slate-400 font-bold hover:bg-slate-100">?</span>
                    <div class="tooltip-box"><?= $info['desc'] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>