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
    <meta name="description" content="Performance Estoica — Rastreie seus objetivos, acompanhe seu progresso e alcance a excelência.">
    <title>Login — Performance Estoica</title>
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
                <h1>Bem-vindo de volta</h1>
                <p>Acesse sua conta para continuar sua jornada.</p>
            </div>

            <form id="formLogin" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="seu@email.com" autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="••••••••" autocomplete="current-password">
                </div>

                <button type="submit" id="btnLogin" class="btn btn-primary btn-full">Entrar</button>

                <div id="mensagemErro" class="mensagem-erro"></div>
            </form>

            <div class="auth-footer">
                <p>Não possui conta? <a href="cadastro.php">Crie uma agora</a></p>
            </div>
        </div>

        <div class="auth-image">
            <div class="image-content">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                </div>
                <h2>Alcance a Excelência</h2>
                <p>Rastreie metas, organize tarefas e acompanhe treinos em um único lugar.</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="feature-item-text">
                            <h4>Metas Inteligentes</h4>
                            <p>Defina e acompanhe seus objetivos</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="feature-item-text">
                            <h4>Tarefas Organizadas</h4>
                            <p>Gerencie sua rotina diária com eficiência</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-item-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                        </div>
                        <div class="feature-item-text">
                            <h4>Treinos Detalhados</h4>
                            <p>Registre fichas e acompanhe evolução</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
