// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    carregarDados();
    configurarModais();
    configurarLogout();
});

function carregarDados() {
    // Carregar metas
    fetch('api/metas.php?acao=listar')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirMetasProximas(data.dados);
                atualizarContadorMetas(data.dados);
            }
        });

    // Carregar tarefas de hoje
    fetch('api/tarefas.php?acao=listar&filtro=hoje')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirTarefasHoje(data.dados);
                atualizarContadorTarefas(data.dados);
            }
        });

    // Carregar treinos este mês
    fetch('api/treinos.php?acao=estatisticas')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                atualizarEstatisticasTreinos(data.dados);
            }
        });

    // Carregar registros de treino para o gráfico
    fetch('api/treinos.php?acao=listar_registros')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                renderizarGraficoTreinos(data.dados);
            }
        });
}

function exibirMetasProximas(metas) {
    const container = document.getElementById('metasProximas');
    container.innerHTML = '';

    if (metas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma meta registrada</p>';
        return;
    }

    const metasOrdena = metas
        .filter(m => m.status !== 'cancelada')
        .sort((a, b) => new Date(a.data_termino) - new Date(b.data_termino))
        .slice(0, 3);

    metasOrdena.forEach(meta => {
        const dias = calcularDiasRestantes(meta.data_termino);
        const diasTexto = dias > 0 ? `${dias} dias restantes` : 'Vencida';
        
        const item = document.createElement('div');
        item.className = 'item';
        item.innerHTML = `
            <div class="item-content">
                <div class="item-title">${meta.titulo}</div>
                <div class="item-meta">
                    Progresso: ${meta.progresso}% • ${diasTexto}
                </div>
            </div>
            <div class="item-actions">
                <a href="metas.php" class="item-btn">Ver</a>
            </div>
        `;
        container.appendChild(item);
    });
}

function exibirTarefasHoje(tarefas) {
    const container = document.getElementById('tarefasHoje');
    container.innerHTML = '';

    if (tarefas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma tarefa para hoje</p>';
        return;
    }

    tarefas.slice(0, 3).forEach(tarefa => {
        const item = document.createElement('div');
        item.className = 'item';
        item.innerHTML = `
            <div class="item-content">
                <div class="item-title">${tarefa.titulo}</div>
                <div class="item-meta">
                    Prioridade: ${tarefa.prioridade.charAt(0).toUpperCase() + tarefa.prioridade.slice(1)}
                </div>
            </div>
            <div class="item-actions">
                <a href="tarefas.php" class="item-btn">Ver</a>
            </div>
        `;
        container.appendChild(item);
    });
}

function atualizarContadorMetas(metas) {
    const metasAtivasCount = document.getElementById('metasAtivasCount');
    const ativas = metas.filter(m => m.status === 'em_progresso').length;
    metasAtivasCount.textContent = ativas;
}

function atualizarContadorTarefas(tarefas) {
    const tarefasHojeCount = document.getElementById('tarefasHojeCount');
    tarefasHojeCount.textContent = tarefas.length;
}

function atualizarEstatisticasTreinos(stats) {
    document.getElementById('treinosCount').textContent = stats.total_treinos || 0;
    
    const tempoTotal = stats.tempo_total_minutos || 0;
    const horas = Math.floor(tempoTotal / 60);
    const minutos = tempoTotal % 60;
    const tempoTexto = horas > 0 ? `${horas}h ${minutos}m` : `${minutos}m`;
    document.getElementById('tempoTreino').textContent = tempoTexto;
}

let chartInstance = null;

function renderizarGraficoTreinos(registros) {
    const ctx = document.getElementById('chartTreinos');
    if (!ctx) return;

    // Destruir gráfico anterior se existir (para evitar problemas de re-renderização)
    if (chartInstance) {
        chartInstance.destroy();
    }

    // Agrupar por data (dia)
    const agrupado = {};
    registros.forEach(reg => {
        const data = reg.data_treino.split(' ')[0]; // Pega apenas YYYY-MM-DD
        if (!agrupado[data]) {
            agrupado[data] = 0;
        }
        agrupado[data] += parseInt(reg.duracao_minutos) || 0;
    });

    const labels = [];
    const dados = [];
    
    // Ordenar as datas
    const datasOrdenadas = Object.keys(agrupado).sort();
    
    // Se não houver dados, exibir um estado vazio amigável no gráfico
    if (datasOrdenadas.length === 0) {
        const hoje = new Date();
        const dia = String(hoje.getDate()).padStart(2, '0');
        const mes = String(hoje.getMonth() + 1).padStart(2, '0');
        labels.push(`${dia}/${mes}`);
        dados.push(0);
    } else {
        // Formatar para DD/MM
        datasOrdenadas.forEach(data => {
            const partes = data.split('-');
            if(partes.length === 3) {
                labels.push(`${partes[2]}/${partes[1]}`);
            } else {
                labels.push(data);
            }
            dados.push(agrupado[data]);
        });
    }

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Minutos de Treino',
                data: dados,
                backgroundColor: '#6366f1',
                borderColor: '#4f46e5',
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: '#ec4899'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
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
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#cbd5e1'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#cbd5e1'
                    }
                }
            }
        }
    });
}

function calcularDiasRestantes(data) {
    const hoje = new Date();
    const termino = new Date(data);
    const diferenca = termino - hoje;
    return Math.ceil(diferenca / (1000 * 60 * 60 * 24));
}

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
    botao.textContent = 'Salvando...';

    const formData = new FormData();
    formData.append('nome', nome);
    if (senhaNova) formData.append('senha_nova', senhaNova);

    fetch('api/auth.php?acao=atualizar_perfil', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            msgSucesso.textContent = data.mensagem;
            msgSucesso.classList.add('ativo');
            setTimeout(() => {
                window.location.reload(); // Recarregar para atualizar o nome no header
            }, 1000);
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

function configurarLogout() {
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', function() {
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
}
