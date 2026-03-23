<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin — Tutores</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        /* Reutilizamos el estilo de tabla técnica que ya te gustó */
        .table-tech th {
            background-color: #1e293b; /* Slate 800 */
            color: #fff;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            padding: 12px 15px;
            text-align: left;
        }
        .table-tech td {
            font-size: 13px;
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-tech tr:hover { background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-svh bg-slate-50 text-slate-900 antialiased font-sans p-6 md:p-10">

    <div class="mx-auto max-w-7xl">
        
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                    Panel de <span class="text-orange-600">Tutores</span>
                </h1>
                <p class="text-slate-500 text-sm mt-1 uppercase font-bold tracking-widest">Administración Ciudad Escolar</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="#" class="inline-flex items-center gap-2 bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-orange-700 transition-all shadow-md shadow-orange-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar Tutor
                </a>

                <form action='index.php' method='POST'>
                    <button type="submit" name='btnLogOut' onclick="return confirm('¿Está seguro que quiere cerrar sesión?')" 
                        class="bg-white border border-slate-200 text-slate-400 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all cursor-pointer">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </header>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left table-tech border-collapse">
                    <thead>
                        <tr>
                            <th class="w-20">ID</th>
                            <th>DNI</th>
                            <th>Nombre Completo</th>
                            <th>Email Institucional</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($tutores)): ?>
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-4xl mb-4">📂</span>
                                        <p class="text-slate-400 font-medium italic">No hay tutores registrados en la base de datos.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tutores as $fila): ?>
                            <tr class="transition-colors">
                                <td class="font-mono text-xs font-bold text-slate-400">#<?php echo $fila['id_tutor']; ?></td>
                                <td class="font-semibold text-slate-700 uppercase"><?php echo $fila['dni']; ?></td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900"><?php echo $fila['nombre']; ?></span>
                                        <span class="text-xs text-slate-500 uppercase"><?php echo $fila['apellidos']; ?></span>
                                    </div>
                                </td>
                                <td class="text-slate-600 italic"><?php echo $fila['email']; ?></td>
                                <td class="font-medium text-slate-600"><?php echo $fila['telefono']; ?></td>
                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="#" title="Editar Tutor" class="p-2 text-orange-500 hover:bg-orange-50 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <a href="#" onclick="return confirm('¿Eliminar este Tutor?')" title="Eliminar Tutor" class="p-2 text-slate-300 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="mt-10 text-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                © <?php echo date('Y'); ?> — Gestión Interna Ciudad Escolar
            </p>
        </footer>
    </div>

</body>
</html>