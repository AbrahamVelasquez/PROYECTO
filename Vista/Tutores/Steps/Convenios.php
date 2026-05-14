<?php 

// Vista/Tutores/Steps/Convenios.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

// Preparamos la URL completa (ajusta la base si es necesario)
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$id_ciclo = $_SESSION['id_ciclo'] ?? '';
$urlCompartir = $protocolo . "://" . $host . "/PROYECTO/Convenios/Registro.php?id_ciclo=" . urlencode($id_ciclo);

?>
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold flex items-center gap-3">🏢 Gestión de Convenios</h2>
    
    <div class="flex items-center gap-2">
        <button type="button" 
                onclick="copiarUrlRegistro('<?= $urlCompartir ?>', this)"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-100 border border-slate-200 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-200 transition-all shadow-sm cursor-pointer group">
            <span id="btn-text">📋 Copiar enlace</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 group-active:scale-90 transition-transform">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
            </svg>
        </button>

        <a href="Convenios/Registro.php?id_ciclo=<?= urlencode($id_ciclo) ?>" 
           class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-slate-900 transition-all shadow-lg">
            <span class="text-sm">+</span> Registro Convenio
        </a>
    </div>
</div>

<form action="index.php" method="POST" class="flex gap-3 w-full mb-10">
    <input type="text" name="busqueda_convenio" value="<?= htmlspecialchars($_POST['busqueda_convenio'] ?? '') ?>" 
        placeholder="CIF O NOMBRE DE EMPRESA..." 
        class="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-6 py-4 outline-none focus:ring-4 focus:ring-orange-50 text-xs font-bold uppercase transition-all">
    <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg cursor-pointer">Buscar</button>
    <button type="button" onclick="this.closest('form').querySelector('[name=busqueda_convenio]').value=''; this.closest('form').submit();"
        class="flex items-center gap-1.5 px-6 py-4 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-500 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-widest shadow-sm whitespace-nowrap">
        Mostrar todos
    </button>
</form>

<?php if (isset($_POST['busqueda_convenio']) && trim($_POST['busqueda_convenio']) !== ''): ?>
    <div class="mb-10">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 text-center">Resultados de la búsqueda</h3>
        <div class="flex items-center justify-between mb-2">
            <span id="rs-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
            <button type="button" onclick="abrirModalPag('rs')" title="Configurar filas por página"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span id="rs-pag-label">10/pág</span>
            </button>
        </div>
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
                <tbody id="rs-tbody" class="divide-y divide-slate-100">
                    <?php if (!empty($convenios)): foreach ($convenios as $c): ?>
                        <tr class="rs-fila hover:bg-slate-50 transition-colors">
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
                                <form action="index.php" method="POST">
                                    <input type="hidden" name="id_convenio_fav" value="<?= $c['id_convenio'] ?>">
                                    
                                    <input type="hidden" name="busqueda_convenio" value="<?= htmlspecialchars($_POST['busqueda_convenio'] ?? '') ?>">
                                    
                                    <button type="submit" name="btnFavorito" 
                                            class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all cursor-pointer">
                                        ⭐ Añadir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-red-500 text-sm font-black uppercase italic">⚠ No hay convenios que coincidan con "<?= htmlspecialchars($_POST['busqueda_convenio']) ?>".</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <div id="rs-paginacion" class="hidden flex items-center justify-center mt-3 gap-1.5">
        <button id="rs-prev" onclick="rsCambiarPagina(rsPaginaActual - 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Anterior
        </button>
        <div id="rs-paginas" class="flex items-center gap-1.5"></div>
        <button id="rs-next" onclick="rsCambiarPagina(rsPaginaActual + 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
    </div>
<?php endif; ?>

<div class="mt-12">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-[10px] font-black text-orange-600 uppercase tracking-[0.2em]">Mi Listado Personal</h3>
        <div class="flex items-center gap-2">
            <span id="lp-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
            <button type="button" onclick="abrirModalPag('lp')" title="Configurar filas por página"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer uppercase tracking-wide">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span id="lp-pag-label">6/pág</span>
            </button>
        </div>
    </div>
    <div class="overflow-hidden rounded-2xl border-2 border-orange-100 bg-white">
        <table class="w-full text-left border-collapse table-fixed">
            <thead class="bg-orange-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-[10px] font-black uppercase tracking-widest">Empresa Seleccionada</th>
                    <th class="w-48 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-center">Gestión</th>
                </tr>
            </thead>
            <tbody id="lp-tbody" class="divide-y divide-orange-50">
                <?php if (!empty($misConvenios)): foreach ($misConvenios as $mc): ?>
                    <tr class="lp-fila hover:bg-orange-50/50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="font-bold text-slate-900 uppercase text-sm"><?= $mc['nombre_empresa'] ?></div>
                            <div class="text-xs text-slate-400 font-bold"><?= $mc['municipio'] ?></div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="index.php" method="POST" class="flex justify-center">
                                <input type="hidden" name="id_convenio_eliminar" value="<?= $mc['id_convenio'] ?>">
                                <input type="hidden" name="busqueda_convenio" value="<?= htmlspecialchars($_POST['busqueda_convenio'] ?? '') ?>">
                                <input type="hidden" name="btnEliminarFav" value="1">
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

    <!-- Controles de paginación -->
    <div id="lp-paginacion" class="flex items-center justify-between mt-3 hidden">
        <button id="lp-prev" onclick="lpCambiarPagina(lpPaginaActual - 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Anterior
        </button>

        <div id="lp-paginas" class="flex items-center gap-1.5"></div>

        <button id="lp-next" onclick="lpCambiarPagina(lpPaginaActual + 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</div>

