<?php
header('Content-Type: application/json');
require_once '../config.php';
verificarLogin();

$acao       = $_GET['acao'] ?? '';
$usuario_id = (int)$_SESSION['usuario_id'];

// ─── LISTAR ───────────────────────────────────────────────────────────────────
if ($acao === 'listar') {
    $filtro = $_GET['filtro'] ?? 'todas';
    $hoje   = date('Y-m-d');

    $where  = "WHERE t.usuario_id = ?";
    $params = [$usuario_id];
    $types  = "i";

    switch ($filtro) {
        case 'hoje':
            $where  .= " AND DATE(t.data_atual) = ?";
            $params[] = $hoje;
            $types   .= "s";
            break;
        case 'agendadas':
            $where  .= " AND DATE(t.data_atual) > ? AND t.concluida = 0";
            $params[] = $hoje;
            $types   .= "s";
            break;
        case 'concluidas':
            $where  .= " AND t.concluida = 1";
            break;
        default:
            $where  .= " AND t.concluida = 0";
            break;
    }

    $orderDirection = ($filtro === 'concluidas') ? 'DESC' : 'ASC';

    $sql  = "SELECT * FROM tarefas t $where ORDER BY
                CASE WHEN t.data_atual IS NULL THEN 1 ELSE 0 END,
                t.data_atual $orderDirection,
                CASE t.prioridade WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $tarefas = [];
    while ($row = $result->fetch_assoc()) {
        $tarefas[] = $row;
    }
    $stmt->close();

    echo json_encode(['sucesso' => true, 'dados' => $tarefas]);
    exit;
}

// ─── ADICIONAR ────────────────────────────────────────────────────────────────
if ($acao === 'adicionar') {
    $titulo            = trim($_POST['titulo'] ?? '');
    $descricao         = trim($_POST['descricao'] ?? '');
    $local             = trim($_POST['local'] ?? '');
    $prioridade        = $_POST['prioridade'] ?? 'media';
    $data_atual        = $_POST['data_atual'] ?? '';
    $data_venc         = $_POST['data_vencimento'] ?? '';
    $recorrencia       = $_POST['recorrencia'] ?? 'nenhuma';
    $notificacao_ativa = (!empty($_POST['notificacao_ativa']) && in_array((string)$_POST['notificacao_ativa'], ['1', 'true', 'sim'], true)) ? 1 : 0;
    
    $opcoes_tempo      = ['na_hora', '15m', '30m', '1h', '1d'];
    $tempo_notificacao = null;
    if ($notificacao_ativa) {
        $tempo_recebido    = $_POST['tempo_notificacao'] ?? 'na_hora';
        $tempo_notificacao = in_array($tempo_recebido, $opcoes_tempo, true) ? $tempo_recebido : 'na_hora';
    }

    if (!$titulo) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Título é obrigatório']);
        exit;
    }

    $data_atual = $data_atual ? date('Y-m-d H:i:s', strtotime($data_atual)) : null;
    $data_venc  = $data_venc  ? date('Y-m-d H:i:s', strtotime($data_venc)) : null;

    // Sem recorrência: data_vencimento = data_atual
    if ($recorrencia === 'nenhuma') {
        $data_venc = $data_atual;
    }

    $local_val = $local !== '' ? $local : null;

    $sql  = "INSERT INTO tarefas (usuario_id, titulo, descricao, local, prioridade, data_atual, data_vencimento, recorrencia, notificacao_ativa, tempo_notificacao)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isssssssis", $usuario_id, $titulo, $descricao, $local_val, $prioridade, $data_atual, $data_venc, $recorrencia, $notificacao_ativa, $tempo_notificacao);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar tarefa']);
    }
    $stmt->close();
    exit;
}

