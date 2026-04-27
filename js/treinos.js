// Treinos JavaScript

const ORDEM_DIAS = {
    'Segunda-feira': 1,
    'Terça-feira':   2,
    'Quarta-feira':  3,
    'Quinta-feira':  4,
    'Sexta-feira':   5,
    'Sábado':        6,
    'Domingo':       7
};

let fichasCarregadas = [];

document.addEventListener('DOMContentLoaded', function() {
    carregarFichas();
    configurarModals();
    configurarTabs();
    configurarLogout();
    preencherMeses();      // popula o select primeiro
    carregarRegistros();   // só então lê o mês selecionado
});

function carregarFichas() {
    fetch('api/treinos.php?acao=listar_fichas')
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                // Ordenar por dia da semana (Segunda → Domingo)
                const fichasOrdenadas = [...data.dados].sort((a, b) => {
                    const ordemA = ORDEM_DIAS[a.dias_semana] ?? 99;
                    const ordemB = ORDEM_DIAS[b.dias_semana] ?? 99;
                    return ordemA - ordemB;
                });
                fichasCarregadas = fichasOrdenadas;
                exibirFichas(fichasOrdenadas);
                preencherSelectFichas(fichasOrdenadas);
            }
        })
        .catch(error => console.error('Erro:', error));
}

function exibirFichas(fichas) {
    const container = document.getElementById('fichasContainer');
    container.innerHTML = '';

    if (fichas.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhuma ficha de treino criada. Crie uma para começar!</p>';
        return;
    }

    fichas.forEach(ficha => {
        const card = document.createElement('div');
        card.className = 'ficha-card';
        card.innerHTML = `
            <div class="ficha-header">
                <h3 class="ficha-titulo">${ficha.nome}</h3>
                <p class="ficha-dias">📅 ${ficha.dias_semana || 'Sem dias definidos'}</p>
            </div>
            <p class="ficha-descricao">${ficha.descricao || 'Sem descrição'}</p>
            <div class="exercicios-list">
                <h4>Exercícios</h4>
                <div id="exercicios-${ficha.id}" class="exercicios-content">
                    <p class="text-empty">Carregando...</p>
                </div>
            </div>
            <div class="ficha-actions">
                <button class="btn btn-primary btn-small" onclick="abrirModalAdicionarExercicio(${ficha.id})">Adicionar Exercício</button>
                <button class="btn btn-secondary btn-small" onclick="abrirModalEdicaoFicha(${ficha.id})">Editar</button>
                <button class="btn btn-danger btn-small" onclick="deletarFicha(${ficha.id})">Deletar</button>
            </div>
        `;
        container.appendChild(card);
        carregarExercicios(ficha.id);
    });
}

