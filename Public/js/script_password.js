/**
 * Public/js/script_password.js — Toggle de visibilidad de contraseña
 *
 * Alterna el campo de contraseña del login entre type="password" y type="text",
 * y cambia el icono del ojo (abierto / cerrado) para dar feedback visual.
 *
 * Llamado desde el botón de ojo en Vista/Login.php mediante onclick="togglePassword()".
 * Requiere que el input tenga id="passInput" y los iconos id="eyeOpen" / id="eyeClosed".
 */
function togglePassword() {
    const input     = document.getElementById('passInput');
    const eyeOpen   = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}