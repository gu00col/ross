<?php

namespace App\Controllers;

use MF\Controller\Action;
use CURLFile;
use App\EnvLoader;

class UploadController extends Action
{
    public function process()
    {
        $this->validaAutenticacao();

        // 1. Validação básica do arquivo
        if (empty($_FILES['contractFile']) || $_FILES['contractFile']['error'] !== UPLOAD_ERR_OK) {
            // Adicionar redirecionamento com erro
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/home') . '?upload=error_file');
            exit;
        }

        $file = $_FILES['contractFile'];
        if ($file['type'] !== 'application/pdf') {
            // Adicionar redirecionamento com erro
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/home') . '?upload=error_type');
            exit;
        }

        // 2. Preparar dados para o cURL
        $n8nUrl = EnvLoader::get('N8N_URL') . '/' . EnvLoader::get('N8N_WEBHOOK_NOVO_CONTRATO');
        $webhookKey = EnvLoader::get('N8N_API_KEY'); // Chave para o header 'webhook-contratos'
        $userId = $_SESSION['id'];
        $filePath = $file['tmp_name'];
        $fileName = $file['name'];

        // Cria um objeto CURLFile para o upload
        $cfile = new CURLFile($filePath, 'application/pdf', $fileName);

        $postData = [
            'contrato' => $cfile,
            'user_id' => $userId,
        ];

        // Iniciar e executar a requisição cURL
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

        // 4. Tratar a resposta e redirecionar
        $redirectBaseUrl = strtok($_SERVER['HTTP_REFERER'] ?? '/home', '?');

        if ($httpcode >= 200 && $httpcode < 300) {
            header('Location: ' . $redirectBaseUrl . '?upload=success');
        } else {
            // Log detalhado do erro
            $logDetails = "Erro na API n8n. Código: {$httpcode}. Resposta: {$response}";
            logMessage($logDetails);
            
            header('Location: ' . $redirectBaseUrl . '?upload=error_api');
        }
        exit;
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}
