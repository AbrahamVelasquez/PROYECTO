<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Convenios de Empresa</h2>
        <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Empresas con convenio activo: <?= count($convenios) ?>
        </p>
    </div>
    <div class="flex items-center gap-4">
        <form action="index.php" method="POST">
            <input type="hidden" name="accion" value="mostrarPanel">
            <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-blue-600 transition-all cursor-pointer">
                <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
            </button>
        </form>
    </div>
</div>

<form method="POST" action="index.php" class="flex flex-col lg:flex-row gap-4 mb-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 items-center">
    <input type="hidden" name="accion" value="mostrarConvenios">
    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR NOMBRE O CIF..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-blue-100 transition-all uppercase">
    </div>
    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold text-[10px] hover:bg-blue-600 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
</form>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-slate-700">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">CIF</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Razón Social</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Dirección</th>
                <th class="py-5 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($convenios)): ?>
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400 italic text-xs uppercase tracking-widest">
                        No hay convenios que coincidan con la búsqueda
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($convenios as $fila): ?>
                <tr class="hover:bg-slate-50/40 transition-all group">
                    <td class="py-5 px-6">
                        <div class="text-sm font-bold text-slate-800 uppercase"><?= htmlspecialchars($fila['nombre_empresa']) ?></div>
                        <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($fila['cif']) ?></div>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-xs text-slate-600 font-bold uppercase"><?= htmlspecialchars($fila['municipio']) ?></div>
                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($fila['direccion']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-xs text-slate-500">
                        <?= htmlspecialchars($fila['mail']) ?>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-[11px] font-bold text-slate-700 uppercase"><?= htmlspecialchars($fila['nombre_representante']) ?></div>
                        <div class="text-[9px] text-slate-400 uppercase tracking-tighter"><?= htmlspecialchars($fila['cargo']) ?></div>
                    </td>
                    <td class="py-5 px-6 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='abrirEditarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button onclick='abrirModalEliminarConvenio(<?= json_encode($fila) ?>)' class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>