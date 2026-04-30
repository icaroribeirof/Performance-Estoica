// Metas JavaScript

let filtroAtual = 'todas';
let listaMetas = [];

document.addEventListener('DOMContentLoaded', function() {
    carregarMetas();
    configurarModal();
    configurarFiltros();
    configurarLogout();
});

function carregarMetas() {
    fetch('api/metas.php?acao=listar')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                listaMetas = data.dados;
                exibirMetas(data.dados);
            }
        })
        .catch(error => console.error('Erro:', error));
}

function exibirMetas(metas) {
    const container = document.getElementById('metasContainer');
    container.innerHTML = '';

    let metasFiltradas = metas;
    if (filtroAtual !== 'todas') {
        metasFiltradas = metas.filter(m => m.status === filtroAtual);
    }

    if (metasFiltradas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma meta encontrada</p>';
        return;
    }

    metasFiltradas.forEach(meta => {
        const dias = calcularDiasRestantes(meta.data_termino);
        const progressoCalculado = calcularProgressoData(meta.data_inicio, meta.data_termino);
        const card = document.createElement('div');
        card.className = 'meta-card';
        card.innerHTML = `
            <div class="meta-header">
                <h3 class="meta-title">${meta.titulo}</h3>
                <span class="meta-status ${meta.status}">${meta.status === 'em_progresso' ? 'Em Progresso' : meta.status === 'concluida' ? 'Concluída' : 'Cancelada'}</span>
            </div>
            ${meta.descricao ? `<p class="meta-descricao">${meta.descricao}</p>` : ''}
            <div class="meta-data">
                Início: ${formatarData(meta.data_inicio)} • Término: ${formatarData(meta.data_termino)} • ${dias > 0 ? `${dias} dias` : 'Vencida'}
            </div>
            <div class="meta-progress">
                <div class="progress-label">
                    <span>Progresso Temporal</span>
                    <span>${progressoCalculado}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${progressoCalculado}%"></div>
                </div>
            </div>
            <div class="meta-actions">
                <button class="btn btn-secondary btn-small" onclick="abrirModalEdicaoCompleta(${meta.id})">Editar</button>
                <button class="btn btn-danger btn-small" onclick="deletarMeta(${meta.id})">Deletar</button>
            </div>
        `;
        container.appendChild(card);
    });
}

function configurarFiltros() {
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('ativo'));
            this.classList.add('ativo');
            filtroAtual = this.dataset.filtro;
            carregarMetas();
        });
    });
}

function configurarModal() {
    const modal = document.getElementById('modalNovaMetaOverlay');
    const btnAbrir = document.getElementById('btnNovaMetaModal');
    const btnFechar = modal.querySelector('.modal-close');
    const form = document.getElementById('formNovaMetaForm');

    btnAbrir.addEventListener('click', () => modal.classList.add('ativo'));
    btnFechar.addEventListener('click', () => modal.classList.remove('ativo'));
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('ativo');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarNovaMetadata();
    });

    // Data hoje como padrão (usando data local para evitar problema de fuso horário)
    const hoje = new Date();
    const hojeLocal = `${hoje.getFullYear()}-${String(hoje.getMonth() + 1).padStart(2, '0')}-${String(hoje.getDate()).padStart(2, '0')}`;
    document.getElementById('dataInicio').value = hojeLocal;

    // Configurar Modal Edição Completa
    const modalEdit = document.getElementById('modalEditarMetaOverlay');
    const btnFecharEdit = modalEdit.querySelector('.modal-close');
    const formEdit = document.getElementById('formEditarMetaForm');

    btnFecharEdit.addEventListener('click', () => modalEdit.classList.remove('ativo'));
    modalEdit.addEventListener('click', (e) => {
        if (e.target === modalEdit) modalEdit.classList.remove('ativo');
    });

    formEdit.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarEdicaoCompletaMeta();
    });
}

