document.addEventListener('DOMContentLoaded', function () {
    carregarTarefas();
    configurarModal();
    configurarFiltros();
    configurarLogout();
});

let filtroAtual = 'todas';
let listaTarefas = [];
let processandoConclusao = false;

// ─── HELPER ───────────────────────────────────────────────────────────────────
function el(id) { return document.getElementById(id); }

// SVG icon templates
const ICONS = {
    edit: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
    trash: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>',
    repeat: '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>',
    warning: '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>'
};

// ─── CARREGAR ─────────────────────────────────────────────────────────────────
function carregarTarefas() {
    const filtro = filtroAtual === 'alta' ? 'todas' : filtroAtual;
    fetch(`api/tarefas.php?acao=listar&filtro=${filtro}`)
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                listaTarefas = data.dados;
                exibirTarefas(data.dados);
            }
        })
        .catch(err => console.error('Erro ao carregar tarefas:', err));
}

// ─── EXIBIR ───────────────────────────────────────────────────────────────────
function exibirTarefas(tarefas) {
    const container = el('tarefasContainer');
    if (!container) return;
    container.innerHTML = '';

    let lista = filtroAtual === 'alta' ? tarefas.filter(t => t.prioridade === 'alta') : tarefas;

    if (lista.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma tarefa encontrada</p>';
        return;
    }

    const hoje = new Date();

    lista.forEach((tarefa, index) => {
        const dataAtual  = tarefa.data_atual ? new Date(tarefa.data_atual.replace(' ', 'T')) : null;
        const isAtrasada = dataAtual && dataAtual < hoje && !tarefa.concluida;

        const item = document.createElement('div');
        item.className = `tarefa-item ${tarefa.concluida ? 'concluida' : ''} ${isAtrasada ? 'atrasada' : ''} stagger-item`;
        item.style.animationDelay = `${index * 40}ms`;

        const avisoAtraso = isAtrasada
            ? `<span class="badge badge-danger" style="margin-left:6px;">${ICONS.warning} Atrasada</span>`
            : '';

        let infoData = '';
        if (tarefa.data_atual) {
            infoData += `<span>Data: ${formatarData(tarefa.data_atual)}</span>`;
        }
        if (tarefa.recorrencia !== 'nenhuma' && tarefa.data_vencimento) {
            infoData += `<span>Limite: ${formatarData(tarefa.data_vencimento)}</span>`;
        }

        item.innerHTML = `
            <input type="checkbox" class="tarefa-checkbox" data-id="${tarefa.id}" ${tarefa.concluida ? 'checked' : ''} aria-label="Marcar como ${tarefa.concluida ? 'pendente' : 'concluída'}">
            <div class="tarefa-content">
                <div class="tarefa-titulo">
                    ${escapeHtml(tarefa.titulo)} ${avisoAtraso}
                </div>
                ${tarefa.descricao ? `<div class="tarefa-descricao">${escapeHtml(tarefa.descricao)}</div>` : ''}
                <div class="tarefa-meta">
                    <span class="tarefa-prioridade ${tarefa.prioridade}">
                        ${tarefa.prioridade.charAt(0).toUpperCase() + tarefa.prioridade.slice(1)}
                    </span>
                    ${infoData}
                    ${tarefa.recorrencia !== 'nenhuma'
                        ? `<span class="tarefa-recorrencia">${ICONS.repeat} ${formatarRecorrencia(tarefa.recorrencia)}</span>`
                        : ''}
                </div>
            </div>
            <div class="tarefa-actions">
                <button class="tarefa-btn btn-editar" title="Editar" data-id="${tarefa.id}">${ICONS.edit}</button>
                <button class="tarefa-btn btn-deletar" title="Deletar" data-id="${tarefa.id}">${ICONS.trash}</button>
            </div>
        `;
        container.appendChild(item);
    });

    container.querySelectorAll('.tarefa-checkbox').forEach(cb => {
        cb.addEventListener('change', handleCheckboxChange);
    });
    container.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => abrirModalEdicao(parseInt(btn.dataset.id)));
    });
    container.querySelectorAll('.btn-deletar').forEach(btn => {
        btn.addEventListener('click', () => deletarTarefa(parseInt(btn.dataset.id)));
    });
}

// ─── CHECKBOX ─────────────────────────────────────────────────────────────────
function handleCheckboxChange(e) {
    e.stopPropagation();
    if (processandoConclusao) return;

    const checkbox  = e.target;
    const tarefaId  = parseInt(checkbox.dataset.id);
    const concluida = checkbox.checked;

    processandoConclusao = true;

    const fd = new FormData();
    fd.append('id', tarefaId);
    fd.append('concluida', concluida ? 'true' : 'false');

    fetch('api/tarefas.php?acao=concluir', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                carregarTarefas();
            } else {
                checkbox.checked = !concluida;
            }
        })
        .catch(() => { checkbox.checked = !concluida; })
        .finally(() => { processandoConclusao = false; });
}

