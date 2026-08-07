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
    <meta name="description" content="Crie sua conta no Performance Estoica e comece a transformar sua vida.">
    <!-- Nome ao salvar na Tela de Início -->
    <meta name="apple-mobile-web-app-title" content="P. Estoica">
    <!-- Ícone da Tela de Início (iOS) -->
    <link rel="apple-touch-icon" sizes="180x180" href="icon/favicon.png">
    <title>Cadastro — Performance Estoica</title>
    <link rel="icon" type="image/svg+xml" href="icon/favicon.svg">
    <link rel="stylesheet" href="css/geral.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container-auth">
        <div class="auth-box">
            <div class="auth-header">
                <div class="auth-logo">
                    <div class="auth-logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    </div>
                    <span class="auth-logo-text">Performance Estoica</span>
                </div>
                <h1>Crie sua conta</h1>
                <p>Comece sua jornada rumo à excelência.</p>
            </div>

            <form id="formCadastro" class="auth-form">
                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" required placeholder="Seu nome completo" autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="seu@email.com" autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                    <div id="passwordStrength" class="password-strength" style="display:none;">
                        <div class="strength-bar"><div class="strength-fill"></div></div>
                        <span class="strength-label"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senhaConfirm">Confirmar senha</label>
                    <input type="password" id="senhaConfirm" name="senhaConfirm" required placeholder="Repita sua senha" autocomplete="new-password">
                </div>

                <button type="submit" id="btnCadastro" class="btn btn-primary btn-full">Criar conta</button>

                <div id="mensagemErro" class="mensagem-erro"></div>
                <div id="mensagemSucesso" class="mensagem-sucesso"></div>
            </form>

            <div class="auth-footer">
                <p>Já possui conta? <a href="login.php">Fazer login</a></p>
            </div>
        </div>

        <div class="auth-image">
            <div class="image-content">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </div>
                <h2>Comece Agora</h2>
                <p>Junte-se a milhares de pessoas que estão transformando suas vidas com disciplina e consistência.</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="feature-item-text">
                            <h4>100% Gratuito</h4>
                            <p>Acesso completo a todas as funcionalidades</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <div class="feature-item-text">
                            <h4>Dados Seguros</h4>
                            <p>Suas informações protegidas e privadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/cadastro.js"></script>
</body>
</html>