<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em] flex items-center gap-2">
            ⏳ Convenios en Proceso
        </h3>
        <div class="flex items-center gap-2">
            <span id="cp-contador" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"></span>
            <button type="button" onclick="abrirModalPag('cp')" title="Configurar filas por página"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-[9px] font-black text-slate-400 hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 transition-all cursor-pointer uppercase tracking-wide">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span id="cp-pag-label">10/pág</span>
            </button>
        </div>
    </div>
    <div class="overflow-hidden rounded-2xl border-2 border-amber-100 bg-white shadow-sm">
        <table class="w-full text-left border-collapse table-fixed"> <thead class="bg-amber-500 text-white">
                <tr>
                    <th class="w-32 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center">Editar</th>
                    
                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-left">Empresa</th>
                    
                    <th class="w-48 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-center">Acción</th>
                </tr>
            </thead>
            <tbody id="cp-tbody" class="divide-y divide-amber-50">
                <?php if (!empty($conveniosProceso)): ?>
                    <?php foreach ($conveniosProceso as $convP): ?>
                        <tr class="cp-fila hover:bg-amber-50/50 transition-colors">
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
                                    <input type="hidden" name="busqueda_convenio" value="<?= htmlspecialchars($_POST['busqueda_convenio'] ?? '') ?>">
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
    <div id="cp-paginacion" class="hidden flex items-center justify-center mt-3 gap-1.5">
        <button id="cp-prev" onclick="cpCambiarPagina(cpPaginaActual - 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Anterior
        </button>
        <div id="cp-paginas" class="flex items-center gap-1.5"></div>
        <button id="cp-next" onclick="cpCambiarPagina(cpPaginaActual + 1)"
            class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:text-slate-400 disabled:hover:border-slate-200">
            Siguiente
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</div>

<script>
// ─── PAGINACIÓN: RESULTADOS DE BÚSQUEDA ─────────────────────────────────────
let rsPorPagina = parseInt(localStorage.getItem('pag_rs_porPagina')) || 10;
let rsPaginaActual = 1;

