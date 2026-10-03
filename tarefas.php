<?php
require_once 'config.php';
verificarLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Nome ao salvar na Tela de Início -->
    <meta name="apple-mobile-web-app-title" content="P. Estoica">
    <!-- Ícone da Tela de Início (iOS) -->
    <link rel="apple-touch-icon" sizes="180x180" href="icon/favicon.png">
    <title>Tarefas — Performance Estoica</title>
    <link rel="icon" type="image/svg+xml" href="icon/favicon.svg">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/tarefas.css">
    <link rel="stylesheet" href="css/confirm-modal.css">
    <script>
        (function() {
            var theme = localStorage.getItem('app_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.add(theme === 'light' ? 'light-theme' : 'dark-theme');
        })();
    </script>
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
                    <div class="sidebar-logo-text">Performance Estoica</div>
                </a>
            </div>
            <nav class="sidebar-nav">
                <span class="sidebar-section-label">Menu</span>
                <a href="dashboard.php" class="nav-item">
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
                <a href="tarefas.php" class="nav-item ativo">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg></span>
                    <span>Tarefas</span>
                </a>
                <a href="treinos.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg></span>
                    <span>Treinos</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <button id="btnThemeToggle" class="btn btn-outline btn-full btn-theme-toggle" type="button" aria-label="Alternar modo claro e escuro" title="Alternar tema">
                    <span class="theme-icon theme-icon-sun">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </span>
                    <span class="theme-icon theme-icon-moon" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </span>
                    <span class="theme-toggle-label">Modo Claro</span>
                </button>
                <button id="btnLogout" class="btn btn-outline btn-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Sair</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-title-wrapper">
                    <button id="btnMobileMenu" class="mobile-menu-btn" aria-label="Abrir menu">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    </button>
                    <h1>Minhas Tarefas</h1>
                </div>
                <button id="btnNovaTarefaModal" class="btn btn-primary btn-small">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Nova Tarefa
                </button>
            </header>

            <!-- Filtros -->
            <div class="filtros-section">
                <button class="filtro-btn ativo" data-filtro="todas">Todas</button>
                <button class="filtro-btn" data-filtro="hoje">Hoje</button>
                <button class="filtro-btn" data-filtro="agendadas">Agendadas</button>
                <button class="filtro-btn" data-filtro="alta">Alta Prioridade</button>
                <button class="filtro-btn" data-filtro="concluidas">Concluídas</button>
            </div>

            <!-- Lista de Tarefas -->
            <div id="tarefasContainer" class="tarefas-container">
                <p class="text-empty">Carregando tarefas...</p>
            </div>
        </main>
    </div>

    <!-- Modal Nova Tarefa -->
    <div id="modalNovaTarefaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Nova Tarefa</h2>
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formNovaTarefaForm" class="form-modal">
                <div class="form-group">
                    <label for="titulo">Título <span class="required">*</span></label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ex: Ir à academia">
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva a tarefa..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prioridade">Prioridade</label>
                        <select id="prioridade" name="prioridade">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dataAtual">Data e hora</label>
                        <input type="datetime-local" id="dataAtual" name="dataAtual">
                    </div>
                </div>

                <div class="form-group">
                    <label for="recorrencia">Recorrência</label>
                    <select id="recorrencia" name="recorrencia">
                        <option value="nenhuma" selected>Nenhuma</option>
                        <option value="diaria">Diária</option>
                        <option value="semanal">Semanal</option>
                        <option value="dias_semana">Dias de Semana (Seg-Sex)</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>

                <div class="form-group" id="group_data_limite" style="display:none;">
                    <label for="dataVencimento">Repetir até</label>
                    <input type="datetime-local" id="dataVencimento" name="dataVencimento">
                </div>

                <div id="mensagemErroModal" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Criar Tarefa</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Tarefa -->
    <div id="modalEditarTarefaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Editar Tarefa</h2>
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formEditarTarefaForm" class="form-modal">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_titulo">Título <span class="required">*</span></label>
                    <input type="text" id="edit_titulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label for="edit_descricao">Descrição</label>
                    <textarea id="edit_descricao" name="descricao"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_prioridade">Prioridade</label>
                        <select id="edit_prioridade" name="prioridade">
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_dataAtual">Data e hora</label>
                        <input type="datetime-local" id="edit_dataAtual" name="dataAtual">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_recorrencia">Recorrência</label>
                    <select id="edit_recorrencia" name="recorrencia">
                        <option value="nenhuma">Nenhuma</option>
                        <option value="diaria">Diária</option>
                        <option value="semanal">Semanal</option>
                        <option value="dias_semana">Dias de Semana (Seg-Sex)</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>

                <div class="form-group" id="edit_group_data_limite" style="display:none;">
                    <label for="edit_dataVencimento">Repetir até</label>
                    <input type="datetime-local" id="edit_dataVencimento" name="dataVencimento">
                </div>

                <div id="mensagemErroModalEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="js/theme.js"></script>
    <script src="js/confirm-modal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/tarefas.js"></script>
</body>
</html>