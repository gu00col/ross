<?php

namespace App\Services;

/**
 * Serviço para gerenciar mensagens flash
 * Sistema ROSS - Analista Jurídico
 */
class FlashMessageService
{
    private const FLASH_KEY = 'flash_messages';
    private const FLASH_NEXT_KEY = 'flash_messages_next';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Adiciona uma mensagem flash
     */
    public function add(string $type, string $message): void
    {
        if (!isset($_SESSION[self::FLASH_KEY])) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        
        $_SESSION[self::FLASH_KEY][] = [
            'type' => $type,
            'message' => $message,
            'timestamp' => time()
        ];
    }

    /**
     * Adiciona mensagem de sucesso
     */
    public function success(string $message): void
    {
        $this->add('success', $message);
    }

    /**
     * Adiciona mensagem de erro
     */
    public function error(string $message): void
    {
        $this->add('danger', $message);
    }

    /**
     * Adiciona mensagem de aviso
     */
    public function warning(string $message): void
    {
        $this->add('warning', $message);
    }

    /**
     * Adiciona mensagem de informação
     */
    public function info(string $message): void
    {
        $this->add('info', $message);
    }

    /**
     * Recupera todas as mensagens flash
     */
    public function get(): array
    {
        $messages = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);
        return $messages;
    }

    /**
     * Verifica se existem mensagens flash
     */
    public function has(): bool
    {
        return !empty($_SESSION[self::FLASH_KEY]);
    }

    /**
     * Renderiza as mensagens flash em HTML Bootstrap
     */
    public function render(): string
    {
        $messages = $this->get();
        
        if (empty($messages)) {
            return '';
        }

        $html = '';
        foreach ($messages as $message) {
            $html .= $this->renderMessage($message['type'], $message['message']);
        }

        return $html;
    }

    /**
     * Renderiza uma mensagem individual
     */
    private function renderMessage(string $type, string $message): string
    {
        $icon = $this->getIconForType($type);
        
        return sprintf(
            '<div class="mt-3 alert alert-%s alert-dismissible fade show" role="alert">
                <i class="%s me-2"></i>
                %s
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>',
            $type,
            $icon,
            htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Retorna o ícone apropriado para cada tipo de mensagem
     */
    private function getIconForType(string $type): string
    {
        $icons = [
            'success' => 'fas fa-check-circle',
            'danger' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle'
        ];

        return $icons[$type] ?? 'fas fa-info-circle';
    }
}
