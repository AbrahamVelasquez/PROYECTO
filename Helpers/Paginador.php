<?php

/**
 * Helpers/Paginador.php — Lógica de paginación reutilizable
 *
 * Centraliza todo lo relacionado con paginar resultados en el sistema.
 * Se usa tanto en vistas PHP (paginación server-side sobre arrays)
 * como en la generación del HTML de navegación entre páginas.
 *
 * Funciones disponibles:
 *   - generarPaginacion()      → calcula offset/límite para consultas SQL
 *   - leerPaginaActual()       → lee el número de página del GET de forma segura
 *   - leerPorPagina()          → lee cuántos registros por página quiere el usuario
 *   - paginarArray()           → recorta un array PHP al subconjunto de la página
 *   - renderizarNavPaginacion() → genera el HTML de la barra de navegación
 *
 * El sistema de paginación funciona exclusivamente via GET params para que
 * los enlaces sean compartibles y el botón Atrás del navegador funcione bien.
 *
 * MVC: Helper de presentación. No accede a BD — opera sobre datos ya cargados.
 */

// ── Cálculo de metadatos (LIMIT/OFFSET) para consultas SQL ───────────────────
function generarPaginacion($totalRegistros, $paginaActual, $registrosPorPagina = 10) {
    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);
    if ($totalPaginas <= 0) $totalPaginas = 1;

    if ($paginaActual < 1) $paginaActual = 1;
    if ($paginaActual > $totalPaginas) $paginaActual = $totalPaginas;

    $offset = ($paginaActual - 1) * $registrosPorPagina;

    return [
        'paginaActual'    => $paginaActual,
        'totalPaginas'    => $totalPaginas,
        'offset'          => $offset,
        'limite'          => $registrosPorPagina,
        'tienePrevia'     => $paginaActual > 1,
        'tieneSiguiente'  => $paginaActual < $totalPaginas
    ];
}

// ── Leer el número de página actual desde GET ─────────────────────────────────
function leerPaginaActual(string $param = 'pag'): int {
    return max(1, (int)($_GET[$param] ?? 1));
}

// ── Leer cuántos registros por página desde GET (0 = mostrar todos) ───────────
function leerPorPagina(string $param = 'pp', int $default = 10): int {
    if (!isset($_GET[$param])) return $default;
    $v = (int)$_GET[$param];
    return $v <= 0 ? 0 : $v;
}

// ── Slicear un array al subconjunto de la página actual ───────────────────────
function paginarArray(array $items, int $porPagina, int $pagina): array {
    if ($porPagina <= 0) return $items;
    $offset = max(0, ($pagina - 1) * $porPagina);
    return array_slice($items, $offset, $porPagina);
}

// ── Renderizar la barra de navegación de páginas (HTML) ──────────────────────
// $extraParams: parámetros GET adicionales que siempre deben incluirse en los enlaces
// (útil para vistas admin donde 'accion' no está en $_GET)
function renderizarNavPaginacion(
    int $total,
    int $pagina,
    int $porPagina,
    string $paramPag,
    string $color = 'orange',
    array $extraParams = []
): string {
    if ($total === 0 || $porPagina <= 0 || $total <= $porPagina) return '';

    $totalPags = (int)ceil($total / $porPagina);
    $pagina    = max(1, min($pagina, $totalPags));
    $inicio    = ($pagina - 1) * $porPagina + 1;
    $fin       = min($pagina * $porPagina, $total);
    $c         = $color;

    // URL base: solo preservamos los params pp_* del GET (configuración por página elegida por el usuario)
    // El resto (tab, accion, etc.) viene exclusivamente de $extraParams para evitar contaminación entre pasos
    $preservedGET = array_filter($_GET, fn($key) => str_starts_with($key, 'pp_'), ARRAY_FILTER_USE_KEY);
    $params = array_merge($preservedGET, $extraParams);
    unset($params[$paramPag]);
    $qs   = http_build_query($params);
    $base = 'index.php' . ($qs ? '?' . $qs . '&' : '?') . $paramPag . '=';

    $svg_prev = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>';
    $svg_next = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';

    $html  = '<div class="flex flex-col items-center gap-2 mt-3">';
    $html .= '<p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Mostrando ' . $inicio . '–' . $fin . ' de ' . $total . '</p>';
    $html .= '<div class="flex items-center justify-center gap-1.5">';

    // Botón Anterior
    $baseClass = 'flex items-center gap-1.5 px-4 py-2 rounded-xl border text-[10px] font-black uppercase tracking-widest transition-all ';
    if ($pagina > 1) {
        $html .= '<a href="' . htmlspecialchars($base . ($pagina - 1)) . '" class="' . $baseClass . 'border-slate-200 text-slate-500 hover:border-' . $c . '-300 hover:text-' . $c . '-600 hover:bg-' . $c . '-50 cursor-pointer">' . $svg_prev . 'Anterior</a>';
    } else {
        $html .= '<span class="' . $baseClass . 'border-slate-100 text-slate-300 cursor-not-allowed">' . $svg_prev . 'Anterior</span>';
    }

    // Números de página
    $pags = array_unique(array_filter([
        1, $pagina - 1, $pagina, $pagina + 1, $totalPags
    ], fn($p) => $p >= 1 && $p <= $totalPags));
    sort($pags);

    $prev = null;
    foreach ($pags as $p) {
        if ($prev !== null && $p - $prev > 1) {
            $html .= '<span class="text-slate-300 text-xs font-bold px-1">···</span>';
        }
        if ($p === $pagina) {
            $html .= '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-' . $c . '-600 text-white text-[10px] font-black">' . $p . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($base . $p) . '" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 text-[10px] font-black hover:border-' . $c . '-300 hover:text-' . $c . '-600 hover:bg-' . $c . '-50 transition-all">' . $p . '</a>';
        }
        $prev = $p;
    }

    // Botón Siguiente
    if ($pagina < $totalPags) {
        $html .= '<a href="' . htmlspecialchars($base . ($pagina + 1)) . '" class="' . $baseClass . 'border-slate-200 text-slate-500 hover:border-' . $c . '-300 hover:text-' . $c . '-600 hover:bg-' . $c . '-50 cursor-pointer">Siguiente' . $svg_next . '</a>';
    } else {
        $html .= '<span class="' . $baseClass . 'border-slate-100 text-slate-300 cursor-not-allowed">Siguiente' . $svg_next . '</span>';
    }

    $html .= '</div></div>';
    return $html;
}
