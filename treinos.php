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
    <meta name="apple-mobile-web-app-title" content="P.&nbspEstoica">
    <!-- Ícone da Tela de Início (iOS) -->
    <link rel="apple-touch-icon" sizes="180x180" href="icon/favicon.png">
    <title>Treinos — Performance Estoica</title>
    <link rel="icon" type="image/svg+xml" href="icon/favicon.svg">
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
                <a href="tarefas.php" class="nav-item">
                    <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg></span>
                    <span>Tarefas</span>
                </a>
                <a href="treinos.php" class="nav-item ativo">
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
            <header class="top-header">
                <div class="header-title-wrapper">
                    <button id="btnMobileMenu" class="mobile-menu-btn" aria-label="Abrir menu">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                    </button>
                    <h1>Meus Treinos</h1>
                </div>
                <div class="header-actions">
                    <button id="btnNovaFichaModal" class="btn btn-primary btn-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        Nova Ficha
                    </button>
                    <button id="btnRegistroTreinoModal" class="btn btn-secondary btn-small">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                        Registrar Treino
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
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formNovaFichaForm" class="form-modal">
                <div class="form-group">
                    <label for="fichanome">Nome da Ficha <span class="required">*</span></label>
                    <input type="text" id="fichanome" name="fichaname" required placeholder="Ex: Treino A — Peito">
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
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formRegistroTreinoForm" class="form-modal">
                <div class="form-group">
                    <label for="fichaSelect">Selecione a Ficha <span class="required">*</span></label>
                    <select id="fichaSelect" name="fichaSelect" required>
                        <option value="">Escolha uma ficha</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dataTreino">Data do Treino <span class="required">*</span></label>
                    <input type="date" id="dataTreino" name="dataTreino" required>
                </div>
                <div class="form-group">
                    <label for="duracaoMinutos">Duração (minutos) <span class="required">*</span></label>
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
                <h2>Editar Ficha</h2>
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formEditarFichaForm" class="form-modal">
                <input type="hidden" id="edit_ficha_id" name="id">
                <div class="form-group">
                    <label for="edit_fichanome">Nome da Ficha <span class="required">*</span></label>
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
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formAdicionarExercicioForm" class="form-modal">
                <input type="hidden" id="exercicio_ficha_id" name="ficha_id">
                <div class="form-group">
                    <label for="exercicioNome">Nome do Exercício <span class="required">*</span></label>
                    <input type="text" id="exercicioNome" name="nome" required placeholder="Ex: Supino Reto">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exercicioSeries">Séries <span class="required">*</span></label>
                        <input type="number" id="exercicioSeries" name="series" required min="1" placeholder="4">
                    </div>
                    <div class="form-group">
                        <label for="exercicioRepeticoes">Repetições <span class="required">*</span></label>
                        <input type="number" id="exercicioRepeticoes" name="repeticoes" required min="1" placeholder="12">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exercicioPeso">Carga</label>
                        <input type="text" id="exercicioPeso" name="peso" placeholder="80kg">
                    </div>
                    <div class="form-group">
                        <label for="exercicioDescanso">Descanso (seg)</label>
                        <input type="number" id="exercicioDescanso" name="descanso" min="0" placeholder="60">
                    </div>
                </div>
                <div class="form-group">
                    <label for="exercicioNotas">Observações</label>
                    <textarea id="exercicioNotas" name="notas" placeholder="Controlar fase excêntrica..."></textarea>
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
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formEditarExercicioForm" class="form-modal">
                <input type="hidden" id="edit_exercicio_id" name="id">
                <input type="hidden" id="edit_exercicio_ficha_id" name="ficha_id">
                <div class="form-group">
                    <label for="edit_exercicioNome">Nome do Exercício <span class="required">*</span></label>
                    <input type="text" id="edit_exercicioNome" name="nome" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_exercicioSeries">Séries <span class="required">*</span></label>
                        <input type="number" id="edit_exercicioSeries" name="series" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="edit_exercicioRepeticoes">Repetições <span class="required">*</span></label>
                        <input type="number" id="edit_exercicioRepeticoes" name="repeticoes" required min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_exercicioPeso">Carga</label>
                        <input type="text" id="edit_exercicioPeso" name="peso">
                    </div>
                    <div class="form-group">
                        <label for="edit_exercicioDescanso">Descanso (seg)</label>
                        <input type="number" id="edit_exercicioDescanso" name="descanso" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_exercicioNotas">Observações</label>
                    <textarea id="edit_exercicioNotas" name="notas"></textarea>
                </div>
                <div id="mensagemErroModalExercicioEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>
    <!-- Modal Editar Registro de Treino -->
    <div id="modalEditarRegistroOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Editar Registro</h2>
                <button class="modal-close" aria-label="Fechar">&times;</button>
            </div>
            <form id="formEditarRegistroForm" class="form-modal">
                <input type="hidden" id="edit_registro_id" name="id">
                <div class="form-group">
                    <label for="edit_registro_ficha">Ficha <span class="required">*</span></label>
                    <select id="edit_registro_ficha" name="ficha_id" required>
                        <option value="">Escolha uma ficha</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_registro_data">Data do Treino <span class="required">*</span></label>
                    <input type="date" id="edit_registro_data" name="data_treino" required>
                </div>
                <div class="form-group">
                    <label for="edit_registro_duracao">Duração (minutos) <span class="required">*</span></label>
                    <input type="number" id="edit_registro_duracao" name="duracao_minutos" required min="1">
                </div>
                <div class="form-group">
                    <label for="edit_registro_intensidade">Intensidade</label>
                    <select id="edit_registro_intensidade" name="intensidade">
                        <option value="leve">Leve</option>
                        <option value="moderada">Moderada</option>
                        <option value="intensa">Intensa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_registro_notas">Notas</label>
                    <textarea id="edit_registro_notas" name="notas"></textarea>
                </div>
                <div id="mensagemErroModalRegistroEdit" class="mensagem-erro"></div>
                <button type="submit" class="btn btn-primary btn-full">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script src="js/confirm-modal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/treinos.js"></script>
</body>
</html>
