// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    atualizarSaudacao();
    carregarDados();
    configurarModais();
    configurarLogout();
});

// ── Dynamic Greeting ──────────────────────────────────────────────────
function atualizarSaudacao() {
    const hora = new Date().getHours();
    let saudacao;
    if (hora < 12) saudacao = 'Bom dia';
    else if (hora < 18) saudacao = 'Boa tarde';
    else saudacao = 'Boa noite';

    const el = document.getElementById('saudacao');
    if (el) {
        const nome = el.textContent.replace(/^.*,\s*/, '').trim();
        el.textContent = `${saudacao}, ${nome}`;
    }

    const sub = document.getElementById('headerSubtitle');
    if (sub) {
        const hoje = new Date();
        const opcoes = { weekday: 'long', day: 'numeric', month: 'long' };
        const dataFormatada = hoje.toLocaleDateString('pt-BR', opcoes);
        sub.textContent = dataFormatada.charAt(0).toUpperCase() + dataFormatada.slice(1);
    }
}

// ── Load Data ─────────────────────────────────────────────────────────
function carregarDados() {
    fetch('api/metas.php?acao=listar')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirMetasProximas(data.dados);
                atualizarContadorMetas(data.dados);
            }
        });

    fetch('api/tarefas.php?acao=listar&filtro=hoje')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirTarefasHoje(data.dados);
                atualizarContadorTarefas(data.dados);
            }
        });

    fetch('api/treinos.php?acao=estatisticas')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                atualizarEstatisticasTreinos(data.dados);
            }
        });

    fetch('api/treinos.php?acao=listar_registros')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                renderizarGraficoTreinos(data.dados);
            }
        });
}

// ── Display Goals ─────────────────────────────────────────────────────
function exibirMetasProximas(metas) {
    const container = document.getElementById('metasProximas');
    container.innerHTML = '';

    if (metas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma meta registrada</p>';
        return;
    }

    const metasOrdena = metas
        .filter(m => m.status !== 'cancelada' && m.status !== 'concluida')
        .sort((a, b) => new Date(a.data_termino) - new Date(b.data_termino))
        .slice(0, 3);

    if (metasOrdena.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma meta em andamento</p>';
        return;
    }

    metasOrdena.forEach(meta => {
        const dias = calcularDiasRestantes(meta.data_termino);
        const progressoCalculado = calcularProgressoData(meta.data_inicio, meta.data_termino);
        const diasTexto = dias > 0 ? `${dias} dias restantes` : progressoCalculado >= 100 ? 'Finalizada' : 'Vencida';
        
        const item = document.createElement('div');
        item.className = 'item fade-in';
        item.innerHTML = `
            <div class="item-content">
                <div class="item-title">${meta.titulo}</div>
                <div class="item-meta">
                    ${progressoCalculado}% concluído · ${diasTexto}
                </div>
            </div>
            <div class="item-actions">
                <a href="metas.php" class="item-btn">Ver</a>
            </div>
        `;
        container.appendChild(item);
    });
}

// ── Display Tasks ─────────────────────────────────────────────────────
function exibirTarefasHoje(tarefas) {
    const container = document.getElementById('tarefasHoje');
    container.innerHTML = '';

    if (tarefas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma tarefa para hoje</p>';
        return;
    }

    tarefas.slice(0, 3).forEach(tarefa => {
        const prioridadeClass = tarefa.prioridade === 'alta' ? 'badge-danger' : tarefa.prioridade === 'media' ? 'badge-warning' : 'badge-success';
        const prioridadeLabel = tarefa.prioridade.charAt(0).toUpperCase() + tarefa.prioridade.slice(1);

        const item = document.createElement('div');
        item.className = 'item fade-in';
        item.innerHTML = `
            <div class="item-content">
                <div class="item-title">${tarefa.titulo}</div>
                <div class="item-meta">
                    <span class="badge ${prioridadeClass}">${prioridadeLabel}</span>
                </div>
            </div>
            <div class="item-actions">
                <a href="tarefas.php" class="item-btn">Ver</a>
            </div>
        `;
        container.appendChild(item);
    });
}

// ── Update Counters ───────────────────────────────────────────────────
function atualizarContadorMetas(metas) {
    const el = document.getElementById('metasAtivasCount');
    const ativas = metas.filter(m => m.status === 'em_progresso').length;
    animarContador(el, ativas);
}

function atualizarContadorTarefas(tarefas) {
    const el = document.getElementById('tarefasHojeCount');
    animarContador(el, tarefas.length);
}

function atualizarEstatisticasTreinos(stats) {
    animarContador(document.getElementById('treinosCount'), stats.total_treinos || 0);
    
    const tempoTotal = stats.tempo_total || 0;
    const horas = Math.floor(tempoTotal / 60);
    const minutos = tempoTotal % 60;
    const tempoTexto = horas > 0 ? `${horas}h ${minutos}m` : `${minutos}m`;
    document.getElementById('tempoTreino').textContent = tempoTexto;
}