function carregarExercicios(fichaId) {
    fetch(`api/treinos.php?acao=listar_exercicios&ficha_id=${fichaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirExercicios(fichaId, data.dados);
            }
        });
}

function exibirExercicios(fichaId, exercicios) {
    const container = document.getElementById(`exercicios-${fichaId}`);
    container.innerHTML = '';

    if (exercicios.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhum exercício adicionado</p>';
        return;
    }

    exercicios.forEach(exercicio => {
        const item = document.createElement('div');
        item.className = 'exercicio-item';

        const detalhes = [
            `${exercicio.series}x${exercicio.repeticoes}`,
            exercicio.peso   ? `💪 ${exercicio.peso}`          : null,
            exercicio.descanso > 0 ? `⏱ ${exercicio.descanso}s` : null,
        ].filter(Boolean).join(' · ');

        item.innerHTML = `
            <div class="exercicio-info">
                <span class="exercicio-nome">${exercicio.nome}</span>
                <span class="exercicio-detalhes">${detalhes}</span>
                ${exercicio.notas ? `<span class="exercicio-notas">${exercicio.notas}</span>` : ''}
            </div>
            <div class="exercicio-actions">
                <button class="btn-exercicio-edit" title="Editar">✏️</button>
                <button class="btn-exercicio-del" onclick="deletarExercicio(${exercicio.id}, ${fichaId})" title="Remover">🗑️</button>
            </div>
        `;
        const btnEdit = item.querySelector('.btn-exercicio-edit');
        btnEdit.addEventListener('click', () => abrirModalEdicaoExercicio(exercicio, fichaId));
        container.appendChild(item);
    });
}

function preencherSelectFichas(fichas) {
    const select = document.getElementById('fichaSelect');
    select.innerHTML = '<option value="">Escolha uma ficha</option>';
    fichas.forEach(ficha => {
        const option = document.createElement('option');
        option.value = ficha.id;
        option.textContent = ficha.nome;
        select.appendChild(option);
    });
}

function preencherMeses() {
    const select = document.getElementById('mesSelecionado');
    const mesAtual = new Date().getMonth() + 1;
    const anoAtual = new Date().getFullYear();

    for (let i = 0; i < 12; i++) {
        let mes = mesAtual - i;
        let ano = anoAtual;
        
        if (mes <= 0) {
            mes += 12;
            ano -= 1;
        }

        const option = document.createElement('option');
        option.value = `${ano}-${String(mes).padStart(2, '0')}`;
        option.textContent = `${String(mes).padStart(2, '0')}/${ano}`;
        
        if (i === 0) option.selected = true;
        select.appendChild(option);
    }

    select.addEventListener('change', carregarRegistros);
}

function carregarRegistros() {
    const mesSelecionado = document.getElementById('mesSelecionado').value;
    if (!mesSelecionado) {
        document.getElementById('registrosContainer').innerHTML = '<p class="text-empty">Selecione um mês</p>';
        return;
    }

    const [ano, mes] = mesSelecionado.split('-');
    
    fetch(`api/treinos.php?acao=listar_registros&mes=${mes}&ano=${ano}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirRegistros(data.dados);
                carregarEstatisticas(mes, ano);
            }
        });
}

function exibirRegistros(registros) {
    const container = document.getElementById('registrosContainer');
    container.innerHTML = '';

    if (registros.length === 0) {
        container.innerHTML = '<p class="text-empty">Nenhum treino registrado neste período</p>';
        return;
    }

    registros.forEach(registro => {
        const item = document.createElement('div');
        item.className = 'registro-item';
        item.innerHTML = `
            <div class="registro-info">
                <div class="registro-data">${formatarData(registro.data_treino)}</div>
                <div class="registro-meta">
                    <span>${registro.nome}</span>
                    <span class="registro-intensidade ${registro.intensidade}">${registro.intensidade}</span>
                </div>
            </div>
            <div class="registro-duracao">${registro.duracao_minutos} min</div>
        `;
        container.appendChild(item);
    });
}

function carregarEstatisticas(mes, ano) {
    fetch(`api/treinos.php?acao=estatisticas&mes=${mes}&ano=${ano}`)
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                exibirEstatisticas(data.dados);
            }
        });
}

function exibirEstatisticas(stats) {
    const container = document.getElementById('estatisticas');
    const totalMinutos = stats.tempo_total || 0;
    const horas = Math.floor(totalMinutos / 60);
    const minutos = totalMinutos % 60;
    const tempoLabel = horas > 0
        ? (minutos > 0 ? `${horas}h ${minutos}min` : `${horas}h`)
        : `${minutos}min`;

    container.innerHTML = `
        <div class="stat-mini">
            <div class="stat-mini-label">Total de Treinos</div>
            <div class="stat-mini-value">${stats.total_treinos || 0}</div>
        </div>
        <div class="stat-mini">
            <div class="stat-mini-label">Tempo Total</div>
            <div class="stat-mini-value">${tempoLabel}</div>
        </div>
    `;
}