function salvarNovaMetadata() {
    const titulo = document.getElementById('titulo').value;
    const descricao = document.getElementById('descricao').value;
    const categoria = document.getElementById('categoria').value;
    const dataInicio = document.getElementById('dataInicio').value;
    const dataTermino = document.getElementById('dataTermino').value;
    const mensagemErro = document.getElementById('mensagemErroModal');

    if (!titulo || !dataInicio || !dataTermino) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Preencha os campos obrigatórios';
        return;
    }

    const formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('descricao', descricao);
    formData.append('categoria', categoria);
    formData.append('data_inicio', dataInicio);
    formData.append('data_termino', dataTermino);

    fetch('api/metas.php?acao=adicionar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalNovaMetaOverlay').classList.remove('ativo');
            document.getElementById('formNovaMetaForm').reset();
            carregarMetas();
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem;
        }
    });
}

function abrirModalEdicaoCompleta(id) {
    const meta = listaMetas.find(m => m.id == id);
    if (!meta) return;

    document.getElementById('edit_meta_id').value = meta.id;
    document.getElementById('edit_meta_titulo').value = meta.titulo;
    document.getElementById('edit_meta_descricao').value = meta.descricao || '';
    document.getElementById('edit_meta_categoria').value = meta.categoria || '';
    document.getElementById('edit_meta_dataInicio').value = meta.data_inicio;
    document.getElementById('edit_meta_dataTermino').value = meta.data_termino;

    document.getElementById('modalEditarMetaOverlay').classList.add('ativo');
}

function salvarEdicaoCompletaMeta() {
    const id = document.getElementById('edit_meta_id').value;
    const titulo = document.getElementById('edit_meta_titulo').value;
    const descricao = document.getElementById('edit_meta_descricao').value;
    const categoria = document.getElementById('edit_meta_categoria').value;
    const dataInicio = document.getElementById('edit_meta_dataInicio').value;
    const dataTermino = document.getElementById('edit_meta_dataTermino').value;
    const mensagemErro = document.getElementById('mensagemErroModalEdit');

    const formData = new FormData();
    formData.append('id', id);
    formData.append('titulo', titulo);
    formData.append('descricao', descricao);
    formData.append('categoria', categoria);
    formData.append('data_inicio', dataInicio);
    formData.append('data_termino', dataTermino);

    fetch('api/metas.php?acao=editar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalEditarMetaOverlay').classList.remove('ativo');
            carregarMetas();
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem;
        }
    });
}

function calcularProgressoData(dataInicio, dataTermino) {
    if (!dataInicio || !dataTermino) return 0;
    
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    
    const inicio = new Date(dataInicio + 'T00:00:00');
    inicio.setHours(0, 0, 0, 0);
    
    const termino = new Date(dataTermino + 'T00:00:00');
    termino.setHours(0, 0, 0, 0);
    
    if (inicio > termino) return 0;
    if (hoje < inicio) return 0;
    if (hoje >= termino) return 100;
    
    const msPorDia = 1000 * 60 * 60 * 24;
    const totalDias = Math.round((termino - inicio) / msPorDia) + 1;
    const diasPassados = Math.round((hoje - inicio) / msPorDia) + 1;
    
    return Math.round((diasPassados / totalDias) * 100);
}

function deletarMeta(metaId) {
    confirmarAcao(
        'Deletar Meta',
        'Tem certeza que deseja deletar esta meta? Esta ação não pode ser desfeita.',
        function() {
            const formData = new FormData();
            formData.append('id', metaId);

            fetch('api/metas.php?acao=deletar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    carregarMetas();
                }
            });
        }
    );
}

function calcularDiasRestantes(data) {
    const hoje = new Date();
    const termino = new Date(data);
    const diferenca = termino - hoje;
    return Math.ceil(diferenca / (1000 * 60 * 60 * 24));
}

function formatarData(data) {
    if (!data) return '';
    const partes = data.split('-');
    if (partes.length < 3) return data;
    const [ano, mes, dia] = partes;
    const diaLimpo = dia.substring(0, 2);
    return `${diaLimpo}/${mes}/${ano}`;
}

function configurarLogout() {
    document.getElementById('btnLogout').addEventListener('click', function() {
        confirmarAcao(
            'Sair da conta',
            'Deseja sair da sua conta?',
            function() {
                fetch('api/auth.php?acao=logout', {
                    method: 'POST'
                })
                .then(() => {
                    window.location.href = 'login.php';
                });
            },
            '🚪'
        );
    });
}
