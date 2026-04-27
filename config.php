<?php
// Carrega as variáveis do arquivo .env
$env = parse_ini_file(__DIR__ . '/.env');

// Configurações da aplicação
date_default_timezone_set($env['APP_TIMEZONE']);

// Conexão com o Banco de Dados
$conexao = new mysqli(
    $env['DB_HOST'],
    $env['DB_USER'],
    $env['DB_PASS'],
    $env['DB_NAME']
);

if ($conexao->connect_error) {
    die('Erro na conexão: ' . $conexao->connect_error);
}

$conexao->set_charset('utf8mb4');

// Sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para sanitizar input
function sanitizar(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)));
}

// Função para verificar login
function verificarLogin(): void
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}
