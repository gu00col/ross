<?php

namespace App\Controllers\Api;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Services\AuthService;
use App\Services\FlashMessageService;

class ContractController
{
    private AuthService $authService;
    private FlashMessageService $flashService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->flashService = new FlashMessageService();
    }
    
    /**
     * Upload de contrato para N8N
     */
    public function upload(ServerRequestInterface $request): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new JsonResponse(['error' => 'Não autorizado'], 401);
        }
        
        try {
            // Obter dados do formulário
            $parsedBody = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();
            
            $userId = $parsedBody['user_id'] ?? null;
            $contractFile = $uploadedFiles['contrato'] ?? null;
            
            // Validar dados
            if (!$userId) {
                $this->flashService->error('ID do usuário é obrigatório');
                return new RedirectResponse('/contracts');
            }
            
            if (!$contractFile || $contractFile->getError() !== UPLOAD_ERR_OK) {
                $this->flashService->error('Arquivo de contrato é obrigatório');
                return new RedirectResponse('/contracts');
            }
            
            // Validar tipo de arquivo
            $allowedTypes = ['application/pdf'];
            $fileType = $contractFile->getClientMediaType();
            
            if (!in_array($fileType, $allowedTypes)) {
                $this->flashService->error('Apenas arquivos PDF são aceitos');
                return new RedirectResponse('/contracts');
            }
            
            // Validar tamanho do arquivo (10MB)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($contractFile->getSize() > $maxSize) {
                $this->flashService->error('Arquivo muito grande. Tamanho máximo: 10MB');
                return new RedirectResponse('/contracts');
            }
            
            // Enviar para N8N
            $result = $this->sendToN8N($contractFile, $userId);
            
            if ($result['success']) {
                $this->flashService->success('Contrato enviado para análise com sucesso! O arquivo está sendo processado.');
                return new RedirectResponse('/contracts');
            } else {
                $this->flashService->error('Erro ao enviar para N8N: ' . $result['error']);
                return new RedirectResponse('/contracts');
            }
            
        } catch (\Exception $e) {
            $this->flashService->error('Erro interno do servidor: ' . $e->getMessage());
            return new RedirectResponse('/contracts');
        }
    }
    
    /**
     * Enviar arquivo para N8N via cURL
     */
    private function sendToN8N($contractFile, string $userId): array
    {
        try {
            // Obter configurações do .env
            $n8nUrl = $_ENV['N8N_URL'] ?? '';
            $n8nWebhookId = $_ENV['N8N_WEBHOOK_ID'] ?? '';
            $n8nApiKey = $_ENV['N8N_API_KEY'] ?? '';
            
            if (empty($n8nUrl) || empty($n8nWebhookId) || empty($n8nApiKey)) {
                return [
                    'success' => false,
                    'error' => 'Configurações do N8N não encontradas no .env'
                ];
            }
            
            // Construir URL do webhook
            $webhookUrl = rtrim($n8nUrl, '/') . '/' . ltrim($n8nWebhookId, '/');
            
            // Debug: Log da URL e token (remover em produção)
            error_log("N8N Webhook URL: " . $webhookUrl);
            error_log("N8N API Key: " . substr($n8nApiKey, 0, 10) . "...");
            
            // Preparar arquivo temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'contract_');
            $contractFile->moveTo($tempFile);
            
            // Gerar JWT usando o secret
            $jwt = $this->generateJWT($n8nApiKey);
            
            // Configurar cURL
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $webhookUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $jwt,
                    'Content-Type: multipart/form-data'
                ],
                CURLOPT_POSTFIELDS => [
                    'contrato' => new \CURLFile($tempFile, 'application/pdf', $contractFile->getClientFilename()),
                    'user_id' => $userId
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            // Executar requisição
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            
            curl_close($curl);
            
            // Limpar arquivo temporário
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            
            // Verificar erros do cURL
            if ($error) {
                return [
                    'success' => false,
                    'error' => 'Erro cURL: ' . $error
                ];
            }
            
            // Verificar código de resposta
            if ($httpCode !== 200) {
                return [
                    'success' => false,
                    'error' => 'N8N retornou código ' . $httpCode . ': ' . $response
                ];
            }
            
            // Tentar decodificar resposta JSON
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $responseData = ['message' => $response];
            }
            
            return [
                'success' => true,
                'data' => $responseData
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Exceção ao enviar para N8N: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Gerar JWT usando o secret do N8N
     */
    private function generateJWT(string $secret): string
    {
        // Header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        // Payload (vazio como mostrado no Postman)
        $payload = json_encode([]);
        
        // Base64 URL encode
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        // Signature
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $secret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        // JWT
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
}
