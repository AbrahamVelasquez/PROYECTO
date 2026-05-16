// Motor de paginación compartido — todas las tablas registran sus callbacks aquí
window._pagCallbacks = window._pagCallbacks || {};

// Devuelve 0 si el usuario eligió "Todos", o el número guardado (por defecto 10)
function _leerPagStorage(prefix) {
    const v = localStorage.getItem('pag_' + prefix + '_porPagina');
    return v === 'all' ? 0 : (parseInt(v) || 10);
}

function abrirModalPag(prefix) {
    const v = localStorage.getItem('pag_' + prefix + '_porPagina');
    const input = document.getElementById('input-pag-' + prefix);
    if (input) input.value = (v && v !== 'all') ? parseInt(v) : '';
    document.getElementById('modal-pag-' + prefix).style.display = 'flex';
}

function cerrarModalPag(prefix) {
    document.getElementById('modal-pag-' + prefix).style.display = 'none';
}

function setPagPreset(prefix, n) {
    document.getElementById('input-pag-' + prefix).value = n;
}

// Muestra todos los registros sin paginar
function setPagTodos(prefix) {
    localStorage.setItem('pag_' + prefix + '_porPagina', 'all');
    const label = document.getElementById(prefix + '-pag-label');
    if (label) label.textContent = 'Todos';
    cerrarModalPag(prefix);
    if (window._pagCallbacks[prefix]) window._pagCallbacks[prefix](0);
}

function aplicarPag(prefix) {
    const val = parseInt(document.getElementById('input-pag-' + prefix).value);
    if (!val || val < 1) return;
    localStorage.setItem('pag_' + prefix + '_porPagina', val);
    const label = document.getElementById(prefix + '-pag-label');
    if (label) label.textContent = val + '/pág';
    cerrarModalPag(prefix);
    if (window._pagCallbacks[prefix]) window._pagCallbacks[prefix](val);
}
