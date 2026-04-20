<?php 

// Vista/Admin/Sections/Tabla_Convenios_Pendientes.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<div class="flex items-center justify-between mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Convenios Pendientes</h2>
        <p class="text-emerald-600 text-[10px] font-black tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Esperando incorporación al sistema: <?= count($pendientes) ?>
        </p>
    </div>
    
    <form action="index.php" method="POST">
        <input type="hidden" name="accion" value="mostrarPanel">
        <button type="submit" class="flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-emerald-600 transition-all cursor-pointer">
            ← VOLVER AL PANEL
        </button>
    </form>
</div>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="py-4 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Empresa</th>
                <th class="py-4 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Aprobación</th>
                <th class="py-4 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($pendientes)): ?>
                <tr>
                    <td colspan="3" class="py-20 px-6 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-slate-800 font-black uppercase text-sm tracking-tighter">Todo al día</h3>
                            <p class="text-slate-400 text-[10px] font-bold uppercase mt-1 tracking-widest">No hay convenios pendientes de validación</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pendientes as $p): ?>
                <tr class="hover:bg-emerald-50/30 transition-all group">
                    <td class="py-5 px-6">
                        <div class="font-bold text-slate-800 uppercase text-sm group-hover:text-emerald-700 transition-colors">
                            <?= htmlspecialchars($p['nombre_empresa']) ?>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($p['cif']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-xs text-slate-500 font-medium">
                        <?= date('d/m/Y H:i', strtotime($p['fecha_aprobacion'])) ?>
                    </td>
                    <td class="py-5 px-6">
                        <div class="flex items-center justify-center gap-3">
                            <button onclick='abrirModalRevision(<?= json_encode($p) ?>)' 
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all cursor-pointer" 
                                    title="Revisar y Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            <form id="form-validar-<?= $p['id_convenio_nuevo'] ?>" method="POST" action="index.php">
                                <input type="hidden" name="accion" value="validarConvenio">
                                <input type="hidden" name="id_convenio_nuevo" value="<?= $p['id_convenio_nuevo'] ?>">
                                
                                <button type="button" 
                                        onclick="confirmarValidacionDirecta('<?= $p['id_convenio_nuevo'] ?>', '<?= htmlspecialchars($p['nombre_empresa']) ?>')"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100 cursor-pointer flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Validar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'Vista/Admin/Components/Modales_TCP.php'; ?>