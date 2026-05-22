/**
 * Public/js/script_tabs.js — Sistema de pestañas / pasos del wizard
 *
 * Gestiona la navegación entre pasos (tutor: 4 pasos, admin: 4 secciones)
 * y el menú desplegable del usuario en la cabecera.
 *
 * Cómo funciona:
 *   - Los elementos con clase `.step-label` actúan como botones de navegación.
 *     Cada uno tiene un atributo `data-step="N"` con el número de paso.
 *   - Los contenedores de contenido tienen `data-tab="N"`. Solo el activo
 *     es visible; el resto se oculta con display:none.
 *   - Al cambiar de paso se actualiza la URL (?tab=N) con replaceState
 *     para que el botón Atrás del navegador funcione correctamente
 *     y la página recargue en el mismo paso.
 *
 * El indicador visual (círculo y texto del paso activo) se actualiza junto
 * con el contenido, añadiendo/quitando las clases step-active-circle y
 * step-active-text definidas en el CSS del dashboard.
 */

document.addEventListener('DOMContentLoaded', () => {
    const stepLabels = document.querySelectorAll('.step-label');
    const tabs       = document.querySelectorAll('[data-tab]');

    /**
     * Cambia el paso activo: oculta todos los tabs, muestra el solicitado
     * y actualiza los indicadores visuales del wizard.
     */
    function updateTabs(stepId) {
        // Sincronizamos la URL para que la recarga y el historial apunten al paso correcto
        history.replaceState(null, '', '?tab=' + stepId);

        tabs.forEach(tab => {
            const esActivo = tab.getAttribute('data-tab') === stepId;
            tab.style.display = esActivo ? 'block' : 'none';
            tab.classList.toggle('active', esActivo);
        });

        stepLabels.forEach(label => {
            const esActivo = label.getAttribute('data-step') === stepId;
            label.querySelector('.step-circle')?.classList.toggle('step-active-circle', esActivo);
            label.querySelector('.step-heading')?.classList.toggle('step-active-text', esActivo);
        });
    }

    // Clic en cualquier paso del wizard
    stepLabels.forEach(label => {
        label.addEventListener('click', () => {
            updateTabs(label.getAttribute('data-step'));
        });
    });

    // Al cargar la página, leer el paso de la URL o mostrar el primero por defecto
    const tabActiva = new URLSearchParams(window.location.search).get('tab') || '1';
    updateTabs(tabActiva);

    // ── Menú desplegable del usuario (cabecera) ────────────────────────────────
    // stopPropagation en el botón evita que el listener del documento lo cierre
    // inmediatamente después de abrirlo
    const userBtn      = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
});