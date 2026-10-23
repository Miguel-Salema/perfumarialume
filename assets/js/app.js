document.addEventListener('DOMContentLoaded', () => {
    // Menu compacto em telas pequenas.
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.nav-links');

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    // Alterna a visibilidade dos campos de senha.
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.togglePassword);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.textContent = show ? 'Ocultar' : 'Mostrar';
        });
    });

    // Confirma ações que ocultam um produto.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });
});
