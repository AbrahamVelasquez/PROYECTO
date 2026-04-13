<div class="pt-8 border-t border-slate-100 flex flex-wrap gap-4 justify-between items-center">
    <button type="button" onclick="volverALista()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-[10px] transition-all uppercase tracking-widest cursor-pointer border border-slate-200">
        ← VOLVER AL LISTADO
    </button>
    
    <div class="flex gap-3">
        <div class="relative group">
            <?php $idEditar = $_GET['editar'] ?? 0; ?>

            <button type="button" 
                    id="btn-devolver-alumno"
                    onclick="abrirModalDevolver(<?= $idEditar ?>, '<?= htmlspecialchars($datosAlumno['nombre'] ?? '') ?>')" 
                    class="px-6 py-3 bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 rounded-xl font-bold text-[10px] flex items-center gap-2 transition-all cursor-pointer uppercase tracking-widest">
                DEVOLVER ALUMNO 
                <span class="bg-red-200 text-red-700 rounded-full h-4 w-4 flex items-center justify-center text-[9px]">!</span>
            </button>

            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 hidden group-hover:block w-56 p-3 bg-slate-900 text-white text-[9px] rounded-xl shadow-xl leading-relaxed z-10 text-center tracking-tighter">
                Acción excepcional para reasignar al alumno.
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
            </div>
        </div>

        <button type="button" 
                onclick="abrirModalExportarPF(document.getElementById('edit_id_asignacion')?.value)" 
                class="bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md flex items-center gap-2 cursor-pointer uppercase tracking-wide">
            <span>📤</span> GENERAR Y EXPORTAR EXCEL
        </button>
    </div>
</div>

<script>
function volverALista() {
    window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
}

window.exportarYMarcar = async function(idAsignacion) {
    const idDefinitivo = idAsignacion || document.getElementById('edit_id_asignacion')?.value;

    if (!idDefinitivo) {
        alert("Error: No se encuentra el ID de la asignación.");
        return;
    }

    try {
        const res = await fetch('index.php?controlador=Tutores&accion=marcarComoExportado', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_asignacion=${encodeURIComponent(idDefinitivo)}`
        });

        const textoBruto = await res.text();
        // Buscamos el JSON dentro del texto por si PHP soltó algún warning invisible
        const inicioJson = textoBruto.indexOf('{');
        const finJson = textoBruto.lastIndexOf('}') + 1;
        const jsonLimpio = textoBruto.substring(inicioJson, finJson);

        const data = JSON.parse(jsonLimpio);

        if (data.success) {
            // ÉXITO: Redirigimos al panel con la pestaña 3 activa
            window.location.href = "index.php?controlador=Tutores&accion=mostrarPanel&tab=3";
        } else {
            alert("No se pudo actualizar la base de datos. Verifica si el ID " + idDefinitivo + " existe.");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Error de comunicación. Revisa la consola.");
    }
};
</script>