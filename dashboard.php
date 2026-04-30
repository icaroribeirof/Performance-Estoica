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
    <title>Dashboard - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
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
                <h2>💪 Performance<br>Estoica</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item ativo">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="metas.php" class="nav-item">
                    <span class="icon">🎯</span>
                    <span>Metas</span>
                </a>
                <a href="tarefas.php" class="nav-item">
                    <span class="icon">✓</span>
                    <span>Tarefas</span>
                </a>
                <a href="treinos.php" class="nav-item">
                    <span class="icon">🏋️</span>
                    <span>Treinos</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <button id="btnLogout" class="btn btn-outline btn-full">
                    <span>🚪</span>
                    <span>Sair</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-header">
                <div class="header-title-wrapper">
                    <button id="btnMobileMenu" class="mobile-menu-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                    <h1>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>! 👋</h1>
                </div>
                <div class="header-actions">
                    <button id="btnConfiguracoes" class="btn btn-primary btn-small">
                        ⚙️ Configurações
                    </button>
                </div>
            </header>

            <!-- Content -->
            <div class="dashboard-content">
                <!-- Estatísticas -->
                <section class="stats-section">
                    <h2>Suas Estatísticas</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">🎯</div>
                            <div class="stat-info">
                                <h3>Metas Ativas</h3>
                                <p class="stat-value" id="metasAtivasCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">✓</div>
                            <div class="stat-info">
                                <h3>Tarefas Hoje</h3>
                                <p class="stat-value" id="tarefasHojeCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">🏋️</div>
                            <div class="stat-info">
                                <h3>Treinos Este Mês</h3>
                                <p class="stat-value" id="treinosCount">0</p>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">⏱️</div>
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
                        <a href="metas.php" class="link-mais">Ver todas →</a>
                    </div>
                    <div id="metasProximas" class="items-list">
                        <p class="text-empty">Nenhuma meta registrada</p>
                    </div>
                </section>

                <!-- Tarefas de Hoje -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>Tarefas de Hoje</h2>
                        <a href="tarefas.php" class="link-mais">Ver todas →</a>
                    </div>
                    <div id="tarefasHoje" class="items-list">
                        <p class="text-empty">Nenhuma tarefa para hoje</p>
                    </div>
                </section>

                <!-- Gráfico de Progresso -->
                <section class="dashboard-section">
                    <h2>Progresso de Treinos</h2>
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
                <h2>Configurações do Perfil</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formConfiguracoes" class="form-modal" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="form-group">
                    <label for="configNome">Nome *</label>
                    <input type="text" id="configNome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" required>
                </div>

                <div class="form-group">
                    <label for="configSenhaNova">Nova Senha</label>
                    <input type="password" id="configSenhaNova" name="senha_nova" placeholder="Deixe em branco para não alterar">
                    <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem;">Mínimo 6 caracteres.</small>
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
