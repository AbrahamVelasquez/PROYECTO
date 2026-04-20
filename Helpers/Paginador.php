<?php

// Controlador/Helpers.php 

// Usarla para tener la paginación de ciertas tablas

function generarPaginacion($totalRegistros, $paginaActual, $registrosPorPagina = 10) {
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
    if ($totalPaginas <= 0) $totalPaginas = 1;

    // Ajustes de rango
    if ($paginaActual < 1) $paginaActual = 1;
    if ($paginaActual > $totalPaginas) $paginaActual = $totalPaginas;

    $offset = ($paginaActual - 1) * $registrosPorPagina;

    return [
        'paginaActual' => $paginaActual,
        'totalPaginas' => $totalPaginas,
        'offset'       => $offset,
        'limite'       => $registrosPorPagina,
        'tienePrevia'  => $paginaActual > 1,
        'tieneSiguiente' => $paginaActual < $totalPaginas
    ];
}