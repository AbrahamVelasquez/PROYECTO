<?php 

// Vista/Admin/Components/Dashboard_Header.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('admin'); 

?>
<div class="pt-6 px-8 pb-6 border-b border-slate-100"> 
    <div class="flex justify-between items-start">
        <div class="flex-1 min-w-0"> 
            <h1 class="text-4xl font-extrabold tracking-tight">
                Panel de <span class="text-orange-600">Administración</span>
            </h1>
            <p class="mt-1 text-slate-500 text-sm max-w-2xl truncate">
                Interfaz de control central de Ciudad Escolar. Gestión de personal docente y convenios.
            </p>
        </div>

        <div class="relative ml-4 flex-shrink-0">
            <button id="userMenuBtn" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2 pr-4 hover:bg-slate-50 transition-all shadow-sm cursor-pointer">
                <div class="h-8 w-8 rounded-lg bg-orange-600 flex items-center justify-center text-white font-bold text-xs shadow-inner">A</div>
                <span class="text-xs font-bold text-slate-700">Administrador</span>
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nivel de Acceso</p>
                    <p class="text-[11px] font-bold text-slate-800 uppercase italic">Administración de CE</p>
                </div>
                <div class="p-1">
                    <form action="index.php" method="POST">
                        <button type="submit" name="btnLogOut" class="w-full text-left px-4 py-3 text-[10px] font-black text-red-500 hover:bg-red-50 rounded-lg transition-colors uppercase flex items-center justify-between">
                            <span>Cerrar Sesión</span> <span>✕</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>