function configurarModals() {
    // Modal Nova Ficha
    const modalFicha = document.getElementById('modalNovaFichaOverlay');
    const btnFicha = document.getElementById('btnNovaFichaModal');
    const btnFecharFicha = modalFicha.querySelector('.modal-close');
    const formFicha = document.getElementById('formNovaFichaForm');

    btnFicha.addEventListener('click', () => modalFicha.classList.add('ativo'));
    btnFecharFicha.addEventListener('click', () => modalFicha.classList.remove('ativo'));
    
    modalFicha.addEventListener('click', (e) => {
        if (e.target === modalFicha) modalFicha.classList.remove('ativo');
    });

    formFicha.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarNovaFicha();
    });

    // Modal Registrar Treino
    const modalTreino = document.getElementById('modalRegistroTreinoOverlay');
    const btnTreino = document.getElementById('btnRegistroTreinoModal');
    const btnFecharTreino = modalTreino.querySelector('.modal-close');
    const formTreino = document.getElementById('formRegistroTreinoForm');

    btnTreino.addEventListener('click', () => modalTreino.classList.add('ativo'));
    btnFecharTreino.addEventListener('click', () => modalTreino.classList.remove('ativo'));
    
    modalTreino.addEventListener('click', (e) => {
        if (e.target === modalTreino) modalTreino.classList.remove('ativo');
    });

    formTreino.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarRegistroTreino();
    });

    // Data hoje como padrão (usando data local para evitar problema de fuso horário)
    const dataTreino = document.getElementById('dataTreino');
    if (dataTreino) {
        const hoje = new Date();
        dataTreino.value = `${hoje.getFullYear()}-${String(hoje.getMonth() + 1).padStart(2, '0')}-${String(hoje.getDate()).padStart(2, '0')}`;
    }

    // Modal Editar Ficha
    const modalEditFicha = document.getElementById('modalEditarFichaOverlay');
    const btnFecharEditFicha = modalEditFicha.querySelector('.modal-close');
    const formEditFicha = document.getElementById('formEditarFichaForm');

    btnFecharEditFicha.addEventListener('click', () => modalEditFicha.classList.remove('ativo'));
    modalEditFicha.addEventListener('click', (e) => {
        if (e.target === modalEditFicha) modalEditFicha.classList.remove('ativo');
    });

    formEditFicha.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarEdicaoFicha();
    });

    // Modal Adicionar Exercício
    const modalExercicio = document.getElementById('modalAdicionarExercicioOverlay');
    const btnFecharExercicio = modalExercicio.querySelector('.modal-close');
    const formExercicio = document.getElementById('formAdicionarExercicioForm');

    btnFecharExercicio.addEventListener('click', () => modalExercicio.classList.remove('ativo'));
    modalExercicio.addEventListener('click', (e) => {
        if (e.target === modalExercicio) modalExercicio.classList.remove('ativo');
    });

    formExercicio.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarExercicio();
    });

    // Modal Editar Exercício
    const modalEditExercicio = document.getElementById('modalEditarExercicioOverlay');
    const btnFecharEditExercicio = modalEditExercicio.querySelector('.modal-close');
    const formEditExercicio = document.getElementById('formEditarExercicioForm');

    btnFecharEditExercicio.addEventListener('click', () => modalEditExercicio.classList.remove('ativo'));
    modalEditExercicio.addEventListener('click', (e) => {
        if (e.target === modalEditExercicio) modalEditExercicio.classList.remove('ativo');
    });

    formEditExercicio.addEventListener('submit', function(e) {
        e.preventDefault();
        salvarEdicaoExercicio();
    });
}

function salvarNovaFicha() {
    const nome = document.getElementById('fichanome').value;
    const descricao = document.getElementById('fichadescricao').value;
    const dias = document.getElementById('fichadias').value;
    const mensagemErro = document.getElementById('mensagemErroModalFicha');

    if (!nome) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Nome da ficha é obrigatório';
        return;
    }

    const formData = new FormData();
    formData.append('nome', nome);
    formData.append('descricao', descricao);
    formData.append('dias_semana', dias);

    fetch('api/treinos.php?acao=criar_ficha', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalNovaFichaOverlay').classList.remove('ativo');
            document.getElementById('formNovaFichaForm').reset();
            carregarFichas();
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem;
        }
    });
}

