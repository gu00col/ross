<?php

namespace App\Controllers;

use MF\Controller\Action;
use App\EnvLoader;

class DeleteController extends Action
{
    public function process()
    {
        $this->validaAutenticacao();

        // Validação dos parâmetros recebidos
        if (!isset($_POST['contrato_id']) || !isset($_POST['user_id'])) {
            $redirectBaseUrl = strtok($_SERVER['HTTP_REFERER'] ?? '/home', '?');
            header('Location: ' . $redirectBaseUrl . '?delete=error_params');
            exit;
        }

        $contratoId = trim((string)$_POST['contrato_id']);
        $userId = trim((string)$_POST['user_id']);

        if ($contratoId === '' || $userId === '') {
            $redirectBaseUrl = strtok($_SERVER['HTTP_REFERER'] ?? '/home', '?');
            header('Location: ' . $redirectBaseUrl . '?delete=error_params');
            exit;
        }

        // Monta URL e headers do N8N
        $n8nUrl = EnvLoader::get('N8N_URL') . '/' . EnvLoader::get('N8N_WEBHOOK_DELETAR_CONTRATO');
        $webhookKey = EnvLoader::get('N8N_API_KEY');

        // Prepara payload multipart/form-data
        $postData = [
            'contrato_id' => $contratoId,
            'user_id' => $userId,
        ];

        // Executa requisição cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $n8nUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'contratos-key: ' . $webhookKey,
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $redirectBaseUrl = strtok($_SERVER['HTTP_REFERER'] ?? '/home', '?');

        if ($httpcode >= 200 && $httpcode < 300) {
            header('Location: ' . $redirectBaseUrl . '?delete=success');
        } else {
            $logDetails = "Erro na API n8n (delete). Código: {$httpcode}. Resposta: {$response}";
            logMessage($logDetails);
            header('Location: ' . $redirectBaseUrl . '?delete=error_api');
        }
        exit;
    }

    public function validaAutenticacao()
    {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}


