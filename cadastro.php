<?php
require_once 'config.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container-auth">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Performance Estoica</h1>
                <p>Comece sua jornada agora</p>
            </div>

            <form id="formCadastro" class="auth-form">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required placeholder="Digite seu nome completo">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Digite seu e-mail">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="senhaConfirm">Confirmar Senha</label>
                    <input type="password" id="senhaConfirm" name="senhaConfirm" required placeholder="Confirme sua senha">
                </div>

                <button type="submit" class="btn btn-primary btn-full">Cadastrar</button>

                <div id="mensagemErro" class="mensagem-erro"></div>
                <div id="mensagemSucesso" class="mensagem-sucesso"></div>
            </form>

            <div class="auth-footer">
                <p>Já possui conta? <a href="login.php">Faça login aqui</a></p>
            </div>
        </div>

        <div class="auth-image">
            <div class="image-content">
                <div class="logo-large">🚀</div>
                <h2>Comece Agora</h2>
                <p>Junte-se a milhares de pessoas que estão transformando suas vidas</p>
            </div>
        </div>
    </div>

    <script src="js/cadastro.js"></script>
</body>
</html>
