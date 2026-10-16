<?php

class WhatsAppNotificationService
{
    private $apiUrl;
    private $apiToken;

    public function __construct()
    {
        // Pega as variáveis de ambiente carregadas no config.php
        global $env;
        
        $this->apiUrl   = $env['WA_API_URL'] ?? '';
        $this->apiToken = $env['WA_API_TOKEN'] ?? '';
    }

    public $lastHttpCode = 0;
    public $lastResponse = '';
    public $lastError = '';

    /**
     * Envia uma mensagem via WhatsApp
     *
     * @param string $telefone
     * @param string $mensagem
     * @return bool
     */
    public function sendMessage(string $telefone, string $mensagem): bool
    {
        // Remove caracteres não numéricos do telefone
        $telefoneLimpo = preg_replace('/[^0-9]/', '', $telefone);

        if (empty($telefoneLimpo) || empty($this->apiUrl)) {
            if (empty($this->apiUrl)) {
                $this->lastError = "WhatsApp API não configurada (WA_API_URL vazia no .env)";
                error_log($this->lastError . ". Mensagem simulada para: " . $telefoneLimpo);
                return true; 
            }
            $this->lastError = "Telefone vazio ou inválido";
            return false;
        }

        // Se o número tiver 10 ou 11 dígitos (DDD + número no Brasil), adiciona o DDI 55
        if (strlen($telefoneLimpo) === 10 || strlen($telefoneLimpo) === 11) {
            $telefoneLimpo = '55' . $telefoneLimpo;
        }

        // Payload compatível com todas as versões da Evolution API v2
        $payload = [
            'number'      => $telefoneLimpo,
            'text'        => $mensagem,
            'textMessage' => [
                'text' => $mensagem
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        // Desativa verificação SSL estrita para compatibilidade com ambiente local Windows/WAMP
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $this->apiToken,
            'Authorization: Bearer ' . $this->apiToken
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $this->lastHttpCode = $httpCode;
        $this->lastResponse = $response ?: '';
        $this->lastError    = $curlError ?: '';

        // Evolution API retorna 200 ou 201 no envio com sucesso
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log("Falha ao enviar WhatsApp para $telefoneLimpo. HTTP Code: $httpCode. Erro cURL: $curlError. Resposta: $response");
        return false;
    }
}