function salvarRegistroTreino() {
    const fichaId = document.getElementById('fichaSelect').value;
    const dataTreino = document.getElementById('dataTreino').value;
    const duracao = document.getElementById('duracaoMinutos').value;
    const intensidade = document.getElementById('intensidade').value;
    const notas = document.getElementById('notasTreino').value;
    const mensagemErro = document.getElementById('mensagemErroModalTreino');

    if (!fichaId || !dataTreino || !duracao) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Preencha todos os campos obrigatórios';
        return;
    }

    const formData = new FormData();
    formData.append('ficha_id', fichaId);
    formData.append('data_treino', dataTreino);
    formData.append('duracao_minutos', duracao);
    formData.append('intensidade', intensidade);
    formData.append('notas', notas);

    fetch('api/treinos.php?acao=registrar_treino', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalRegistroTreinoOverlay').classList.remove('ativo');
            document.getElementById('formRegistroTreinoForm').reset();
            carregarRegistros();
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem;
        }
    });
}

function abrirModalEdicaoFicha(id) {
    const ficha = fichasCarregadas.find(f => f.id == id);
    if (!ficha) return;

    document.getElementById('edit_ficha_id').value = ficha.id;
    document.getElementById('edit_fichanome').value = ficha.nome;
    document.getElementById('edit_fichadescricao').value = ficha.descricao || '';
    document.getElementById('edit_fichadias').value = ficha.dias_semana || '';

    document.getElementById('modalEditarFichaOverlay').classList.add('ativo');
}

function salvarEdicaoFicha() {
    const id = document.getElementById('edit_ficha_id').value;
    const nome = document.getElementById('edit_fichanome').value;
    const descricao = document.getElementById('edit_fichadescricao').value;
    const dias = document.getElementById('edit_fichadias').value;
    const mensagemErro = document.getElementById('mensagemErroModalFichaEdit');

    if (!nome) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Nome da ficha é obrigatório';
        return;
    }

    const formData = new FormData();
    formData.append('id', id);
    formData.append('nome', nome);
    formData.append('descricao', descricao);
    formData.append('dias_semana', dias);

    fetch('api/treinos.php?acao=editar_ficha', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalEditarFichaOverlay').classList.remove('ativo');
            carregarFichas();
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem;
        }
    });
}

function abrirModalAdicionarExercicio(fichaId) {
    // Limpar form e erro
    document.getElementById('formAdicionarExercicioForm').reset();
    document.getElementById('mensagemErroModalExercicio').classList.remove('ativo');
    document.getElementById('mensagemErroModalExercicio').textContent = '';
    // Guardar a qual ficha pertence
    document.getElementById('exercicio_ficha_id').value = fichaId;
    document.getElementById('modalAdicionarExercicioOverlay').classList.add('ativo');
}

function salvarExercicio() {
    const fichaId   = document.getElementById('exercicio_ficha_id').value;
    const nome      = document.getElementById('exercicioNome').value.trim();
    const series    = document.getElementById('exercicioSeries').value;
    const reps      = document.getElementById('exercicioRepeticoes').value;
    const peso      = document.getElementById('exercicioPeso').value.trim();
    const descanso  = document.getElementById('exercicioDescanso').value;
    const notas     = document.getElementById('exercicioNotas').value.trim();
    const mensagemErro = document.getElementById('mensagemErroModalExercicio');

    if (!nome || !series || !reps) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Preencha os campos obrigatórios (nome, séries e repetições)';
        return;
    }

    const formData = new FormData();
    formData.append('ficha_id',    fichaId);
    formData.append('nome',        nome);
    formData.append('series',      series);
    formData.append('repeticoes',  reps);
    formData.append('peso',        peso);
    formData.append('descanso',    descanso || 0);
    formData.append('notas',       notas);

    fetch('api/treinos.php?acao=adicionar_exercicio', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalAdicionarExercicioOverlay').classList.remove('ativo');
            carregarExercicios(fichaId);
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem || 'Erro ao adicionar exercício';
        }
    })
    .catch(() => {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Erro de comunicação com o servidor';
    });
}

