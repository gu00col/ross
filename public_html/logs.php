<?php
function logMessage($message, $level = 'INFO') {
    // Obter a data e hora atual
    $dateTime = date('Y-m-d H:i:s');
    
    // Obter o nome do arquivo de onde a função foi chamada
    $backtrace = debug_backtrace();
    $callingFile = isset($backtrace[0]['file']) ? basename($backtrace[0]['file']) : 'unknown';
    
    // Montar a mensagem de log
    $logMessage = "[{$dateTime}] [{$level}] [{$callingFile}] - {$message}" . PHP_EOL;
    
    // Caminho para o diretório de logs
    $logDir = __DIR__ . '/logs';
    
    // Tenta criar o diretório se ele não existir
    if (!is_dir($logDir)) {
        // @ suprime o erro se a criação falhar (ex: permissão)
        @mkdir($logDir, 0775, true);
    }
    
    // Verifica se o diretório é gravável
    if (is_dir($logDir) && is_writable($logDir)) {
        $logFile = $logDir . '/log-' . date('Y-m-d') . '.txt';
        // Escreve no arquivo
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    } else {
        // Fallback: se não conseguir escrever no arquivo, envia para o log de erros do PHP/Apache
        error_log("Falha ao escrever no arquivo de log. Mensagem: " . $logMessage);
    }
}

