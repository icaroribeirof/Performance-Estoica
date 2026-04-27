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

    while ($row = $result->fetch_assoc()) {
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