function abrirModalEdicaoExercicio(exercicio, fichaId) {
    document.getElementById('edit_exercicio_id').value = exercicio.id;
    document.getElementById('edit_exercicio_ficha_id').value = fichaId;
    document.getElementById('edit_exercicioNome').value = exercicio.nome;
    document.getElementById('edit_exercicioSeries').value = exercicio.series;
    document.getElementById('edit_exercicioRepeticoes').value = exercicio.repeticoes;
    document.getElementById('edit_exercicioPeso').value = exercicio.peso || '';
    document.getElementById('edit_exercicioDescanso').value = exercicio.descanso || '';
    document.getElementById('edit_exercicioNotas').value = exercicio.notas || '';

    document.getElementById('mensagemErroModalExercicioEdit').classList.remove('ativo');
    document.getElementById('mensagemErroModalExercicioEdit').textContent = '';

    document.getElementById('modalEditarExercicioOverlay').classList.add('ativo');
}

function salvarEdicaoExercicio() {
    const id        = document.getElementById('edit_exercicio_id').value;
    const fichaId   = document.getElementById('edit_exercicio_ficha_id').value;
    const nome      = document.getElementById('edit_exercicioNome').value.trim();
    const series    = document.getElementById('edit_exercicioSeries').value;
    const reps      = document.getElementById('edit_exercicioRepeticoes').value;
    const peso      = document.getElementById('edit_exercicioPeso').value.trim();
    const descanso  = document.getElementById('edit_exercicioDescanso').value;
    const notas     = document.getElementById('edit_exercicioNotas').value.trim();
    const mensagemErro = document.getElementById('mensagemErroModalExercicioEdit');

    if (!nome || !series || !reps) {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Preencha os campos obrigatórios (nome, séries e repetições)';
        return;
    }

    const formData = new FormData();
    formData.append('id',          id);
    formData.append('nome',        nome);
    formData.append('series',      series);
    formData.append('repeticoes',  reps);
    formData.append('peso',        peso);
    formData.append('descanso',    descanso || 0);
    formData.append('notas',       notas);

    fetch('api/treinos.php?acao=editar_exercicio', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            document.getElementById('modalEditarExercicioOverlay').classList.remove('ativo');
            carregarExercicios(fichaId);
        } else {
            mensagemErro.classList.add('ativo');
            mensagemErro.textContent = data.mensagem || 'Erro ao editar exercício';
        }
    })
    .catch(() => {
        mensagemErro.classList.add('ativo');
        mensagemErro.textContent = 'Erro de comunicação com o servidor';
    });
}

function deletarExercicio(exercicioId, fichaId) {
    confirmarAcao(
        'Remover Exercício',
        'Deseja remover este exercício da ficha? Esta ação não pode ser desfeita.',
        function() {
            const formData = new FormData();
            formData.append('id', exercicioId);

            fetch('api/treinos.php?acao=deletar_exercicio', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    carregarExercicios(fichaId);
                }
            });
        },
        '⚠️'
    );
}

function deletarFicha(fichaId) {
    confirmarAcao(
        'Deletar Ficha',
        'Deseja deletar esta ficha e todos os seus exercícios? Esta ação não pode ser desfeita.',
        function() {
            const formData = new FormData();
            formData.append('id', fichaId);

            fetch('api/treinos.php?acao=deletar_ficha', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    carregarFichas();
                }
            });
        }
    );
}

function configurarTabs() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('ativo'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('ativo'));
            
            this.classList.add('ativo');
            document.getElementById(`tab-${tabName}`).classList.add('ativo');
        });
    });
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