// ─── CONFIGURAR MODAIS ────────────────────────────────────────────────────────
function configurarModal() {
    const modalNovo = el('modalNovaTarefaOverlay');
    const btnAbrir  = el('btnNovaTarefaModal');
    const formNovo  = el('formNovaTarefaForm');

    if (!modalNovo || !btnAbrir || !formNovo) return;

    const btnFechar = modalNovo.querySelector('.modal-close');
    const selectRec = el('recorrencia');
    const grpLimite = el('group_data_limite');
    const inputData = el('dataAtual');

    btnAbrir.addEventListener('click', () => {
        formNovo.reset();
        if (grpLimite) grpLimite.style.display = 'none';
        if (inputData) {
            const now = new Date();
            const offset = now.getTimezoneOffset() * 60000;
            inputData.value = (new Date(now - offset)).toISOString().substring(0, 16);
        }
        el('mensagemErroModal')?.classList.remove('ativo');
        modalNovo.classList.add('ativo');
    });

    if (btnFechar) btnFechar.addEventListener('click', () => modalNovo.classList.remove('ativo'));
    modalNovo.addEventListener('click', e => { if (e.target === modalNovo) modalNovo.classList.remove('ativo'); });

    if (selectRec && grpLimite) {
        selectRec.addEventListener('change', () => {
            grpLimite.style.display = selectRec.value !== 'nenhuma' ? '' : 'none';
        });
    }

    formNovo.addEventListener('submit', function (e) {
        e.preventDefault();
        salvarNovaTarefa();
    });

    const modalEdit = el('modalEditarTarefaOverlay');
    const formEdit  = el('formEditarTarefaForm');

    if (!modalEdit || !formEdit) return;

    const btnFecharEdit = modalEdit.querySelector('.modal-close');
    const selectRecEdit = el('edit_recorrencia');
    const grpLimiteEdit = el('edit_group_data_limite');

    if (btnFecharEdit) btnFecharEdit.addEventListener('click', () => modalEdit.classList.remove('ativo'));
    modalEdit.addEventListener('click', e => { if (e.target === modalEdit) modalEdit.classList.remove('ativo'); });

    if (selectRecEdit && grpLimiteEdit) {
        selectRecEdit.addEventListener('change', () => {
            grpLimiteEdit.style.display = selectRecEdit.value !== 'nenhuma' ? '' : 'none';
        });
    }

    formEdit.addEventListener('submit', function (e) {
        e.preventDefault();
        salvarEdicaoTarefa();
    });
}

// ─── SALVAR NOVA TAREFA ───────────────────────────────────────────────────────
function salvarNovaTarefa() {
    const titulo      = (el('titulo')?.value || '').trim();
    const descricao   = el('descricao')?.value || '';
    const prioridade  = el('prioridade')?.value || 'media';
    const dataAtual   = el('dataAtual')?.value || '';
    const dataVenc    = el('dataVencimento')?.value || '';
    const recorrencia = el('recorrencia')?.value || 'nenhuma';
    const msgErro     = el('mensagemErroModal');
    const botao       = document.querySelector('#formNovaTarefaForm button[type="submit"]');

    if (msgErro) msgErro.classList.remove('ativo');

    if (!titulo) {
        if (msgErro) { msgErro.textContent = 'Título é obrigatório'; msgErro.classList.add('ativo'); }
        return;
    }
    if (recorrencia !== 'nenhuma' && !dataVenc) {
        if (msgErro) { msgErro.textContent = 'Informe a data limite para tarefas recorrentes'; msgErro.classList.add('ativo'); }
        return;
    }

    if (!botao || botao.disabled) return;
    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Salvando...';

    const fd = new FormData();
    fd.append('titulo', titulo);
    fd.append('descricao', descricao);
    fd.append('prioridade', prioridade);
    fd.append('data_atual', dataAtual);
    fd.append('data_vencimento', dataVenc);
    fd.append('recorrencia', recorrencia);

    fetch('api/tarefas.php?acao=adicionar', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                el('modalNovaTarefaOverlay')?.classList.remove('ativo');
                carregarTarefas();
            } else {
                if (msgErro) { msgErro.textContent = data.mensagem || 'Erro ao salvar'; msgErro.classList.add('ativo'); }
            }
        })
        .catch(() => {
            if (msgErro) { msgErro.textContent = 'Erro ao conectar ao servidor'; msgErro.classList.add('ativo'); }
        })
        .finally(() => {
            if (botao) { botao.disabled = false; botao.textContent = 'Criar Tarefa'; }
        });
}

