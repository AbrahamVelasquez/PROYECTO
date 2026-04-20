<?php 

// Vista/Admin/Sections/Tabla_Tutores.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
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

<?php include 'Vista/Admin/Components/Modales_Tutores.php'; ?>