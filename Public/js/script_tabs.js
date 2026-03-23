document.addEventListener('DOMContentLoaded', () => {
    const stepLabels = document.querySelectorAll('.step-label');
    const tabs = document.querySelectorAll('[data-tab]');

    // --- LÓGICA DE TUS PESTAÑAS (MANTENIDA) ---
    function updateTabs(stepId) {
        tabs.forEach(tab => {
            if (tab.getAttribute('data-tab') === stepId) {
                tab.style.display = 'block';
                tab.classList.add('active');
            } else {
                tab.style.display = 'none';
                tab.classList.remove('active');
            }
        });

        stepLabels.forEach(label => {
            const currentStep = label.getAttribute('data-step');
            const circle = label.querySelector('.step-circle');
            const text = label.querySelector('.step-heading');
            
            if (currentStep === stepId) {
                circle.classList.add('step-active-circle');
                text.classList.add('step-active-text');
            } else {
                circle.classList.remove('step-active-circle');
                text.classList.remove('step-active-text');
            }
        });
    }

    stepLabels.forEach(label => {
        label.addEventListener('click', () => {
            const stepId = label.getAttribute('data-step');
            updateTabs(stepId);
        });
    });

    updateTabs("1");

    // --- NUEVA LÓGICA PARA EL MENÚ DESPLEGABLE ---
    const userBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Evita que el clic cierre el menú inmediatamente
            userDropdown.classList.toggle('hidden');
        });

        // Cerrar menú si haces clic fuera de él
        document.addEventListener('click', (e) => {
            if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
});