// ─── EDITAR ───────────────────────────────────────────────────────────────────
if ($acao === 'editar') {
    $id                 = (int)($_POST['id'] ?? 0);
    $titulo             = trim($_POST['titulo'] ?? '');
    $descricao          = trim($_POST['descricao'] ?? '');
    $local              = trim($_POST['local'] ?? '');
    $prioridade         = $_POST['prioridade'] ?? 'media';
    $data_atual         = $_POST['data_atual'] ?? '';
    $data_venc          = $_POST['data_vencimento'] ?? '';
    $recorrencia        = $_POST['recorrencia'] ?? 'nenhuma';
    $notificacao_ativa  = (!empty($_POST['notificacao_ativa']) && in_array((string)$_POST['notificacao_ativa'], ['1', 'true', 'sim'], true)) ? 1 : 0;
    $escopo_recorrencia = $_POST['escopo_recorrencia'] ?? 'este_e_futuros';

    $opcoes_tempo      = ['na_hora', '15m', '30m', '1h', '1d'];
    $tempo_notificacao = null;
    if ($notificacao_ativa) {
        $tempo_recebido    = $_POST['tempo_notificacao'] ?? 'na_hora';
        $tempo_notificacao = in_array($tempo_recebido, $opcoes_tempo, true) ? $tempo_recebido : 'na_hora';
    }

    if (!$titulo || !$id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
        exit;
    }

    $data_atual = $data_atual ? date('Y-m-d H:i:s', strtotime($data_atual)) : null;
    $data_venc  = $data_venc  ? date('Y-m-d H:i:s', strtotime($data_venc)) : null;

    if ($recorrencia === 'nenhuma') {
        $data_venc = $data_atual;
    }

    $local_val = $local !== '' ? $local : null;

    // Obter dados atuais da tarefa
    $stmt = $conexao->prepare("SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $tarefa_original = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$tarefa_original) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada']);
        exit;
    }

    // Se o usuário escolheu alterar SOMENTE ESTA tarefa de uma série recorrente:
    if ($escopo_recorrencia === 'somente_este' && $tarefa_original['recorrencia'] !== 'nenhuma') {
        // 1. Atualiza a tarefa atual como pontual (sem recorrência)
        $sql  = "UPDATE tarefas
                 SET titulo = ?, descricao = ?, local = ?, prioridade = ?, data_atual = ?, data_vencimento = ?, recorrencia = 'nenhuma',
                     notificacao_ativa = ?, tempo_notificacao = ?, notificacao_enviada = 0, concluida = 0, data_conclusao = NULL
                 WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssssisii", $titulo, $descricao, $local_val, $prioridade, $data_atual, $data_atual, $notificacao_ativa, $tempo_notificacao, $id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        // 2. Continua a série original a partir da próxima data para as futuras
        if ($tarefa_original['data_atual']) {
            $proxima = calcularProximaData($tarefa_original['data_atual'], $tarefa_original['recorrencia']);
            $dentro_do_prazo = $tarefa_original['data_vencimento'] ? ($proxima <= $tarefa_original['data_vencimento']) : true;

            if ($proxima && $dentro_do_prazo) {
                $stmt = $conexao->prepare(
                    "INSERT INTO tarefas (usuario_id, titulo, descricao, local, prioridade, data_atual, data_vencimento, recorrencia, notificacao_ativa, tempo_notificacao, notificacao_enviada, concluida)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)"
                );
                $stmt->bind_param(
                    "isssssssis",
                    $usuario_id,
                    $tarefa_original['titulo'],
                    $tarefa_original['descricao'],
                    $tarefa_original['local'],
                    $tarefa_original['prioridade'],
                    $proxima,
                    $tarefa_original['data_vencimento'],
                    $tarefa_original['recorrencia'],
                    $tarefa_original['notificacao_ativa'],
                    $tarefa_original['tempo_notificacao']
                );
                $stmt->execute();
                $stmt->close();
            }
        }

        echo json_encode(['sucesso' => true]);
        exit;
    }

    // Escopo padrão: "este_e_futuros" (Esta e todas as tarefas futuras)
    $sql  = "UPDATE tarefas
             SET titulo = ?, descricao = ?, local = ?, prioridade = ?, data_atual = ?, data_vencimento = ?, recorrencia = ?,
                 notificacao_ativa = ?, tempo_notificacao = ?, notificacao_enviada = 0, concluida = 0, data_conclusao = NULL
             WHERE id = ? AND usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssssssisii", $titulo, $descricao, $local_val, $prioridade, $data_atual, $data_venc, $recorrencia, $notificacao_ativa, $tempo_notificacao, $id, $usuario_id);
    $execOk = $stmt->execute();
    $stmt->close();

    if ($execOk) {
        // Se houver tarefas futuras geradas da mesma série no banco, atualiza as notificações e dados delas também
        if (!empty($tarefa_original['titulo']) && !empty($tarefa_original['data_atual'])) {
            $stmt_futuras = $conexao->prepare(
                "UPDATE tarefas
                 SET titulo = ?, descricao = ?, local = ?, prioridade = ?,
                     notificacao_ativa = ?, tempo_notificacao = ?, notificacao_enviada = 0,
                     data_vencimento = ?, recorrencia = ?
                 WHERE usuario_id = ? AND titulo = ? AND concluida = 0 AND data_atual > ?"
            );
            $stmt_futuras->bind_param(
                "ssssisssiss",
                $titulo, $descricao, $local_val, $prioridade,
                $notificacao_ativa, $tempo_notificacao,
                $data_venc, $recorrencia,
                $usuario_id, $tarefa_original['titulo'], $tarefa_original['data_atual']
            );
            $stmt_futuras->execute();
            $stmt_futuras->close();
        }

        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao editar tarefa']);
    }
    exit;
}

