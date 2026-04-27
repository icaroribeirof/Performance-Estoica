<?php
require_once 'config.php';
verificarLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefas - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/tarefas.css">
    <link rel="stylesheet" href="css/confirm-modal.css">
</head>
<body>
    <div class="container-app">
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
                <a href="metas.php" class="nav-item">
                    <span class="icon">🎯</span>
                    <span>Metas</span>
                </a>
                <a href="tarefas.php" class="nav-item ativo">
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
                <h1>✓ Minhas Tarefas</h1>
                <button id="btnNovaTarefaModal" class="btn btn-primary btn-small">
                    + Nova Tarefa
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
                <button class="modal-close">&times;</button>
            </div>
            <form id="formNovaTarefaForm" class="form-modal">
                <div class="form-group">
                    <label for="titulo">Título *</label>
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
                        <label for="dataAtual">Data da tarefa</label>
                        <input type="date" id="dataAtual" name="dataAtual">
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

                <!-- Só aparece quando há recorrência -->
                <div class="form-group" id="group_data_limite" style="display:none;">
                    <label for="dataVencimento">Repetir até (data limite)</label>
                    <input type="date" id="dataVencimento" name="dataVencimento">
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
                <button class="modal-close">&times;</button>
            </div>
            <form id="formEditarTarefaForm" class="form-modal">
                <input type="hidden" id="edit_id" name="id">
                <div class="form-group">
                    <label for="edit_titulo">Título *</label>
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
                        <label for="edit_dataAtual">Data da tarefa</label>
                        <input type="date" id="edit_dataAtual" name="dataAtual">
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

                <!-- Só aparece quando há recorrência -->
                <div class="form-group" id="edit_group_data_limite" style="display:none;">
                    <label for="edit_dataVencimento">Repetir até (data limite)</label>
                    <input type="date" id="edit_dataVencimento" name="dataVencimento">
                </div>

                <div id="mensagemErroModalEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="js/confirm-modal.js"></script>
    <script src="js/tarefas.js"></script>
</body>
</html>