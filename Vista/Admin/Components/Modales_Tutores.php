<?php 
// Vista/Admin/Components/Modales_Tutores.php
?>
<div id="modalConfirmarEliminar" style="display:none" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 border border-slate-100" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase tracking-tight">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-600 text-white text-xs">⚠️</span>
                Eliminar Registro
            </h3>
            <button onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold cursor-pointer">✕</button>
        </div>
        
        <p class="text-[10px] font-black text-slate-400 mb-1 text-center uppercase tracking-[0.2em]">¿Seguro que desea eliminar a?</p>
        <p id="nombreTutorEliminar" class="text-sm font-black text-slate-900 mb-4 text-center uppercase"></p>
        
        <div class="bg-red-50 p-4 rounded-xl mb-6 border border-red-100">
            <p class="text-[10px] text-red-700 font-bold text-center leading-relaxed">
                ESTA ACCIÓN ES IRREVERSIBLE.<br>Se eliminarán también sus credenciales de acceso al sistema.
            </p>
        </div>

        <form method="POST" action="index.php" id="formEliminarTutor">
            <input type="hidden" name="accion" value="eliminarTutor">
            <input type="hidden" name="id_tutor" id="eliminar_id_tutor">
            
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="document.getElementById('modalConfirmarEliminar').style.display='none'" 
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer uppercase transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 shadow-md shadow-red-100 cursor-pointer uppercase tracking-wider transition-all">
                    Sí, eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalAgregarTutor" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase tracking-tight">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-white text-xs">👨‍🏫</span>
                Nuevo Tutor
            </h3>
            <button onclick="document.getElementById('modalAgregarTutor').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
        </div>

        <form method="POST" action="index.php">
            <input type="hidden" name="accion" value="guardarTutor">

            <div class="mb-4">
                <label class="block text-[10px] font-black text-orange-600 uppercase tracking-widest mb-1">Ciclo Disponible <span class="text-red-500">*</span></label>
                <select name="id_ciclo" required class="w-full px-4 py-2.5 rounded-xl border-2 border-orange-100 bg-orange-50/30 text-xs font-bold uppercase outline-none focus:border-orange-500 transition-all cursor-pointer">
                    <option value="">-- SELECCIONA CICLO --</option>
                    <?php foreach ($ciclosLibres as $c): ?>
                        <?php $prefijo = ($c['nombre_curso'] == 'Primero') ? '1º' : '2º'; ?>
                        <option value="<?= $c['id_ciclo'] ?>"><?= $prefijo ?> <?= $c['nombre_ciclo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Apellidos <span class="text-red-500">*</span></label>
                <input type="text" name="apellidos" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>

            <div class="flex gap-3 mb-4">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">DNI / NIE <span class="text-red-500">*</span></label>
                    <input type="text" name="dni" required maxlength="9" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase font-mono outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono <span class="text-red-500">*</span></label>
                    <input type="text" name="telefono" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-all">
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modalAgregarTutor').style.display='none'" 
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer uppercase">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-orange-600 text-white text-xs font-bold hover:bg-orange-700 transition-all shadow-md cursor-pointer uppercase tracking-wider">
                    Guardar Tutor
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarTutor" style="display:none" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) this.style.display='none'">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border border-slate-100" onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2 uppercase tracking-tight">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-900 text-white text-xs">✏️</span>
                Editar Perfil
            </h3>
            <button onclick="document.getElementById('modalEditarTutor').style.display='none'" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none cursor-pointer">✕</button>
        </div>

        <form method="POST" action="index.php">
            <input type="hidden" name="accion" value="actualizarTutor">
            <input type="hidden" name="id_tutor" id="edit_id">

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Cambiar Ciclo Asignado</label>
                <select name="id_ciclo" id="edit_ciclo" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-[11px] font-black uppercase outline-none transition-all cursor-pointer shadow-sm bg-white">
                    <?php 
                        $todosLosCiclos = $this->admin->obtenerTodosLosCiclos(); 
                        foreach ($todosLosCiclos as $c): 
                            $cursoLimpio = mb_strtolower(trim($c['nombre_curso']));
                            $prefijo = ($cursoLimpio == 'primero') ? "1º" : (($cursoLimpio == 'segundo') ? "2º" : $c['nombre_curso']);
                            
                            $idOcupante = $c['ocupado_por']; // ID del tutor que lo tiene (si existe)

                            // Definimos los estilos por defecto
                            if (!empty($idOcupante)) {
                                $labelEstado = '— OCUPADO';
                                $estiloFondo = 'background-color: #fee2e2; color: #991b1b;'; // Rojo (Ocupado por otro)
                            } else {
                                $labelEstado = '— DISPONIBLE';
                                $estiloFondo = 'background-color: #dcfce7; color: #166534;'; // Verde (Libre)
                            }
                    ?>
                        <option value="<?= $c['id_ciclo'] ?>" 
                                style="<?= $estiloFondo ?>" 
                                data-tutor="<?= $idOcupante ?>" 
                                data-original-label="<?= $prefijo ?> <?= htmlspecialchars($c['nombre_ciclo']) ?>"
                                class="py-3 font-medium">
                            <?= $prefijo ?> <?= htmlspecialchars($c['nombre_ciclo']) ?> <?= $labelEstado ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 transition-all">
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Apellidos</label>
                <input type="text" name="apellidos" id="edit_apellidos" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold uppercase outline-none focus:ring-2 focus:ring-orange-200 transition-all">
            </div>

            <div class="flex gap-3 mb-6">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 transition-all">
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold outline-none focus:ring-2 focus:ring-orange-200 transition-all">
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('modalEditarTutor').style.display='none'" 
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer uppercase">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-orange-600 transition-all shadow-md cursor-pointer uppercase tracking-wider">
                    Actualizar Datos
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function actualizarEstiloSelect(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            select.style.backgroundColor = selectedOption.style.backgroundColor;
            select.style.color = selectedOption.style.color;
            // Opcional: un toque de sombra para que resalte al cambiar
            select.style.boxShadow = "0 4px 6px -1px rgb(0 0 0 / 0.1)";
        }
    }

    // Configurar el evento una sola vez al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        const selectCiclo = document.getElementById('edit_ciclo');
        if (selectCiclo) {
            selectCiclo.addEventListener('change', function() {
                actualizarEstiloSelect(this);
            });
        }
    });

    function abrirEditarTutor(datos) {
        const selectCiclo = document.getElementById('edit_ciclo');
        
        // 1. Rellenar campos básicos
        document.getElementById('edit_id').value = datos.id_tutor;
        document.getElementById('edit_nombre').value = datos.nombre;
        document.getElementById('edit_apellidos').value = datos.apellidos;
        document.getElementById('edit_telefono').value = datos.telefono;
        document.getElementById('edit_email').value = datos.email;
        
        // 2. Iterar las opciones para marcar el "Ciclo Actual" dinámicamente
        Array.from(selectCiclo.options).forEach(option => {
            const idTutorOcupante = option.getAttribute('data-tutor');
            const nombreCiclo = option.getAttribute('data-original-label');

            if (idTutorOcupante == datos.id_tutor) {
                // Es el ciclo que tiene asignado este tutor actualmente
                option.innerText = nombreCiclo + " — TU CICLO ACTUAL";
                option.style.backgroundColor = "#e0f2fe"; // Azul celeste
                option.style.color = "#075985";
            } else if (!idTutorOcupante) {
                // Está libre
                option.innerText = nombreCiclo + " — DISPONIBLE";
                option.style.backgroundColor = "#dcfce7";
                option.style.color = "#166534";
            } else {
                // Ocupado por otra persona
                option.innerText = nombreCiclo + " — OCUPADO";
                option.style.backgroundColor = "#fee2e2";
                option.style.color = "#991b1b";
            }
        });

        // 3. Seleccionar el valor y aplicar estilo al select
        selectCiclo.value = datos.id_ciclo;
        actualizarEstiloSelect(selectCiclo);
        
        document.getElementById('modalEditarTutor').style.display = 'flex';
    }

    function abrirModalEliminar(datos) {
        // Rellenamos el nombre en el texto del modal
        document.getElementById('nombreTutorEliminar').innerText = datos.nombre + " " + datos.apellidos;
        
        // Pasamos el ID al input hidden del formulario de eliminación
        document.getElementById('eliminar_id_tutor').value = datos.id_tutor;
        
        // Mostramos el modal
        document.getElementById('modalConfirmarEliminar').style.display = 'flex';
    }

    // Accesibilidad: Permite cerrar cualquier modal activo al pulsar la tecla 'Esc'
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Se ocultan todos los contenedores de modales por ID
            document.getElementById('modalEditarTutor').style.display = 'none';
            document.getElementById('modalConfirmarEliminar').style.display = 'none';
            document.getElementById('modalAgregarTutor').style.display = 'none';
        }
    });
</script>