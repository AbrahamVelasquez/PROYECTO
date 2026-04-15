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

                        <form method="POST" action="index.php" onsubmit="return confirm('¿Confirmas que deseas validar e incorporar esta empresa directamente?');">
                            <input type="hidden" name="accion" value="validarConvenio">
                            <input type="hidden" name="id_convenio_nuevo" value="<?= $p['id_convenio_nuevo'] ?>">
                            
                            <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100 cursor-pointer flex items-center gap-2">
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
        </tbody>
    </table>
</div>

<div id="modalRevisionConvenio" style="display:none" class="fixed inset-0 bg-slate-900/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-slate-100 animate-in fade-in zoom-in duration-200">
        
        <div class="px-8 py-5 bg-white border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Revisión de Solicitud</h3>
                <p class="text-emerald-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">Verifique los datos antes de la incorporación definitiva</p>
            </div>
            <button onclick="cerrarModalRevision()" class="text-slate-400 hover:text-slate-600 transition-colors text-2xl cursor-pointer">✕</button>
        </div>

        <form action="index.php" method="POST" class="overflow-y-auto p-8 bg-slate-50/30">
            <input type="hidden" name="accion" value="validarConvenio">
            <input type="hidden" name="solo_guardar" value="1">
            <input type="hidden" name="id_convenio_nuevo" id="rev_id">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Empresa</label>
                    <input type="text" name="nombre_empresa" id="rev_nombre" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CIF</label>
                    <input type="text" name="cif" id="rev_cif" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Teléfono</label>
                    <input type="text" name="telefono" id="rev_tel" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Email</label>
                    <input type="email" name="mail" id="rev_mail" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Fax</label>
                    <input type="text" name="fax" id="rev_fax" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Dirección</label>
                    <input type="text" name="direccion" id="rev_dir" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Municipio</label>
                    <input type="text" name="municipio" id="rev_mun" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">CP</label>
                    <input type="text" name="cp" id="rev_cp" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">País</label>
                    <input type="text" name="pais" id="rev_pais" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>

                <div class="md:col-span-3 mt-4 pt-4 border-t border-slate-200 flex items-center gap-2">
                    <span class="text-[10px] font-black bg-slate-800 text-white px-2 py-0.5 rounded">REPRESENTANTE LEGAL</span>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Nombre Representante</label>
                    <input type="text" name="nombre_representante" id="rev_rep_nom" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">DNI</label>
                    <input type="text" name="dni_representante" id="rev_rep_dni" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 ml-1 tracking-widest">Cargo</label>
                    <input type="text" name="cargo" id="rev_rep_cargo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-emerald-100 transition-all">
                </div>
            </div>

            <div class="flex gap-4 mt-10">
                <button type="button" onclick="cerrarModalRevision()" 
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                    Cancelar
                </button>
                
                <button type="submit" 
                        class="flex-1 py-3 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg hover:bg-slate-900 transition-all">
                    Solo Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalRevision(datos) {
    document.getElementById('rev_id').value = datos.id_convenio_nuevo;
    document.getElementById('rev_nombre').value = datos.nombre_empresa;
    document.getElementById('rev_cif').value = datos.cif;
    document.getElementById('rev_tel').value = datos.telefono;
    document.getElementById('rev_mail').value = datos.mail;
    document.getElementById('rev_fax').value = datos.fax;
    document.getElementById('rev_dir').value = datos.direccion;
    document.getElementById('rev_mun').value = datos.municipio;
    document.getElementById('rev_cp').value = datos.cp;
    document.getElementById('rev_pais').value = datos.pais;
    document.getElementById('rev_rep_nom').value = datos.nombre_representante;
    document.getElementById('rev_rep_dni').value = datos.dni_representante;
    document.getElementById('rev_rep_cargo').value = datos.cargo;

    document.getElementById('modalRevisionConvenio').style.display = 'flex';
}

function cerrarModalRevision() {
    document.getElementById('modalRevisionConvenio').style.display = 'none';
}
</script>