// ─── ABRIR MODAL EDIÇÃO ───────────────────────────────────────────────────────
function abrirModalEdicao(id) {
    const tarefa = listaTarefas.find(t => t.id == id);
    if (!tarefa) return;

    if (el('edit_id'))          el('edit_id').value          = tarefa.id;
    if (el('edit_titulo'))      el('edit_titulo').value      = tarefa.titulo;
    if (el('edit_descricao'))   el('edit_descricao').value   = tarefa.descricao || '';
    if (el('edit_prioridade'))  el('edit_prioridade').value  = tarefa.prioridade;
    if (el('edit_dataAtual'))   el('edit_dataAtual').value   = formatDatetimeLocal(tarefa.data_atual) || '';
    if (el('edit_recorrencia')) el('edit_recorrencia').value = tarefa.recorrencia || 'nenhuma';

    const temRec        = tarefa.recorrencia && tarefa.recorrencia !== 'nenhuma';
    const grpLimiteEdit = el('edit_group_data_limite');
    const inputLimite   = el('edit_dataVencimento');

    if (grpLimiteEdit) grpLimiteEdit.style.display = temRec ? '' : 'none';
    if (inputLimite)   inputLimite.value = temRec ? (formatDatetimeLocal(tarefa.data_vencimento) || '') : '';

    el('mensagemErroModalEdit')?.classList.remove('ativo');
    el('modalEditarTarefaOverlay')?.classList.add('ativo');
}

// ─── SALVAR EDIÇÃO ────────────────────────────────────────────────────────────
function salvarEdicaoTarefa() {
    const id          = el('edit_id')?.value || '';
    const titulo      = (el('edit_titulo')?.value || '').trim();
    const descricao   = el('edit_descricao')?.value || '';
    const prioridade  = el('edit_prioridade')?.value || 'media';
    const dataAtual   = el('edit_dataAtual')?.value || '';
    const dataVenc    = el('edit_dataVencimento')?.value || '';
    const recorrencia = el('edit_recorrencia')?.value || 'nenhuma';
    const msgErro     = el('mensagemErroModalEdit');
    const botao       = document.querySelector('#formEditarTarefaForm button[type="submit"]');

    if (msgErro) msgErro.classList.remove('ativo');

    if (!titulo) {
        if (msgErro) { msgErro.textContent = 'Título é obrigatório'; msgErro.classList.add('ativo'); }
        return;
    }
    if (recorrencia !== 'nenhuma' && !dataVenc) {
        if (msgErro) { msgErro.textContent = 'Informe a data limite para tarefas recorrentes'; msgErro.classList.add('ativo'); }
        return;
    }

    if (!botao || botao.disabled) return;
    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Salvando...';

    const fd = new FormData();
    fd.append('id', id);
    fd.append('titulo', titulo);
    fd.append('descricao', descricao);
    fd.append('prioridade', prioridade);
    fd.append('data_atual', dataAtual);
    fd.append('data_vencimento', dataVenc);
    fd.append('recorrencia', recorrencia);

    fetch('api/tarefas.php?acao=editar', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                el('modalEditarTarefaOverlay')?.classList.remove('ativo');
                carregarTarefas();
            } else {
                if (msgErro) { msgErro.textContent = data.mensagem || 'Erro ao salvar'; msgErro.classList.add('ativo'); }
            }
        })
        .catch(() => {
            if (msgErro) { msgErro.textContent = 'Erro ao conectar ao servidor'; msgErro.classList.add('ativo'); }
        })
        .finally(() => {
            if (botao) { botao.disabled = false; botao.textContent = 'Salvar Alterações'; }
        });
}

// ─── DELETAR ──────────────────────────────────────────────────────────────────
function deletarTarefa(tarefaId) {
    confirmarAcao(
        'Deletar Tarefa',
        'Deseja deletar esta tarefa permanentemente? Esta ação não pode ser desfeita.',
        function() {
            const fd = new FormData();
            fd.append('id', tarefaId);
            fetch('api/tarefas.php?acao=deletar', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => { if (data.sucesso) carregarTarefas(); });
        }
    );
}

// ─── FILTROS ──────────────────────────────────────────────────────────────────
function configurarFiltros() {
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('ativo'));
            this.classList.add('ativo');
            filtroAtual = this.dataset.filtro;
            carregarTarefas();
        });
    });
}

// ─── LOGOUT ───────────────────────────────────────────────────────────────────
function configurarLogout() {
    const btn = el('btnLogout');
    if (!btn) return;
    btn.addEventListener('click', () => {
        confirmarAcao(
            'Sair da conta',
            'Deseja sair da sua conta?',
            function() {
                fetch('api/auth.php?acao=logout', { method: 'POST' })
                    .finally(() => { window.location.href = 'login.php'; });
            },
            '👋'
        );
    });
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatDatetimeLocal(data) {
    if (!data) return '';
    return data.replace(' ', 'T').substring(0, 16);
}

function formatarData(data) {
    if (!data) return '';
    const partes = data.split(' ');
    const dataStr = partes[0];
    const timeStr = partes[1] || '';
    const [ano, mes, dia] = dataStr.split('-');
    let result = `${dia.substring(0, 2)}/${mes}/${ano}`;
    if (timeStr) result += ` às ${timeStr.substring(0, 5)}`;
    return result;
}

function formatarRecorrencia(rec) {
    const labels = { diaria: 'Diária', semanal: 'Semanal', anual: 'Anual', dias_semana: 'Seg-Sex' };
    return labels[rec] || rec;
}