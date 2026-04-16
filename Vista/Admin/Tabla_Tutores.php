<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 px-2">
    <div>
        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Personal Docente</h2>
        <p class="text-slate-500 text-[11px] uppercase font-bold tracking-[0.2em] mt-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            Total registrados: <?= count($tutores) ?>
        </p>
    </div>
    <div class="flex items-center gap-4">
        <form action="index.php" method="POST">
            <input type="hidden" name="accion" value="mostrarPanel">
            <button type="submit" class="group flex items-center gap-2 text-slate-400 px-4 py-2 text-xs font-bold hover:text-orange-600 transition-all cursor-pointer">
                <span class="transition-transform group-hover:-translate-x-1">←</span> Volver al inicio
            </button>
        </form>
        <button onclick="document.getElementById('modalAgregarTutor').style.display='flex'" 
                class="bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-orange-700 transition-all shadow-md shadow-orange-100 cursor-pointer">
            + Agregar Tutor
        </button>
    </div>
</div>

<form method="POST" action="index.php" class="flex flex-col lg:flex-row gap-4 mb-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-100 items-center">
    <input type="hidden" name="accion" value="mostrarTutores">

    <div class="flex-1 relative w-full">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
        <input type="text" name="busqueda" value="<?= htmlspecialchars($_POST['busqueda'] ?? '') ?>" placeholder="BUSCAR POR NOMBRE O APELLIDOS..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-[10px] font-bold outline-none focus:ring-2 focus:ring-orange-100 transition-all uppercase">
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Ordenar por:</span>
        <select name="ordenar" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="id"        <?= (!isset($_POST['ordenar']) || $_POST['ordenar'] == 'id') ? 'selected' : '' ?>>Nº REGISTRO (ID)</option>
            <option value="apellidos" <?= ($_POST['ordenar'] ?? '') == 'apellidos' ? 'selected' : '' ?>>APELLIDOS (A-Z)</option>
            <option value="ciclo"     <?= ($_POST['ordenar'] ?? '') == 'ciclo'     ? 'selected' : '' ?>>CURSO Y CICLO</option>
        </select>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Curso:</span>
        <select name="filtro_curso" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[10px] font-bold outline-none cursor-pointer uppercase">
            <option value="">TODOS LOS CURSOS</option>
            <option value="Primero" <?= ($_POST['filtro_curso'] ?? '') == 'Primero' ? 'selected' : '' ?>>1º CURSO</option>
            <option value="Segundo" <?= ($_POST['filtro_curso'] ?? '') == 'Segundo' ? 'selected' : '' ?>>2º CURSO</option>
        </select>
    </div>

    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold text-[10px] hover:bg-orange-600 transition-all shadow-sm uppercase tracking-wider cursor-pointer">
        BUSCAR
    </button>
</form>

<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-slate-700">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">ID</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-32">DNI</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tutor</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Información de Contacto</th>
                <th class="py-5 px-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest w-48">Ciclo</th>
                <th class="py-5 px-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest w-28">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (empty($tutores)): ?>
                <tr>
                    <td colspan="6" class="py-20 text-center">
                        <p class="text-slate-400 text-sm italic font-medium">No se han encontrado tutores registrados.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tutores as $fila): ?>
                <tr class="hover:bg-slate-50/40 transition-all duration-200 group">
                    <td class="py-5 px-6">
                        <span class="font-mono text-[11px] font-bold text-slate-300 group-hover:text-orange-400 transition-colors">#<?= $fila['id_tutor'] ?></span>
                    </td>
                    <td class="py-5 px-6">
                        <span class="font-mono text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-1 rounded-md"><?= $fila['dni'] ?></span>
                    </td>
                    <td class="py-5 px-6">
                        <span class="font-bold text-slate-800 text-sm uppercase tracking-tight">
                            <?= $fila['apellidos'] ?> <?= $fila['nombre'] ?> 
                        </span>
                    </td>
                    <td class="py-5 px-6">
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <span class="font-medium"><?= $fila['email'] ?></span>
                            <span class="text-slate-200">|</span>
                            <span class="font-bold text-slate-400"><?= $fila['telefono'] ?></span>
                        </div>
                    </td>
                    <td class="py-5 px-6">
                        <?php if (!empty($fila['nombre_ciclo'])): ?>
                            <?php 
                                $cursoLimpio = mb_strtolower(trim($fila['nombre_curso']));
                                $abreviatura = ($cursoLimpio == 'primero') ? "1º" : (($cursoLimpio == 'segundo') ? "2º" : $fila['nombre_curso']);
                            ?>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-50 border border-orange-100 text-orange-700">
                                <span class="text-[11px] font-black italic"><?= $abreviatura ?></span>
                                <span class="text-[10px] font-bold uppercase tracking-tight"><?= $fila['nombre_ciclo'] ?></span>
                            </div>
                        <?php else: ?>
                            <span class="text-slate-300 text-[10px] font-bold uppercase tracking-widest">Sin asignar</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-5 px-6">
                        <div class="flex justify-center gap-1">
                            <button onclick='abrirEditarTutor(<?= json_encode($fila) ?>)' 
                                    title="Editar" 
                                    class="p-2 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button type="button" 
                                    onclick='abrirModalEliminar(<?= json_encode($fila) ?>)' 
                                    title="Eliminar" 
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

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
                    $todosLosCiclos = $admin->obtenerTodosLosCiclos(); 
                    foreach ($todosLosCiclos as $c): 
                        $cursoLimpio = mb_strtolower(trim($c['nombre_curso']));
                        $prefijo = ($cursoLimpio == 'primero') ? "1º" : (($cursoLimpio == 'segundo') ? "2º" : $c['nombre_curso']);
                        
                        $esOcupado = !empty($c['ocupado_por']);
                        
                        // LÓGICA DE ETIQUETAS Y COLORES
                        // Usaremos un color azul/celeste para identificar el ciclo actual del tutor
                        $esSuCicloActual = ($esOcupado && isset($fila['id_tutor']) && $c['ocupado_por'] == $fila['id_tutor']);

                        if ($esSuCicloActual) {
                            $labelEstado = '— CICLO ACTUAL';
                            $estiloFondo = 'background-color: #e0f2fe; color: #075985;'; // Azul claro
                        } elseif ($esOcupado) {
                            $labelEstado = '— OCUPADO';
                            $estiloFondo = 'background-color: #fee2e2; color: #991b1b;'; // Rojo
                        } else {
                            $labelEstado = '— DISPONIBLE';
                            $estiloFondo = 'background-color: #dcfce7; color: #166534;'; // Verde
                        }
                ?>
                    <option value="<?= $c['id_ciclo'] ?>" 
                            style="<?= $estiloFondo ?>" 
                            data-bg="<?= explode(':', explode(';', $estiloFondo)[0])[1] ?>" 
                            data-color="<?= explode(':', explode(';', $estiloFondo)[1])[1] ?>"
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
        // 1. Obtener la referencia correcta al elemento
        const selectCiclo = document.getElementById('edit_ciclo');
        
        // 2. Rellenar los campos de texto
        document.getElementById('edit_id').value = datos.id_tutor;
        document.getElementById('edit_nombre').value = datos.nombre;
        document.getElementById('edit_apellidos').value = datos.apellidos;
        document.getElementById('edit_telefono').value = datos.telefono;
        document.getElementById('edit_email').value = datos.email;
        
        // 3. Asignar el valor del ciclo
        selectCiclo.value = datos.id_ciclo;

        // 4. Aplicar el estilo visual inmediatamente al abrir
        actualizarEstiloSelect(selectCiclo);
        
        // 5. Mostrar el modal
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