<?php
header('Content-Type: application/json');
require_once '../config.php';

$acao = $_GET['acao'] ?? '';

if ($acao === 'cadastro') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $email = sanitizar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos']);
        exit();
    }

    if ($senha !== $senha_confirm) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'As senhas não conferem']);
        exit();
    }

    if (strlen($senha) < 6) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter no mínimo 6 caracteres']);
        exit();
    }

    // Verificar se email já existe
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este email já está cadastrado']);
        exit();
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir usuário
    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar usuário']);
    }
    $stmt->close();
}

elseif ($acao === 'login') {
    $email = sanitizar($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Email e senha são obrigatórios']);
        exit();
    }

    // Buscar usuário
    $stmt = $conexao->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ? AND ativo = TRUE");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Email ou senha incorretos']);
        exit();
    }

    $usuario = $result->fetch_assoc();

    // Verificar senha
    if (!password_verify($senha, $usuario['senha'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Email ou senha incorretos']);
        exit();
    }

    // Definir sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    echo json_encode(['sucesso' => true, 'mensagem' => 'Login realizado com sucesso!', 'redirect' => 'dashboard.php']);
    $stmt->close();
}

elseif ($acao === 'atualizar_perfil') {
    verificarLogin();
    $nome = sanitizar($_POST['nome'] ?? '');
    $senha_nova = $_POST['senha_nova'] ?? '';
    $usuario_id = $_SESSION['usuario_id'];

    if (empty($nome)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'O nome não pode estar vazio']);
        exit();
    }

    if (!empty($senha_nova)) {
        if (strlen($senha_nova) < 6) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'A nova senha deve ter no mínimo 6 caracteres']);
            exit();
        }
        $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
        $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, senha = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nome, $senha_hash, $usuario_id);
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $usuario_id);
    }

    if ($stmt->execute()) {
        $_SESSION['usuario_nome'] = $nome;
        echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar perfil']);
    }
    $stmt->close();
}

elseif ($acao === 'logout') {
    session_destroy();
    echo json_encode(['sucesso' => true, 'mensagem' => 'Logout realizado']);
}

else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
}
?>
