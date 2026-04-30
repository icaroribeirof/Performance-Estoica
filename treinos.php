<?php
require_once 'config.php';
verificarLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treinos - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/treinos.css">
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
                <a href="metas.php" class="nav-item">
                    <span class="icon">🎯</span>
                    <span>Metas</span>
                </a>
                <a href="tarefas.php" class="nav-item">
                    <span class="icon">✓</span>
                    <span>Tarefas</span>
                </a>
                <a href="treinos.php" class="nav-item ativo">
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
                    <h1>🏋️ Meus Treinos</h1>
                </div>
                <div class="header-actions">
                    <button id="btnNovaFichaModal" class="btn btn-primary btn-small">
                        + Nova Ficha
                    </button>
                    <button id="btnRegistroTreinoModal" class="btn btn-secondary btn-small">
                        + Registrar Treino
                    </button>
                </div>
            </header>

            <!-- Abas -->
            <div class="tabs-section">
                <button class="tab-btn ativo" data-tab="fichas">Minhas Fichas</button>
                <button class="tab-btn" data-tab="registros">Histórico de Treinos</button>
            </div>

            <!-- Tab: Fichas -->
            <div id="tab-fichas" class="tab-content ativo">
                <div id="fichasContainer" class="fichas-container">
                    <p class="text-empty">Nenhuma ficha de treino criada</p>
                </div>
            </div>

            <!-- Tab: Registros -->
            <div id="tab-registros" class="tab-content">
                <div class="registros-header">
                    <select id="mesSelecionado" class="form-select">
                        <option value="">Selecione um mês</option>
                    </select>
                    <div id="estatisticas" class="stats-box"></div>
                </div>
                <div id="registrosContainer" class="registros-container">
                    <p class="text-empty">Nenhum treino registrado</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Nova Ficha -->
    <div id="modalNovaFichaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Nova Ficha de Treino</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formNovaFichaForm" class="form-modal">
                <div class="form-group">
                    <label for="fichanome">Nome da Ficha *</label>
                    <input type="text" id="fichanome" name="fichaname" required placeholder="Ex: Treino A - Peito">
                </div>

                <div class="form-group">
                    <label for="fichadescricao">Descrição</label>
                    <textarea id="fichadescricao" name="fichadescricao" placeholder="Descreva a ficha..."></textarea>
                </div>

                <div class="form-group">
                    <label for="fichadias">Dia da Semana</label>
                    <select id="fichadias" name="fichadias">
                        <option value="">Selecione um dia</option>
                        <option value="Segunda-feira">Segunda-feira</option>
                        <option value="Terça-feira">Terça-feira</option>
                        <option value="Quarta-feira">Quarta-feira</option>
                        <option value="Quinta-feira">Quinta-feira</option>
                        <option value="Sexta-feira">Sexta-feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <div id="mensagemErroModalFicha" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Criar Ficha</button>
            </form>
        </div>
    </div>

    <!-- Modal Registrar Treino -->
    <div id="modalRegistroTreinoOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Registrar Treino</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formRegistroTreinoForm" class="form-modal">
                <div class="form-group">
                    <label for="fichaSelect">Selecione a Ficha *</label>
                    <select id="fichaSelect" name="fichaSelect" required>
                        <option value="">Escolha uma ficha</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dataTreino">Data do Treino *</label>
                    <input type="date" id="dataTreino" name="dataTreino" required>
                </div>

                <div class="form-group">
                    <label for="duracaoMinutos">Duração (minutos) *</label>
                    <input type="number" id="duracaoMinutos" name="duracaoMinutos" required min="1">
                </div>

                <div class="form-group">
                    <label for="intensidade">Intensidade</label>
                    <select id="intensidade" name="intensidade">
                        <option value="leve">Leve</option>
                        <option value="moderada" selected>Moderada</option>
                        <option value="intensa">Intensa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notasTreino">Notas</label>
                    <textarea id="notasTreino" name="notasTreino" placeholder="Como se sentiu? Alguma observação?"></textarea>
                </div>

                <div id="mensagemErroModalTreino" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Registrar Treino</button>
            </form>
        </div>
    </div>
    <!-- Modal Editar Ficha -->
    <div id="modalEditarFichaOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Editar Ficha de Treino</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formEditarFichaForm" class="form-modal">
                <input type="hidden" id="edit_ficha_id" name="id">
                <div class="form-group">
                    <label for="edit_fichanome">Nome da Ficha *</label>
                    <input type="text" id="edit_fichanome" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="edit_fichadescricao">Descrição</label>
                    <textarea id="edit_fichadescricao" name="descricao"></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_fichadias">Dia da Semana</label>
                    <select id="edit_fichadias" name="dias_semana">
                        <option value="">Selecione um dia</option>
                        <option value="Segunda-feira">Segunda-feira</option>
                        <option value="Terça-feira">Terça-feira</option>
                        <option value="Quarta-feira">Quarta-feira</option>
                        <option value="Quinta-feira">Quinta-feira</option>
                        <option value="Sexta-feira">Sexta-feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <div id="mensagemErroModalFichaEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Exercício -->
    <div id="modalAdicionarExercicioOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Adicionar Exercício</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formAdicionarExercicioForm" class="form-modal">
                <input type="hidden" id="exercicio_ficha_id" name="ficha_id">

                <div class="form-group">
                    <label for="exercicioNome">Nome do Exercício *</label>
                    <input type="text" id="exercicioNome" name="nome" required placeholder="Ex: Supino Reto">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="exercicioSeries">Séries *</label>
                        <input type="number" id="exercicioSeries" name="series" required min="1" placeholder="Ex: 4">
                    </div>
                    <div class="form-group">
                        <label for="exercicioRepeticoes">Repetições *</label>
                        <input type="number" id="exercicioRepeticoes" name="repeticoes" required min="1" placeholder="Ex: 12">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="exercicioPeso">Carga</label>
                        <input type="text" id="exercicioPeso" name="peso" placeholder="Ex: 80kg">
                    </div>
                    <div class="form-group">
                        <label for="exercicioDescanso">Descanso (seg)</label>
                        <input type="number" id="exercicioDescanso" name="descanso" min="0" placeholder="Ex: 60">
                    </div>
                </div>

                <div class="form-group">
                    <label for="exercicioNotas">Observações</label>
                    <textarea id="exercicioNotas" name="notas" placeholder="Ex: Descer até o peito, controlar a fase excêntrica"></textarea>
                </div>

                <div id="mensagemErroModalExercicio" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Adicionar Exercício</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Exercício -->
    <div id="modalEditarExercicioOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Editar Exercício</h2>
                <button class="modal-close">&times;</button>
            </div>
            <form id="formEditarExercicioForm" class="form-modal">
                <input type="hidden" id="edit_exercicio_id" name="id">
                <input type="hidden" id="edit_exercicio_ficha_id" name="ficha_id">

                <div class="form-group">
                    <label for="edit_exercicioNome">Nome do Exercício *</label>
                    <input type="text" id="edit_exercicioNome" name="nome" required placeholder="Ex: Supino Reto">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_exercicioSeries">Séries *</label>
                        <input type="number" id="edit_exercicioSeries" name="series" required min="1" placeholder="Ex: 4">
                    </div>
                    <div class="form-group">
                        <label for="edit_exercicioRepeticoes">Repetições *</label>
                        <input type="number" id="edit_exercicioRepeticoes" name="repeticoes" required min="1" placeholder="Ex: 12">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_exercicioPeso">Carga</label>
                        <input type="text" id="edit_exercicioPeso" name="peso" placeholder="Ex: 80kg">
                    </div>
                    <div class="form-group">
                        <label for="edit_exercicioDescanso">Descanso (seg)</label>
                        <input type="number" id="edit_exercicioDescanso" name="descanso" min="0" placeholder="Ex: 60">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_exercicioNotas">Observações</label>
                    <textarea id="edit_exercicioNotas" name="notas" placeholder="Ex: Descer até o peito, controlar a fase excêntrica"></textarea>
                </div>

                <div id="mensagemErroModalExercicioEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="js/confirm-modal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/treinos.js"></script>
</body>
</html>
