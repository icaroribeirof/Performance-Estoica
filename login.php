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
    <title>Login - Performance Estoica</title>
    <link rel="icon" type="image/x-icon" href="icon/icon.png">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container-auth">
        <div class="auth-box">
            <div class="auth-header">
                <h1>Performance Estoica</h1>
                <p>Seu caminho para a excelência</p>
            </div>

            <form id="formLogin" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Digite seu e-mail">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="Digite sua senha">
                </div>

                <button type="submit" class="btn btn-primary btn-full">Entrar</button>

                <div id="mensagemErro" class="mensagem-erro"></div>
            </form>

            <div class="auth-footer">
                <p>Não possui conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
            </div>
        </div>

        <div class="auth-image">
            <div class="image-content">
                <div class="logo-large">💪</div>
                <h2>Transforme Sua Vida</h2>
                <p>Rastreie seus objetivos, acompanhe seu progresso e alcance a excelência</p>
            </div>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