// ─── DELETAR ──────────────────────────────────────────────────────────────────
if ($acao === 'deletar') {
    $id   = (int)($_POST['id'] ?? 0);
    $sql  = "DELETE FROM tarefas WHERE id = ? AND usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao deletar']);
    }
    $stmt->close();
    exit;
}

// ─── CONCLUIR ─────────────────────────────────────────────────────────────────
if ($acao === 'concluir') {
    $id       = (int)($_POST['id'] ?? 0);
    $concluir = ($_POST['concluida'] ?? 'false') === 'true';

    if (!$id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido']);
        exit;
    }

    $stmt = $conexao->prepare("SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $tarefa = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$tarefa) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Tarefa não encontrada']);
        exit;
    }

    $stmt = $conexao->prepare(
        "UPDATE tarefas SET concluida = ?, data_conclusao = ? WHERE id = ? AND usuario_id = ?"
    );
    $concluida_val  = $concluir ? 1 : 0;
    $data_conclusao = $concluir ? date('Y-m-d H:i:s') : null;
    $stmt->bind_param("isii", $concluida_val, $data_conclusao, $id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    // Só replica se: está concluindo + tem recorrência + tem data_atual
    // + a próxima data ainda está dentro do limite (data_vencimento)
    if ($concluir && $tarefa['recorrencia'] !== 'nenhuma' && $tarefa['data_atual']) {

        $proxima = calcularProximaData($tarefa['data_atual'], $tarefa['recorrencia']);

        $dentro_do_prazo = $tarefa['data_vencimento']
            ? ($proxima <= $tarefa['data_vencimento'])
            : true;

        if ($proxima && $dentro_do_prazo) {
            $stmt = $conexao->prepare(
                "INSERT INTO tarefas (usuario_id, titulo, descricao, local, prioridade, data_atual, data_vencimento, recorrencia, notificacao_ativa, tempo_notificacao, notificacao_enviada, concluida)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)"
            );
            $stmt->bind_param(
                "isssssssis",
                $usuario_id,
                $tarefa['titulo'],
                $tarefa['descricao'],
                $tarefa['local'],
                $tarefa['prioridade'],
                $proxima,
                $tarefa['data_vencimento'],
                $tarefa['recorrencia'],
                $tarefa['notificacao_ativa'],
                $tarefa['tempo_notificacao']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    echo json_encode(['sucesso' => true]);
    exit;
}

// ─── HELPER ───────────────────────────────────────────────────────────────────
function calcularProximaData(string $data_atual, string $recorrencia): ?string {
    $base = new DateTime($data_atual);

    switch ($recorrencia) {
        case 'diaria':
            $base->modify('+1 day');
            break;
        case 'semanal':
            $base->modify('+7 days');
            break;
        case 'dias_semana':
            $base->modify('+1 day');
            $dow = (int)$base->format('N');
            if ($dow === 6) $base->modify('+2 days');
            if ($dow === 7) $base->modify('+1 day');
            break;
        case 'anual':
            $base->modify('+1 year');
            break;
        default:
            return null;
    }

    return $base->format('Y-m-d H:i:s');
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
?>