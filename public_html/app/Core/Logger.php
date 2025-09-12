<?php
/**
 * Classe de Log
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Logger
{
    private $config;
    private $logPath;

    public function __construct()
    {
        $this->config = config('log');
        $this->logPath = storage_path('logs');
        
        // Criar diretório de logs se não existir
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log de emergência
     */
    public function emergency($message, $context = [])
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Log de alerta
     */
    public function alert($message, $context = [])
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Log crítico
     */
    public function critical($message, $context = [])
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Log de erro
     */
    public function error($message, $context = [])
    {
        $this->log('error', $message, $context);
    }

    /**
     * Log de aviso
     */
    public function warning($message, $context = [])
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Log de notícia
     */
    public function notice($message, $context = [])
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Log de informação
     */
    public function info($message, $context = [])
    {
        $this->log('info', $message, $context);
    }

    /**
     * Log de debug
     */
    public function debug($message, $context = [])
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Método principal de log
     */
    public function log($level, $message, $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        
        $logFile = $this->getLogFile();
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Obter arquivo de log
     */
    private function getLogFile()
    {
        $channel = $this->config['default'];
        $config = $this->config['channels'][$channel];
        
        if ($config['driver'] === 'daily') {
            return $this->logPath . '/app-' . date('Y-m-d') . '.log';
        }
        
        return $config['path'];
    }

    /**
     * Limpar logs antigos
     */
    public function clean($days = 30)
    {
        $files = glob($this->logPath . '/*.log');
        $cutoff = time() - ($days * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