function rsInicializar() {
    const filas = Array.from(document.querySelectorAll('#rs-tbody .rs-fila'));
    const total = filas.length;
    const label = document.getElementById('rs-pag-label');
    if (label) label.textContent = rsPorPagina + '/pág';
    const pag = document.getElementById('rs-paginacion');
    const contador = document.getElementById('rs-contador');
    if (!pag) return;
    if (total <= rsPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} resultado${total !== 1 ? 's' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    rsRenderizar();
}

function rsCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#rs-tbody .rs-fila');
    const totalPaginas = Math.ceil(filas.length / rsPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    rsPaginaActual = nuevaPagina;
    rsRenderizar();
}

function rsRenderizar() {
    const filas = Array.from(document.querySelectorAll('#rs-tbody .rs-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / rsPorPagina);
    const inicio = (rsPaginaActual - 1) * rsPorPagina;
    const fin    = Math.min(inicio + rsPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('rs-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('rs-prev').disabled = rsPaginaActual === 1;
    document.getElementById('rs-next').disabled = rsPaginaActual === totalPaginas;

    const contenedor = document.getElementById('rs-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, rsPaginaActual, rsPaginaActual - 1, rsPaginaActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas));
    [...pagsMostrar].sort((a, b) => a - b).forEach((p, idx, arr) => {
        const prev = arr[idx - 1];
        if (prev !== undefined && p - prev > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => rsCambiarPagina(p);
        btn.className = p === rsPaginaActual
            ? 'w-8 h-8 rounded-lg bg-orange-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', rsInicializar);

// ─── PAGINACIÓN: CONVENIOS EN PROCESO ────────────────────────────────────────
let cpPorPagina = parseInt(localStorage.getItem('pag_cp_porPagina')) || 10;
let cpPaginaActual = 1;

function cpInicializar() {
    const filas = Array.from(document.querySelectorAll('#cp-tbody .cp-fila'));
    const total = filas.length;
    const label = document.getElementById('cp-pag-label');
    if (label) label.textContent = cpPorPagina + '/pág';
    const pag = document.getElementById('cp-paginacion');
    const contador = document.getElementById('cp-contador');
    if (total <= cpPorPagina) {
        if (pag) pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} convenio${total !== 1 ? 's' : ''}` : '';
        return;
    }
    if (pag) pag.classList.remove('hidden');
    cpRenderizar();
}

function cpCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#cp-tbody .cp-fila');
    const totalPaginas = Math.ceil(filas.length / cpPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    cpPaginaActual = nuevaPagina;
    cpRenderizar();
}

function cpRenderizar() {
    const filas = Array.from(document.querySelectorAll('#cp-tbody .cp-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / cpPorPagina);
    const inicio = (cpPaginaActual - 1) * cpPorPagina;
    const fin    = Math.min(inicio + cpPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('cp-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('cp-prev').disabled = cpPaginaActual === 1;
    document.getElementById('cp-next').disabled = cpPaginaActual === totalPaginas;

    const contenedor = document.getElementById('cp-paginas');
    contenedor.innerHTML = '';
    const pagsMostrar = new Set([1, totalPaginas, cpPaginaActual, cpPaginaActual - 1, cpPaginaActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas));
    [...pagsMostrar].sort((a, b) => a - b).forEach((p, idx, arr) => {
        const prev = arr[idx - 1];
        if (prev !== undefined && p - prev > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => cpCambiarPagina(p);
        btn.className = p === cpPaginaActual
            ? 'w-8 h-8 rounded-lg bg-amber-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
    });
}

document.addEventListener('DOMContentLoaded', cpInicializar);

// ─── PAGINACIÓN: MI LISTADO PERSONAL ─────────────────────────────────────────
let lpPorPagina = parseInt(localStorage.getItem('pag_lp_porPagina')) || 6;
let lpPaginaActual = 1;

function lpInicializar() {
    const filas = Array.from(document.querySelectorAll('#lp-tbody .lp-fila'));
    const total = filas.length;
    const label = document.getElementById('lp-pag-label');
    if (label) label.textContent = lpPorPagina + '/pág';
    const pag = document.getElementById('lp-paginacion');
    const contador = document.getElementById('lp-contador');
    if (total <= lpPorPagina) {
        pag.classList.add('hidden');
        filas.forEach(f => f.style.display = '');
        if (contador) contador.textContent = total > 0 ? `${total} registro${total !== 1 ? 's' : ''}` : '';
        return;
    }
    pag.classList.remove('hidden');
    lpRenderizar();
}

function lpCambiarPagina(nuevaPagina) {
    const filas = document.querySelectorAll('#lp-tbody .lp-fila');
    const totalPaginas = Math.ceil(filas.length / lpPorPagina);
    if (nuevaPagina < 1 || nuevaPagina > totalPaginas) return;
    lpPaginaActual = nuevaPagina;
    lpRenderizar();
}

function lpRenderizar() {
    const filas = Array.from(document.querySelectorAll('#lp-tbody .lp-fila'));
    const total = filas.length;
    const totalPaginas = Math.ceil(total / lpPorPagina);
    const inicio = (lpPaginaActual - 1) * lpPorPagina;
    const fin    = Math.min(inicio + lpPorPagina, total);

    filas.forEach((fila, i) => {
        fila.style.display = (i >= inicio && i < fin) ? '' : 'none';
    });

    const contador = document.getElementById('lp-contador');
    if (contador) contador.textContent = `Mostrando ${inicio + 1}–${fin} de ${total}`;

    document.getElementById('lp-prev').disabled = lpPaginaActual === 1;
    document.getElementById('lp-next').disabled = lpPaginaActual === totalPaginas;

    const contenedor = document.getElementById('lp-paginas');
    contenedor.innerHTML = '';

    const pagsMostrar = new Set([1, totalPaginas, lpPaginaActual, lpPaginaActual - 1, lpPaginaActual + 1]
        .filter(p => p >= 1 && p <= totalPaginas));
    const pagsOrdenadas = [...pagsMostrar].sort((a, b) => a - b);

    let anterior = null;
    pagsOrdenadas.forEach(p => {
        if (anterior !== null && p - anterior > 1) {
            const sep = document.createElement('span');
            sep.className = 'text-slate-300 text-xs font-bold px-1';
            sep.textContent = '···';
            contenedor.appendChild(sep);
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = p;
        btn.onclick = () => lpCambiarPagina(p);
        btn.className = p === lpPaginaActual
            ? 'w-8 h-8 rounded-lg bg-orange-600 text-white text-[10px] font-black cursor-pointer shadow-sm'
            : 'w-8 h-8 rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-orange-300 hover:text-orange-600 hover:bg-orange-50 transition-all cursor-pointer';
        contenedor.appendChild(btn);
        anterior = p;
    });
}

document.addEventListener('DOMContentLoaded', lpInicializar);

// ─── Modal configurar paginación ─────────────────────────────────────────────
window._pagCallbacks = window._pagCallbacks || {};
window._pagCallbacks['lp'] = function(n) { lpPorPagina = n; lpPaginaActual = 1; lpInicializar(); };
window._pagCallbacks['rs'] = function(n) { rsPorPagina = n; rsPaginaActual = 1; rsInicializar(); };
window._pagCallbacks['cp'] = function(n) { cpPorPagina = n; cpPaginaActual = 1; cpInicializar(); };

function abrirModalPag(prefix) {
    const defaults = { lp: 6, seg: 6 };
    const val = parseInt(localStorage.getItem('pag_' + prefix + '_porPagina')) || defaults[prefix] || 10;
    document.getElementById('input-pag-' + prefix).value = val;
    document.getElementById('modal-pag-' + prefix).style.display = 'flex';
}
function cerrarModalPag(prefix) {
    document.getElementById('modal-pag-' + prefix).style.display = 'none';
}
function setPagPreset(prefix, n) {
    document.getElementById('input-pag-' + prefix).value = n;
}
function aplicarPag(prefix) {
    const val = parseInt(document.getElementById('input-pag-' + prefix).value);
    if (!val || val < 1) return;
    localStorage.setItem('pag_' + prefix + '_porPagina', val);
    const label = document.getElementById(prefix + '-pag-label');
    if (label) label.textContent = val + '/pág';
    cerrarModalPag(prefix);
    if (window._pagCallbacks[prefix]) window._pagCallbacks[prefix](val);
}
// ─────────────────────────────────────────────────────────────────────────────

    function copiarUrlRegistro(url, elemento) {
        // Copiar al portapapeles
        navigator.clipboard.writeText(url).then(() => {
            const span = elemento.querySelector('#btn-text');
            const originalText = span.innerText;
            
            // Feedback visual
            span.innerText = '¡COPIADO!';
            elemento.classList.remove('bg-slate-100', 'text-slate-600');
            elemento.classList.add('bg-emerald-500', 'text-white', 'border-emerald-600');
            
            // Revertir después de 2 segundos
            setTimeout(() => {
                span.innerText = originalText;
                elemento.classList.remove('bg-emerald-500', 'text-white', 'border-emerald-600');
                elemento.classList.add('bg-slate-100', 'text-slate-600');
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar: ', err);
        });
    }
</script>

<!-- ─── Modal Configurar Paginación: Mi Listado Personal ──────────────────── -->
<div id="modal-pag-lp" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('lp')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('lp')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('lp', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('lp', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('lp', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('lp', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('lp', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('lp', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-lp" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('lp')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('lp')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('lp')" class="px-4 py-2 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<!-- ─── Modal Configurar Paginación: Resultados de Búsqueda ───────────────── -->
<div id="modal-pag-rs" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('rs')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('rs')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('rs', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('rs', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('rs', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('rs', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('rs', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('rs', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-rs" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-orange-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('rs')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('rs')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('rs')" class="px-4 py-2 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<!-- ─── Modal Configurar Paginación: Convenios en Proceso ─────────────────── -->
<div id="modal-pag-cp" style="display:none"
     class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
     onclick="if(event.target===this)cerrarModalPag('cp')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs p-6 border border-slate-100">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Configurar Paginación
            </h3>
            <button onclick="cerrarModalPag('cp')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer leading-none">✕</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Acceso rápido</p>
        <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="setPagPreset('cp', 5)"  class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">5</button>
            <button type="button" onclick="setPagPreset('cp', 10)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">10</button>
            <button type="button" onclick="setPagPreset('cp', 15)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">15</button>
            <button type="button" onclick="setPagPreset('cp', 20)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">20</button>
            <button type="button" onclick="setPagPreset('cp', 25)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">25</button>
            <button type="button" onclick="setPagPreset('cp', 50)" class="px-3 py-2 rounded-lg border border-slate-200 text-[11px] font-black text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-all cursor-pointer">50</button>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Cantidad personalizada</p>
        <div class="flex items-center gap-3 mb-5">
            <input type="number" id="input-pag-cp" min="1" max="200" placeholder="Ej: 12"
                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-amber-200 transition-all"
                onkeydown="if(event.key==='Enter')aplicarPag('cp')">
            <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">por página</span>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="cerrarModalPag('cp')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition-all">Cancelar</button>
            <button onclick="aplicarPag('cp')" class="px-4 py-2 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-700 transition-all shadow-sm cursor-pointer">Aplicar</button>
        </div>
    </div>
</div>

<?php include 'Vista/Tutores/Components/Modales_Convenios.php'; ?>