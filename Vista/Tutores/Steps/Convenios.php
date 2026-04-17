<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold flex items-center gap-3">🏢 Gestión de Convenios</h2>
    <a href="Convenios/Registrar.php" class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-900 transition-all shadow-lg">
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
        <table class="w-full text-left border-collapse table-fixed"> <thead class="bg-orange-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Empresa Seleccionada</th>
                    <th class="w-48 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-center">Gestión</th>
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
                            <form action="<?= $urlDestino ?>" method="POST" class="flex justify-center">
                                <input type="hidden" name="id_convenio_eliminar" value="<?= $mc['id_convenio'] ?>">
                                <button type="button" onclick="abrirConfirmarEliminar(<?= $mc['id_convenio'] ?>, '<?= htmlspecialchars($mc['nombre_empresa']) ?>')"
                                        class="group flex items-center gap-2 bg-red-50 hover:bg-red-500 text-red-500 hover:text-white px-4 py-2 rounded-lg transition-all border border-red-100 shadow-sm cursor-pointer">
                                    <span class="text-[10px] font-black uppercase">Eliminar</span>
                                    <span class="text-xs group-hover:rotate-90 transition-transform">✕</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="2" class="px-6 py-16 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Tu listado está vacío</td>
                    </tr>
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
    </div>
    <div class="overflow-hidden rounded-2xl border-2 border-amber-100 bg-white shadow-sm">
        <table class="w-full text-left border-collapse table-fixed"> <thead class="bg-amber-500 text-white">
                <tr>
                    <th class="w-32 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center">Editar</th>
                    
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-left">Empresa</th>
                    
                    <th class="w-48 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                <?php if (!empty($conveniosProceso)): ?>
                    <?php foreach ($conveniosProceso as $convP): ?>
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5 text-center">
                                <button type="button" onclick='abrirEditarConvenioNuevo(<?= json_encode($convP) ?>)'
                                    class="text-amber-500 hover:text-amber-700 transition-colors p-2.5 rounded-xl hover:bg-amber-100/50 inline-flex items-center justify-center border border-transparent hover:border-amber-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>

                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900 uppercase text-sm tracking-tight">
                                    <?= htmlspecialchars($convP['nombre_empresa']) ?>
                                </div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">
                                    <?= htmlspecialchars($convP['municipio']) ?>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-center">
                                <form action="index.php" method="POST" class="flex justify-center">
                                    <input type="hidden" name="accion" value="aprobarNuevo">
                                    <input type="hidden" name="id_convenio_nuevo" value="<?= $convP['id_convenio_nuevo'] ?>">
                                    
                                    <button type="button" 
                                            onclick="abrirConfirmarAprobar('<?= $convP['id_convenio_nuevo'] ?>', '<?= addslashes($convP['nombre_empresa']) ?>')"
                                            class="group flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-500 text-emerald-600 hover:text-white px-5 py-2.5 rounded-xl transition-all border border-emerald-100 shadow-sm hover:shadow-emerald-200 cursor-pointer active:scale-95">
                                        <span class="text-[10px] font-black uppercase tracking-widest">Aprobar</span>
                                        <span class="text-xs">✓</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Bandeja de entrada vacía</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'Vista/Tutores/Components/Modales_Convenios.php'; ?>