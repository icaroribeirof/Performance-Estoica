<?php
require_once 'config.php';
verificarLogin();

$usuario_nome = $_SESSION['usuario_nome'];
$usuario_id = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Performance Estoica</title>
    <link rel="icon" type="image/svg+xml" href="icon/favicon.svg">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/confirm-modal.css">
</head>
<body>
    <div class="container-app">
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <div class="sidebar-logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </div>
                    <div class="sidebar-logo-text">
                        Performance Estoica
                    </div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <span class="sidebar-section-label">Menu</span>
                <a href="dashboard.php" class="nav-item ativo">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg></span>
                    <span>Dashboard</span>
                </a>
                <a href="calendario.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                    <span>Calendário</span>
                </a>
                <a href="metas.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></span>
                    <span>Metas</span>
                </a>
                <a href="tarefas.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg></span>
                    <span>Tarefas</span>
                </a>
                <a href="treinos.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg></span>
                    <span>Treinos</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <button id="btnLogout" class="btn btn-outline btn-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Sair</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="header-title-wrapper">
                    <button id="btnMobileMenu" class="mobile-menu-btn" aria-label="Abrir menu">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    </button>
                    <div>
                        <h1 id="saudacao">Olá, <?php echo htmlspecialchars($usuario_nome); ?></h1>
                        <p class="header-subtitle" id="headerSubtitle">Confira seu progresso de hoje.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button id="btnConfiguracoes" class="btn btn-secondary btn-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        Configurações
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
                <!-- Estatísticas -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon icon-goals">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Metas Ativas</h3>
                                <p class="stat-value" id="metasAtivasCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon icon-tasks">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Tarefas Hoje</h3>
                                <p class="stat-value" id="tarefasHojeCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon icon-workouts">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Treinos Este Mês</h3>
                                <p class="stat-value" id="treinosCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon icon-time">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div class="stat-info">
                                <h3>Tempo de Treino</h3>
                                <p class="stat-value" id="tempoTreino">0h</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Metas Próximas -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>Próximas Metas</h2>
                        <a href="metas.php" class="link-mais">Ver todas
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                    <div id="metasProximas" class="items-list">
                        <p class="text-empty">Nenhuma meta registrada</p>
                    </div>
                </section>

                <!-- Tarefas de Hoje -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>Tarefas de Hoje</h2>
                        <a href="tarefas.php" class="link-mais">Ver todas
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </a>
                    </div>
                    <div id="tarefasHoje" class="items-list">
                        <p class="text-empty">Nenhuma tarefa para hoje</p>
                    </div>
                </section>

                <!-- Gráfico de Progresso -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>Progresso de Treinos</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartTreinos"></canvas>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal Configurações -->
    <div id="modalConfiguracoesOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Configurações</h2>
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formConfiguracoes" class="form-modal">
                <div class="form-group">
                    <label for="configNome">Nome <span class="required">*</span></label>
                    <input type="text" id="configNome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" required>
                </div>

                <div class="form-group">
                    <label for="configSenhaNova">Nova senha</label>
                    <input type="password" id="configSenhaNova" name="senha_nova" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
                    <small>Mínimo 6 caracteres.</small>
                </div>

                <div id="mensagemErroConfig" class="mensagem-erro"></div>
                <div id="mensagemSucessoConfig" class="mensagem-sucesso"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/confirm-modal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>
