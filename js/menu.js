document.addEventListener('DOMContentLoaded', () => {
    const btnMobileMenu = document.getElementById('btnMobileMenu');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (btnMobileMenu && sidebar && overlay) {
        btnMobileMenu.addEventListener('click', () => {
            sidebar.classList.toggle('ativo');
            overlay.classList.toggle('ativo');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('ativo');
            overlay.classList.remove('ativo');
        });
    }
    
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('ativo');
                if (overlay) overlay.classList.remove('ativo');
            }
        });
    });
});
