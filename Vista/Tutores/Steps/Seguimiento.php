<?php

// Vista/Tutores/Steps/Seguimiento.php

// Calcula la ruta desde la raíz del servidor hasta tu carpeta de proyecto
require_once $_SERVER['DOCUMENT_ROOT'] . '/PROYECTO/Seguridad/Control_Accesos.php';

validarAcceso('tutor'); 

/////////////////////////////////////////////////
//// Este fichero, por ahora, solo es visual ////
/////////////////////////////////////////////////

// Corresponderá al paso 4 del proceso

?>
<div class="text-center text-slate-400 italic py-20 uppercase font-black text-[10px] tracking-widest">
    Módulo de Seguimiento en desarrollo.
</div>
