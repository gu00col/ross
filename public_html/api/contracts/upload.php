<?php
/**
 * API Endpoint para Upload de Contratos
 * Sistema ROSS - Analista Jurídico
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Carregar autoloader do Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->load();

try {
    // Verificar se arquivo foi enviado
    if (!isset($_FILES['contrato']) || $_FILES['contrato']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Nenhum arquivo foi enviado ou ocorreu um erro no upload');
    }

    $file = $_FILES['contrato'];
    $userId = $_POST['user_id'] ?? $_SESSION['user_id'];
    $description = $_POST['description'] ?? '';

    // Validar arquivo
    if ($file['type'] !== 'application/pdf') {
        throw new Exception('Apenas arquivos PDF são aceitos');
    }

    if ($file['size'] > 10 * 1024 * 1024) { // 10MB
        throw new Exception('Arquivo muito grande. Tamanho máximo: 10MB');
    }

    // Preparar dados para envio ao N8N
    $n8nUrl = $_ENV['N8N_URL'];
    $n8nApiKey = $_ENV['N8N_API_KEY'];

    if (empty($n8nUrl) || empty($n8nApiKey)) {
        throw new Exception('Configurações do N8N não encontradas');
    }

    // Preparar cURL para envio ao N8N
    $ch = curl_init();
    
    // Dados do formulário
    $postData = [
        'contrato' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
        'user_id' => $userId,
        'description' => $description
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => $n8nUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $n8nApiKey
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    // Executar requisição
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);

    if ($error) {
        throw new Exception('Erro na comunicação com o N8N: ' . $error);
    }

    if ($httpCode !== 200) {
        throw new Exception('Erro no processamento: HTTP ' . $httpCode);
    }

    // Resposta de sucesso
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Contrato enviado para processamento com sucesso!',
        'data' => [
            'filename' => $file['name'],
            'size' => $file['size'],
            'user_id' => $userId,
            'n8n_response' => $response
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

