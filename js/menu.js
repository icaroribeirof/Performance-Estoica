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

    // Auto-Heartbeat de Notificações WhatsApp (executa no navegador para contornar o bloqueio de bot do InfinityFree)
    (function initNotificationHeartbeat() {
        var cronUrl = window.location.origin + '/cron/notify_whatsapp.php';
        function dispararCron() {
            fetch(cronUrl, { method: 'GET', cache: 'no-store' })
                .catch(function() { /* Silencioso */ });
        }
        // Dispara 3 segundos após carregar a página
        setTimeout(dispararCron, 3000);
        // E repete a cada 60 segundos com o site aberto
        setInterval(dispararCron, 60000);
    })();
});

