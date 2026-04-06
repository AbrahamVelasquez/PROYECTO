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

<div id="modalConvenioEnUso" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">⚠️</span>
                CONVENIO EN USO
            </h3>
            <button onclick="document.getElementById('modalConvenioEnUso').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p id="modalConvenioMensaje" class="text-xs font-bold text-slate-600 mb-6 text-center"></p>
        <div class="flex justify-center">
            <button onclick="document.getElementById('modalConvenioEnUso').style.display='none'" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-700 cursor-pointer">Entendido</button>
        </div>
    </div>
</div>

<div id="modalConfirmarEliminar" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white text-xs">🗑️</span>
                ELIMINAR
            </h3>
            <button onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        <p class="text-xs font-bold text-slate-500 mb-1 text-center uppercase tracking-widest">¿Quitar de tu lista?</p>
        <p id="modalConfirmarNombre" class="text-sm font-black text-slate-900 mb-6 text-center uppercase"></p>
        <div class="flex gap-3 justify-center">
            <button onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">Cancelar</button>
            <button id="btnConfirmarEliminarFav" class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-xs font-bold hover:bg-red-600 cursor-pointer">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
function abrirConfirmarEliminar(idConvenio, nombre) {
    document.getElementById('modalConfirmarNombre').textContent = nombre;
    document.getElementById('modalConfirmarEliminar').style.display = 'flex';
    document.getElementById('btnConfirmarEliminarFav').onclick = function() {
        document.querySelectorAll('input[name="id_convenio_eliminar"]').forEach(function(input) {
            if (input.value == idConvenio) {
                var btn = document.createElement('input');
                btn.type = 'hidden'; btn.name = 'btnEliminarFav'; btn.value = '1';
                input.closest('form').appendChild(btn);
                input.closest('form').submit();
            }
        });
    };
}
</script>