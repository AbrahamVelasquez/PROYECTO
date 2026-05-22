/**
 * Public/js/validacion.js
 *
 * Validación de formularios con mensajes contextuales.
 * Sustituye el required nativo por un bloque al pie del formulario
 * que lista los campos obligatorios que faltan por rellenar.
 *
 * Uso:
 *   <form novalidate onsubmit="return validarForm(this)"> ... </form>
 *
 * Los campos obligatorios siguen marcados con `required`.
 * El texto del label correspondiente se usa como nombre del campo.
 */

/**
 * Valida todos los campos [required] de un formulario.
 * Muestra un bloque de errores al pie y marca los campos vacíos en rojo.
 * @param {HTMLFormElement|string} form — elemento form o su id
 * @returns {boolean} true si todo está relleno, false si hay errores
 */
function validarForm(form) {
    if (typeof form === 'string') form = document.getElementById(form);
    if (!form) return true;

    // ── 1. Limpiar estado previo ──────────────────────────────────────
    form.querySelectorAll('.campo-con-error').forEach(el => {
        el.classList.remove('campo-con-error');
        el.style.borderColor = '';
    });
    form.querySelector('.bloque-errores-validacion')?.remove();

    // ── 2. Comprobar campos requeridos ────────────────────────────────
    const faltantes = [];

    form.querySelectorAll('[required]').forEach(campo => {
        const vacio = campo.tagName === 'SELECT'
            ? !campo.value || campo.value === ''
            : campo.value.trim() === '';

        if (!vacio) return;

        // Obtener nombre del label más cercano
        const nombreLabel = _obtenerLabel(campo, form);
        faltantes.push(nombreLabel);

        // Marcar campo en rojo
        campo.classList.add('campo-con-error');
        campo.style.borderColor = '#f87171'; // red-400

        // Auto-limpiar cuando el usuario empiece a escribir
        const limpiar = () => {
            campo.classList.remove('campo-con-error');
            campo.style.borderColor = '';
            // Si ya no hay errores, quitar el bloque
            if (!form.querySelectorAll('.campo-con-error').length) {
                form.querySelector('.bloque-errores-validacion')?.remove();
            }
        };
        campo.addEventListener('input', limpiar, { once: true });
        campo.addEventListener('change', limpiar, { once: true });
    });

    if (faltantes.length === 0) return true;

    // ── 3. Construir y mostrar el bloque de errores ───────────────────
    const errDiv = document.createElement('div');
    errDiv.className = 'bloque-errores-validacion';
    errDiv.style.cssText = `
        margin-top: 1rem;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    `;

    const titulo = document.createElement('p');
    titulo.style.cssText = 'font-size:10px; font-weight:900; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.4rem; display:flex; align-items:center; gap:6px;';
    titulo.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Datos obligatorios a cumplimentar:
    `;
    errDiv.appendChild(titulo);

    const lista = document.createElement('ul');
    lista.style.cssText = 'list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:3px;';
    faltantes.forEach(nombre => {
        const li = document.createElement('li');
        li.style.cssText = 'font-size:11px; font-weight:700; display:flex; align-items:center; gap:6px;';
        li.innerHTML = `<span style="color:#f87171; font-weight:900;">•</span> ${nombre}`;
        lista.appendChild(li);
    });
    errDiv.appendChild(lista);

    // Insertar justo antes del área de botones de acción
    const ancla = form.querySelector('.flex.justify-end')
               || form.querySelector('.flex.items-center.justify-end')
               || form.querySelector('.flex.gap-3:last-of-type')
               || form.querySelector('button[type="submit"]')?.closest('div')
               || form.lastElementChild;

    if (ancla && ancla !== errDiv) {
        form.insertBefore(errDiv, ancla);
    } else {
        form.appendChild(errDiv);
    }

    errDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    return false;
}

/**
 * Obtiene el texto del label asociado a un campo.
 * Busca en este orden:
 *   1. Label con [for=campo.id]
 *   2. Label hermano en el mismo contenedor div
 *   3. Humaniza el atributo name como fallback
 */
function _obtenerLabel(campo, form) {
    // Intento 1: label[for]
    if (campo.id) {
        const label = form.querySelector(`label[for="${campo.id}"]`);
        if (label) return _limpiarTextoLabel(label);
    }

    // Intento 2: label hermano en el div más cercano
    const contenedor = campo.closest('div');
    if (contenedor) {
        const label = contenedor.querySelector('label');
        if (label) return _limpiarTextoLabel(label);
    }

    // Fallback: humanizar el name
    return campo.name
        .replace(/_/g, ' ')
        .replace(/([A-Z])/g, ' $1')
        .trim()
        .replace(/^\w/, c => c.toUpperCase());
}

/** Elimina asteriscos y espacios extra del texto de un label */
function _limpiarTextoLabel(label) {
    return label.textContent
        .replace(/\*/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}