// ── Counter Animation ─────────────────────────────────────────────────
function animarContador(el, targetValue) {
    if (!el) return;
    const duration = 600;
    const start = performance.now();
    const startValue = parseInt(el.textContent) || 0;

    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
        el.textContent = Math.round(startValue + (targetValue - startValue) * eased);
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// ── Chart ─────────────────────────────────────────────────────────────
let chartInstance = null;

function renderizarGraficoTreinos(registros) {
    const ctx = document.getElementById('chartTreinos');
    if (!ctx) return;

    if (chartInstance) chartInstance.destroy();

    const agrupado = {};
    registros.forEach(reg => {
        const data = reg.data_treino.split(' ')[0];
        if (!agrupado[data]) agrupado[data] = 0;
        agrupado[data] += parseInt(reg.duracao_minutos) || 0;
    });

    const labels = [];
    const dados = [];
    const datasOrdenadas = Object.keys(agrupado).sort();

    if (datasOrdenadas.length === 0) {
        const hoje = new Date();
        labels.push(`${String(hoje.getDate()).padStart(2, '0')}/${String(hoje.getMonth() + 1).padStart(2, '0')}`);
        dados.push(0);
    } else {
        datasOrdenadas.forEach(data => {
            const partes = data.split('-');
            if (partes.length === 3) labels.push(`${partes[2]}/${partes[1]}`);
            else labels.push(data);
            dados.push(agrupado[data]);
        });
    }

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Minutos de Treino',
                data: dados,
                backgroundColor: gradient,
                borderColor: 'rgba(99, 102, 241, 0.9)',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(129, 140, 248, 0.9)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    borderColor: 'rgba(148, 163, 184, 0.12)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.raw} minutos`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.06)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Inter', size: 12 },
                        padding: 8
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Inter', size: 12 },
                        padding: 8
                    },
                    border: { display: false }
                }
            }
        }
    });
}

// ── Helpers ───────────────────────────────────────────────────────────
function calcularDiasRestantes(data) {
    const hoje = new Date();
    const termino = new Date(data);
    return Math.ceil((termino - hoje) / (1000 * 60 * 60 * 24));
}

function calcularProgressoData(dataInicio, dataTermino) {
    if (!dataInicio || !dataTermino) return 0;
    const hoje = new Date(); hoje.setHours(0, 0, 0, 0);
    const inicio = new Date(dataInicio + 'T00:00:00'); inicio.setHours(0, 0, 0, 0);
    const termino = new Date(dataTermino + 'T00:00:00'); termino.setHours(0, 0, 0, 0);
    if (inicio > termino) return 0;
    if (hoje < inicio) return 0;
    if (hoje >= termino) return 100;
    const msPorDia = 86400000;
    const totalDias = Math.round((termino - inicio) / msPorDia) + 1;
    const diasPassados = Math.round((hoje - inicio) / msPorDia) + 1;
    return Math.round((diasPassados / totalDias) * 100);
}

// ── Modals ────────────────────────────────────────────────────────────
function configurarModais() {
    const modalConfig = document.getElementById('modalConfiguracoesOverlay');
    const btnConfig = document.getElementById('btnConfiguracoes');
    const btnFecharConfig = modalConfig.querySelector('.modal-close');
    const formConfig = document.getElementById('formConfiguracoes');

    if (btnConfig) {
        btnConfig.addEventListener('click', () => {
            document.getElementById('mensagemErroConfig').classList.remove('ativo');
            document.getElementById('mensagemSucessoConfig').classList.remove('ativo');
            document.getElementById('configSenhaNova').value = '';
            modalConfig.classList.add('ativo');
        });
    }

    if (btnFecharConfig) {
        btnFecharConfig.addEventListener('click', () => modalConfig.classList.remove('ativo'));
    }

    if (modalConfig) {
        modalConfig.addEventListener('click', (e) => {
            if (e.target === modalConfig) modalConfig.classList.remove('ativo');
        });
    }

    if (formConfig) {
        formConfig.addEventListener('submit', function(e) {
            e.preventDefault();
            salvarConfiguracoes();
        });
    }
}

function salvarConfiguracoes() {
    const nome = document.getElementById('configNome').value.trim();
    const senhaNova = document.getElementById('configSenhaNova').value;
    const msgErro = document.getElementById('mensagemErroConfig');
    const msgSucesso = document.getElementById('mensagemSucessoConfig');
    const botao = document.querySelector('#formConfiguracoes button[type="submit"]');

    msgErro.classList.remove('ativo');
    msgSucesso.classList.remove('ativo');

    if (!nome) {
        msgErro.textContent = 'O nome não pode estar vazio';
        msgErro.classList.add('ativo');
        return;
    }

    if (senhaNova && senhaNova.length < 6) {
        msgErro.textContent = 'A nova senha deve ter no mínimo 6 caracteres';
        msgErro.classList.add('ativo');
        return;
    }

    botao.disabled = true;
    botao.innerHTML = '<span class="btn-spinner"></span> Salvando...';

    const formData = new FormData();
    formData.append('nome', nome);
    if (senhaNova) formData.append('senha_nova', senhaNova);

    fetch('api/auth.php?acao=atualizar_perfil', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            msgSucesso.textContent = data.mensagem;
            msgSucesso.classList.add('ativo');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            msgErro.textContent = data.mensagem || 'Erro ao salvar configurações';
            msgErro.classList.add('ativo');
            botao.disabled = false;
            botao.textContent = 'Salvar Alterações';
        }
    })
    .catch(() => {
        msgErro.textContent = 'Erro de conexão com o servidor';
        msgErro.classList.add('ativo');
        botao.disabled = false;
        botao.textContent = 'Salvar Alterações';
    });
}

// ── Logout ────────────────────────────────────────────────────────────
function configurarLogout() {
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', function() {
            confirmarAcao(
                'Sair da conta',
                'Deseja sair da sua conta?',
                function() {
                    fetch('api/auth.php?acao=logout', { method: 'POST' })
                    .then(() => { window.location.href = 'login.php'; });
                },
                '👋'
            );
        });
    }
}
