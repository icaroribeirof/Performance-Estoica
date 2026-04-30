<?php
require_once 'config.php';
verificarLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metas - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/metas.css">
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
                <a href="dashboard.php" class="nav-item">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="metas.php" class="nav-item ativo">
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
                    <h1>🎯 Minhas Metas</h1>
                </div>
                <button id="btnNovaMetaModal" class="btn btn-primary btn-small">
                    + Nova Meta
                </button>
            </header>

            <!-- Filtros -->
            <div class="filtros-section">
                <button class="filtro-btn ativo" data-filtro="todas">Todas</button>
                <button class="filtro-btn" data-filtro="em_progresso">Em Progresso</button>
                <button class="filtro-btn" data-filtro="concluida">Concluídas</button>
            </div>

            <!-- Lista de Metas -->
            <div id="metasContainer" class="metas-container">
                <p class="text-empty">Carregando metas...</p>
            </div>
        </main>
    </div>

    <!-- Modal Nova Meta -->
    <div id="modalNovaMetaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Nova Meta</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formNovaMetaForm" class="form-modal">
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required placeholder="Ex: Emagrecer 10kg">
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva sua meta..."></textarea>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria">
                        <option value="">Selecione uma categoria</option>
                        <option value="saude">Saúde</option>
                        <option value="fitness">Fitness</option>
                        <option value="financeira">Financeira</option>
                        <option value="educacao">Educação</option>
                        <option value="pessoal">Pessoal</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dataInicio">Data Início *</label>
                        <input type="date" id="dataInicio" name="dataInicio" required>
                    </div>
                    <div class="form-group">
                        <label for="dataTermino">Data Término *</label>
                        <input type="date" id="dataTermino" name="dataTermino" required>
                    </div>
                </div>

                <div id="mensagemErroModal" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Criar Meta</button>
            </form>
        </div>
    </div>
    <!-- Modal Editar Meta -->
    <div id="modalEditarMetaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Editar Meta</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formEditarMetaForm" class="form-modal">
                <input type="hidden" id="edit_meta_id" name="id">
                <div class="form-group">
                    <label for="edit_meta_titulo">Título *</label>
                    <input type="text" id="edit_meta_titulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label for="edit_meta_descricao">Descrição</label>
                    <textarea id="edit_meta_descricao" name="descricao"></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_meta_categoria">Categoria</label>
                    <select id="edit_meta_categoria" name="categoria">
                        <option value="">Selecione uma categoria</option>
                        <option value="saude">Saúde</option>
                        <option value="fitness">Fitness</option>
                        <option value="financeira">Financeira</option>
                        <option value="educacao">Educação</option>
                        <option value="pessoal">Pessoal</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_meta_dataInicio">Data Início *</label>
                        <input type="date" id="edit_meta_dataInicio" name="dataInicio" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_meta_dataTermino">Data Término *</label>
                        <input type="date" id="edit_meta_dataTermino" name="dataTermino" required>
                    </div>
                </div>

                <div id="mensagemErroModalEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="js/confirm-modal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/metas.js"></script>
</body>
</html>
