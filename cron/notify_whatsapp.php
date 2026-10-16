<?php
/**
 * CRON JOB - Disparo de Notificações WhatsApp
 * Deve ser configurado para rodar a cada minuto (ex: * * * * * php /caminho/para/cron/notify_whatsapp.php)
 */

$dir = dirname(__DIR__);
require_once $dir . '/config.php';
require_once $dir . '/api/WhatsAppNotificationService.php';

// Segurança: Se for acessado pelo navegador (Web), exige um token secreto se configurado no .env
global $env;
$cronToken = $env['CRON_TOKEN'] ?? '';
$isCli = (php_sapi_name() === 'cli');

if (!$isCli && !empty($cronToken)) {
    $requestToken = $_GET['token'] ?? '';
    if ($requestToken !== $cronToken) {
        http_response_code(403);
        die("Acesso negado: token inválido ou ausente.");
    }
}

$isDebug = isset($_GET['debug']) || isset($_GET['teste']);
if ($isDebug && !$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}

$whatsappService = new WhatsAppNotificationService();

// MODO DE TESTE DIRETO: se passar ?teste=1&telefone=55...
if (isset($_GET['teste'])) {
    $telefoneTeste = $_GET['telefone'] ?? '';
    if (empty($telefoneTeste)) {
        // Tenta pegar o telefone do primeiro usuário se não informado
        $resUser = $conexao->query("SELECT telefone, nome FROM usuarios WHERE telefone IS NOT NULL AND telefone != '' LIMIT 1");
        if ($rowUser = $resUser->fetch_assoc()) {
            $telefoneTeste = $rowUser['telefone'];
            echo "[Teste] Usando telefone cadastrado do usuário {$rowUser['nome']}: $telefoneTeste\n";
        }
    }

    if (empty($telefoneTeste)) {
        die("ERRO: Nenhum telefone informado ou encontrado. Use: notify_whatsapp.php?teste=1&telefone=5511999999999\n");
    }

    $msgTeste = "Performance App 💪 (Mensagem de Teste)\n\nConexão com a Evolution API no Railway realizada com sucesso!\nHora do servidor: " . date('d/m/Y H:i:s');
    echo "[Teste] Enviando para: $telefoneTeste...\n";
    $ok = $whatsappService->sendMessage($telefoneTeste, $msgTeste);

    echo "Status: " . ($ok ? "SUCESSO (Mensagem enviada)" : "FALHA") . "\n";
    echo "HTTP Code: {$whatsappService->lastHttpCode}\n";
    if ($whatsappService->lastError) {
        echo "Erro: {$whatsappService->lastError}\n";
    }
    echo "Resposta da API: {$whatsappService->lastResponse}\n";
    exit;
}

// Busca tarefas pendentes de notificação
$sql = "SELECT t.*, u.telefone, u.nome as usuario_nome
        FROM tarefas t
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE t.notificacao_ativa = 1
          AND t.notificacao_enviada = 0
          AND t.concluida = 0
          AND t.data_atual IS NOT NULL
          AND t.tempo_notificacao IS NOT NULL
          AND u.telefone IS NOT NULL
          AND u.telefone != ''";

$result = $conexao->query($sql);

if (!$result) {
    die("Erro na query: " . $conexao->error);
}

$now = time();
$notificacoesEnviadas = 0;
$totalPendentes = $result->num_rows;

if ($isDebug) {
    echo "=== DIAGNÓSTICO DO CRON DE NOTIFICAÇÕES ===\n";
    echo "Hora atual no servidor: " . date('d/m/Y H:i:s', $now) . " (Timezone: " . date_default_timezone_get() . ")\n";
    echo "Tarefas candidatas encontradas no banco: $totalPendentes\n\n";
}

while ($tarefa = $result->fetch_assoc()) {
    $dataEvento = strtotime($tarefa['data_atual']);
    if (!$dataEvento) {
        if ($isDebug) echo "- Tarefa ID {$tarefa['id']}: data_atual inválida ({$tarefa['data_atual']})\n";
        continue;
    }

    $minutosAntecedencia = 0;
    switch ($tarefa['tempo_notificacao']) {
        case '15m':
            $minutosAntecedencia = 15;
            break;
        case '30m':
            $minutosAntecedencia = 30;
            break;
        case '1h':
            $minutosAntecedencia = 60;
            break;
        case '1d':
            $minutosAntecedencia = 1440; // 24 * 60
            break;
        case 'na_hora':
        default:
            $minutosAntecedencia = 0;
            break;
    }

    $horarioNotificacao = $dataEvento - ($minutosAntecedencia * 60);

    if ($isDebug) {
        echo "- Tarefa ID {$tarefa['id']}: '{$tarefa['titulo']}'\n";
        echo "  Usuário: {$tarefa['usuario_nome']} (Tel: {$tarefa['telefone']})\n";
        echo "  Horário do evento: " . date('d/m/Y H:i:s', $dataEvento) . "\n";
        echo "  Horário programado para notificar: " . date('d/m/Y H:i:s', $horarioNotificacao) . " (antecedência: {$tarefa['tempo_notificacao']})\n";
    }

    // Se o momento atual é maior ou igual ao horário programado da notificação
    if ($now >= $horarioNotificacao) {
        $local = !empty($tarefa['local']) ? $tarefa['local'] : "Não informado";
        $dataStr = date('d/m/Y', $dataEvento);
        $horaStr = date('H:i', $dataEvento);
        
        $mensagem = "Performance App 💪\n\n";
        $mensagem .= "Olá {$tarefa['usuario_nome']}, gostaríamos de lembrar que você tem uma tarefa a cumprir:\n\n";
        $mensagem .= "Local: $local\n";
        $mensagem .= "Quando: $dataStr às $horaStr\n";
        $mensagem .= "Descrição: {$tarefa['titulo']}";

        if ($isDebug) echo "  -> Disparando envio via WhatsApp...\n";

        // Envia WhatsApp
        $sucesso = $whatsappService->sendMessage($tarefa['telefone'], $mensagem);

        if ($sucesso) {
            // Atualiza no banco
            $stmtUpdate = $conexao->prepare("UPDATE tarefas SET notificacao_enviada = 1 WHERE id = ?");
            $stmtUpdate->bind_param("i", $tarefa['id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            
            $notificacoesEnviadas++;
            if ($isDebug) echo "  -> SUCESSO! Mensagem enviada e marcada como enviada no banco.\n\n";
        } else {
            if ($isDebug) {
                echo "  -> FALHA ao enviar! HTTP: {$whatsappService->lastHttpCode}\n";
                echo "  -> Erro cURL: {$whatsappService->lastError}\n";
                echo "  -> Resposta: {$whatsappService->lastResponse}\n\n";
            }
        }
    } else {
        if ($isDebug) {
            $faltamSegundos = $horarioNotificacao - $now;
            echo "  -> Ainda não atingiu o horário. Faltam " . ceil($faltamSegundos / 60) . " minuto(s).\n\n";
        }
    }
}

echo "Job finalizado. Notificações enviadas: $notificacoesEnviadas\n";
