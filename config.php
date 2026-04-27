<?php
// Configuração do Banco de Dados
date_default_timezone_set('America/Sao_Paulo');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'performance_estoica');

// Criar conexão
$conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Definir charset
$conexao->set_charset("utf8mb4");

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para sanitizar input
function sanitizar($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Função para verificar login
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>
