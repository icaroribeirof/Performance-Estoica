<?php
header('Content-Type: application/json');
require_once '../config.php';
verificarLogin();

$acao = $_GET['acao'] ?? '';
$usuario_id = $_SESSION['usuario_id'];

if ($acao === 'listar_fichas') {
    $stmt = $conexao->prepare("SELECT id, nome, descricao, dias_semana, ativa FROM fichas_treino WHERE usuario_id = ? ORDER BY nome ASC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fichas = [];

    while ($row = $result->fetch_assoc()) {
        $fichas[] = $row;
    }

    echo json_encode(['sucesso' => true, 'dados' => $fichas]);
    $stmt->close();
}

elseif ($acao === 'criar_ficha') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $dias_semana = sanitizar($_POST['dias_semana'] ?? '');

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nome da ficha é obrigatório']);
        exit();
    }

    $stmt = $conexao->prepare("INSERT INTO fichas_treino (usuario_id, nome, descricao, dias_semana) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $nome, $descricao, $dias_semana);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha criada com sucesso!', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao criar ficha']);
    }
    $stmt->close();
}

elseif ($acao === 'editar_ficha') {
    $id = (int)$_POST['id'] ?? 0;
    $nome = sanitizar($_POST['nome'] ?? '');
    $descricao = sanitizar($_POST['descricao'] ?? '');
    $dias_semana = sanitizar($_POST['dias_semana'] ?? '');

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nome da ficha é obrigatório']);
        exit();
    }

    $stmt = $conexao->prepare("UPDATE fichas_treino SET nome = ?, descricao = ?, dias_semana = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("sssii", $nome, $descricao, $dias_semana, $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar ficha']);
    }
    $stmt->close();
}

elseif ($acao === 'listar_exercicios') {
    $ficha_id = (int)$_GET['ficha_id'] ?? 0;

    $stmt = $conexao->prepare("SELECT id, nome, series, repeticoes, peso, descanso, notas FROM exercicios WHERE ficha_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $ficha_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exercicios = [];

    while ($row = $result->fetch_assoc()) {
        $exercicios[] = $row;
    }

    echo json_encode(['sucesso' => true, 'dados' => $exercicios]);
    $stmt->close();
}

elseif ($acao === 'adicionar_exercicio') {
    $ficha_id = (int)$_POST['ficha_id'] ?? 0;
    $nome = sanitizar($_POST['nome'] ?? '');
    $series = (int)$_POST['series'] ?? 0;
    $repeticoes = (int)$_POST['repeticoes'] ?? 0;
    $peso = sanitizar($_POST['peso'] ?? '');
    $descanso = (int)$_POST['descanso'] ?? 0;
    $notas = sanitizar($_POST['notas'] ?? '');

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nome do exercício é obrigatório']);
        exit();
    }

    $stmt = $conexao->prepare("INSERT INTO exercicios (ficha_id, nome, series, repeticoes, peso, descanso, notas) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiiiss", $ficha_id, $nome, $series, $repeticoes, $peso, $descanso, $notas);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Exercício adicionado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao adicionar exercício']);
    }
    $stmt->close();
}

elseif ($acao === 'registrar_treino') {
    $ficha_id        = (int)($_POST['ficha_id'] ?? 0);
    $data_treino     = $_POST['data_treino'] ?? date('Y-m-d');
    $duracao_minutos = (int)($_POST['duracao_minutos'] ?? 0);
    $intensidade     = sanitizar($_POST['intensidade'] ?? 'moderada');
    $notas           = sanitizar($_POST['notas'] ?? '');

    $stmt = $conexao->prepare("INSERT INTO registros_treino (usuario_id, ficha_id, data_treino, duracao_minutos, intensidade, notas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $usuario_id, $ficha_id, $data_treino, $duracao_minutos, $intensidade, $notas);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Treino registrado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar treino']);
    }
    $stmt->close();
}

elseif ($acao === 'listar_registros') {
    $mes = (int)($_GET['mes'] ?? date('m'));
    $ano = (int)($_GET['ano'] ?? date('Y'));

    $stmt = $conexao->prepare("SELECT rt.id, rt.data_treino, rt.duracao_minutos, rt.intensidade, ft.nome FROM registros_treino rt JOIN fichas_treino ft ON rt.ficha_id = ft.id WHERE rt.usuario_id = ? AND MONTH(rt.data_treino) = ? AND YEAR(rt.data_treino) = ? ORDER BY rt.data_treino DESC");
    $stmt->bind_param("iii", $usuario_id, $mes, $ano);
    $stmt->execute();
    $result = $stmt->get_result();
    $registros = [];

    while ($row = $result->fetch_assoc()) {
        $registros[] = $row;
    }

    echo json_encode(['sucesso' => true, 'dados' => $registros]);
    $stmt->close();
}

elseif ($acao === 'estatisticas') {
    $mes = (int)($_GET['mes'] ?? date('m'));
    $ano = (int)($_GET['ano'] ?? date('Y'));

    $stmt = $conexao->prepare("SELECT COUNT(*) as total_treinos, SUM(duracao_minutos) as tempo_total FROM registros_treino WHERE usuario_id = ? AND MONTH(data_treino) = ? AND YEAR(data_treino) = ?");
    $stmt->bind_param("iii", $usuario_id, $mes, $ano);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    echo json_encode(['sucesso' => true, 'dados' => $stats]);
    $stmt->close();
}

elseif ($acao === 'editar_exercicio') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = sanitizar($_POST['nome'] ?? '');
    $series = (int)($_POST['series'] ?? 0);
    $repeticoes = (int)($_POST['repeticoes'] ?? 0);
    $peso = sanitizar($_POST['peso'] ?? '');
    $descanso = (int)($_POST['descanso'] ?? 0);
    $notas = sanitizar($_POST['notas'] ?? '');

    if (empty($nome) || $series <= 0 || $repeticoes <= 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha nome, séries e repetições corretamente']);
        exit();
    }

    $stmt = $conexao->prepare(
        "UPDATE exercicios e 
         JOIN fichas_treino ft ON e.ficha_id = ft.id 
         SET e.nome = ?, e.series = ?, e.repeticoes = ?, e.peso = ?, e.descanso = ?, e.notas = ? 
         WHERE e.id = ? AND ft.usuario_id = ?"
    );
    $stmt->bind_param("siisssii", $nome, $series, $repeticoes, $peso, $descanso, $notas, $id, $usuario_id);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Exercício atualizado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar exercício']);
    }
    $stmt->close();
}

elseif ($acao === 'deletar_exercicio') {
    $id = (int)($_POST['id'] ?? 0);

    // Garante que o exercício pertence a uma ficha do usuário
    $stmt = $conexao->prepare(
        "DELETE e FROM exercicios e
         JOIN fichas_treino ft ON e.ficha_id = ft.id
         WHERE e.id = ? AND ft.usuario_id = ?"
    );
    $stmt->bind_param("ii", $id, $usuario_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Exercício removido']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao remover exercício']);
    }
    $stmt->close();
}

elseif ($acao === 'deletar_ficha') {
    $id = (int)($_POST['id'] ?? 0);

    // Remove exercícios da ficha primeiro
    $stmtEx = $conexao->prepare("DELETE FROM exercicios WHERE ficha_id = ?");
    $stmtEx->bind_param("i", $id);
    $stmtEx->execute();
    $stmtEx->close();

    $stmt = $conexao->prepare("DELETE FROM fichas_treino WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha deletada']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao deletar ficha']);
    }
    $stmt->close();
}

else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
}
?>
