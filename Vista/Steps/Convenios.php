<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold flex items-center gap-3">🏢 Gestión de Convenios</h2>
    <a href="Vista/Registro_Convenio.php" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-900 transition-all shadow-lg">
        + Registrar Nuevo Convenio
    </a>
</div>

<form action="index.php" method="GET" class="flex gap-3 w-full mb-10">
    <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" 
        placeholder="CIF O NOMBRE DE EMPRESA..." 
        class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-6 py-4 outline-none focus:ring-4 focus:ring-orange-50 text-xs font-bold uppercase transition-all">
    <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg cursor-pointer">Buscar</button>
</form>

<?php if (isset($_GET['busqueda']) && trim($_GET['busqueda']) !== ''): ?>
    <div class="mb-10">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 text-center">Resultados de la búsqueda</h3>
        <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Nº</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Empresa / CIF</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Municipio</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Contacto</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Representante</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (!empty($convenios)): foreach ($convenios as $c): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-5 text-center font-mono text-sm text-slate-400 font-bold">#<?= $c['id_convenio'] ?></td>
                            <td class="px-4 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm italic"><?= $c['nombre_empresa'] ?></div>
                                <div class="text-xs text-slate-400 font-mono"><?= $c['cif'] ?></div>
                            </td>
                            <td class="px-4 py-5 text-sm font-bold text-slate-600 uppercase"><?= $c['municipio'] ?></td>
                            <td class="px-4 py-5">
                                <div class="text-sm font-bold text-slate-700"><?= $c['telefono'] ?></div>
                                <div class="text-xs text-orange-600 font-medium"><?= $c['mail'] ?></div>
                            </td>
                            <td class="px-4 py-5 text-sm font-bold text-slate-500 uppercase"><?= $c['nombre_representante'] ?></td>
                            <td class="px-4 py-5 text-center">
                                <form action="index.php?busqueda=<?= urlencode($_GET['busqueda']) ?>" method="POST">
                                    <input type="hidden" name="id_convenio_fav" value="<?= $c['id_convenio'] ?>">
                                    <button type="submit" name="btnFavorito" class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all cursor-pointer">⭐ Añadir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-red-500 text-sm font-black uppercase italic">⚠ No hay convenios que coincidan con "<?= htmlspecialchars($_GET['busqueda']) ?>".</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div class="mt-12">
    <h3 class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-4">Mi Listado Personal</h3>
    <div class="overflow-hidden rounded-2xl border-2 border-orange-100 bg-white">
        <table class="w-full text-left border-collapse">
            <thead class="bg-orange-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Empresa Seleccionada</th>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-center">Gestión</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-orange-50">
                <?php if (!empty($misConvenios)): foreach ($misConvenios as $mc): ?>
                    <tr class="hover:bg-orange-50/50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="font-bold text-slate-900 uppercase text-sm"><?= $mc['nombre_empresa'] ?></div>
                            <div class="text-xs text-slate-400 font-bold"><?= $mc['municipio'] ?></div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php 
                                $urlDestino = "index.php";
                                if(!empty($_GET['busqueda'])) $urlDestino .= "?busqueda=".urlencode($_GET['busqueda']);
                            ?>
                            <form action="<?= $urlDestino ?>" method="POST">
                                <input type="hidden" name="id_convenio_eliminar" value="<?= $mc['id_convenio'] ?>">
                                <button type="button" onclick="abrirConfirmarEliminar(<?= $mc['id_convenio'] ?>, '<?= htmlspecialchars($mc['nombre_empresa']) ?>')"
                                        class="group flex items-center gap-2 mx-auto bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-4 py-2 rounded-lg transition-all border border-red-100 shadow-sm cursor-pointer">
                                    <span class="text-[10px] font-black uppercase">Eliminar</span>
                                    <span class="text-xs group-hover:rotate-90 transition-transform">✕</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="2" class="px-6 py-12 text-center text-slate-300 text-xs font-black uppercase italic">Tu listado está vacío</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em] flex items-center gap-2">
            ⏳ Convenios en Proceso
        </h3>
        <span class="text-[9px] font-black text-amber-500 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full uppercase tracking-widest">
            Pendientes de confirmar
        </span>
    </div>
    <div class="overflow-hidden rounded-2xl border-2 border-amber-100 bg-white shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-amber-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Empresa</th>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Estado del enlace</th>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                <?php if (!empty($conveniosProceso)): ?>
                    <?php foreach ($conveniosProceso as $convP): ?>
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm">
                                    <?= htmlspecialchars($convP['nombre_empresa']) ?>
                                </div>
                                <div class="text-xs text-slate-400 font-bold">
                                    <?= htmlspecialchars($convP['municipio']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 border border-amber-200 px-3 py-1 rounded-full text-[9px] font-black uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Esperando validación de dirección
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <form action="index.php" method="POST">
                                    <input type="hidden" name="accion" value="aprobarNuevo">
                                    <input type="hidden" name="id_convenio_nuevo" value="<?= $convP['id_convenio_nuevo'] ?>">
                                    <button type="submit" class="group flex items-center gap-2 mx-auto bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-4 py-2 rounded-lg transition-all border border-emerald-100 cursor-pointer">
                                        <span class="text-[10px] font-black uppercase">Aprobar</span>
                                        <span class="text-xs">✓</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="text-slate-300 mb-2">
                                <svg class="w-10 h-10 mx-auto opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No hay convenios pendientes para tu ciclo</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'Vista/Components/Modales_Convenios.php'; ?>