<?php
function logMessage($message) {
    // Obter a data e hora atual
    $dateTime = date('Y-m-d H:i:s');
    
    // Obter o nome do arquivo de onde a função foi chamada
    $backtrace = debug_backtrace();
    $callingFile = isset($backtrace[0]['file']) ? basename($backtrace[0]['file']) : 'unknown';
    
    // Montar a mensagem de log
    $logMessage = "{$dateTime} - {$callingFile}: {$message}\n";
    
    // Nome do arquivo de log
    $logFile = __DIR__ . '/log-' . date('Y-m-d') . '.txt';

    // Verificar se o diretório de logs existe, se não, criar
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    // Caminho completo do arquivo de log
    $logFile = $logDir . '/log-' . date('Y-m-d') . '.txt';

    // Verificar se o arquivo de log existe e escrever a mensagem no final do arquivo
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>

