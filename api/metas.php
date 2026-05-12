<?php
header('Content-Type: application/json');
require_once '../config.php';
verificarLogin();

$acao = $_GET['acao'] ?? '';
$usuario_id = $_SESSION['usuario_id'];

if ($acao === 'listar') {
    $stmt = $conexao->prepare("SELECT id, titulo, descricao, categoria, data_inicio, data_termino, progresso, status FROM metas WHERE usuario_id = ? ORDER BY data_termino ASC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $metas = [];

    $hoje = new DateTime('today');

    while ($row = $result->fetch_assoc()) {
        // Calcular progresso temporal
        if (!empty($row['data_inicio']) && !empty($row['data_termino'])) {
            $inicio  = new DateTime($row['data_inicio']);
            $termino = new DateTime($row['data_termino']);

            if ($hoje >= $termino) {
                $progressoTemporal = 100;
            } elseif ($hoje < $inicio) {
                $progressoTemporal = 0;
            } else {
                $totalDias   = $inicio->diff($termino)->days + 1;
                $diasPassados = $inicio->diff($hoje)->days + 1;
                $progressoTemporal = (int) round(($diasPassados / $totalDias) * 100);
                if ($progressoTemporal > 100) $progressoTemporal = 100;
            }


            // Sincronizar status com o progresso temporal (cancelada nunca é alterada)
            if ($row['status'] !== 'cancelada') {
                if ($progressoTemporal >= 100 && $row['status'] !== 'concluida') {
                    $novoStatus = 'concluida';
                } elseif ($progressoTemporal < 100 && $row['status'] !== 'em_progresso') {
                    $novoStatus = 'em_progresso';
                } else {
                    $novoStatus = null; // sem mudança
                }

                if ($novoStatus !== null) {
                    $stmtUpd = $conexao->prepare("UPDATE metas SET status = ? WHERE id = ? AND usuario_id = ?");
                    $stmtUpd->bind_param("sii", $novoStatus, $row['id'], $usuario_id);
                    $stmtUpd->execute();
                    $stmtUpd->close();
                    $row['status'] = $novoStatus;
                }
            }

        }

        $metas[] = $row;
    }

    echo json_encode(['sucesso' => true, 'dados' => $metas]);
    $stmt->close();
}

elseif ($acao === 'adicionar') {
    $titulo = sanitizar($_POST['titulo'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $categoria = sanitizar($_POST['categoria'] ?? '');
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_termino = $_POST['data_termino'] ?? '';

    if (empty($titulo) || empty($data_inicio) || empty($data_termino)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha os campos obrigatórios']);
        exit();
    }

    $stmt = $conexao->prepare("INSERT INTO metas (usuario_id, titulo, descricao, categoria, data_inicio, data_termino) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $usuario_id, $titulo, $descricao, $categoria, $data_inicio, $data_termino);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Meta adicionada com sucesso!', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao adicionar meta']);
    }
    $stmt->close();
}

elseif ($acao === 'editar') {
    $id = (int)$_POST['id'] ?? 0;
    $titulo = sanitizar($_POST['titulo'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $categoria = sanitizar($_POST['categoria'] ?? '');
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_termino = $_POST['data_termino'] ?? '';

    if (empty($titulo) || empty($data_inicio) || empty($data_termino)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha os campos obrigatórios']);
        exit();
    }

    $stmt = $conexao->prepare("UPDATE metas SET titulo = ?, descricao = ?, categoria = ?, data_inicio = ?, data_termino = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("sssssii", $titulo, $descricao, $categoria, $data_inicio, $data_termino, $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Meta atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar meta']);
    }
    $stmt->close();
}

elseif ($acao === 'atualizar') {
    $id = (int)$_POST['id'] ?? 0;
    $progresso = (int)$_POST['progresso'] ?? 0;
    $status = sanitizar($_POST['status'] ?? '');

    if ($progresso > 100) $progresso = 100;
    if ($progresso < 0) $progresso = 0;

    $stmt = $conexao->prepare("UPDATE metas SET progresso = ?, status = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("isii", $progresso, $status, $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Meta atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar meta']);
    }
    $stmt->close();
}

elseif ($acao === 'deletar') {
    $id = (int)$_POST['id'] ?? 0;

    $stmt = $conexao->prepare("DELETE FROM metas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Meta deletada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao deletar meta']);
    }
    $stmt->close();
}

else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
}
?>