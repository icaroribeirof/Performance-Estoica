// Menu JavaScript — Mobile sidebar toggle

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const btnMenu = document.getElementById('btnMobileMenu');

    if (!sidebar || !overlay || !btnMenu) return;

    function abrirMenu() {
        sidebar.classList.add('ativo');
        overlay.classList.add('ativo');
        document.body.style.overflow = 'hidden';
    }

    function fecharMenu() {
        sidebar.classList.remove('ativo');
        overlay.classList.remove('ativo');
        document.body.style.overflow = '';
    }

    btnMenu.addEventListener('click', abrirMenu);
    overlay.addEventListener('click', fecharMenu);

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('ativo')) {
            fecharMenu();
        }
    });

    // Close on window resize if desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024 && sidebar.classList.contains('ativo')) {
            fecharMenu();
        }
    });
});
