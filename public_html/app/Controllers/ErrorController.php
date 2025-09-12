<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;

/**
 * Controller para páginas de erro
 * Sistema ROSS - Analista Jurídico
 */
class ErrorController
{
    /**
     * Página 404 - Não encontrado
     */
    public function notFound(): HtmlResponse
    {
        ob_start();
        include __DIR__ . '/../../404.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content, 404);
    }
    
    /**
     * Página 500 - Erro interno do servidor
     */
    public function internalError(): HtmlResponse
    {
        ob_start();
        include __DIR__ . '/../../500.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content, 500);
    }
    
    /**
     * Página 403 - Acesso negado
     */
    public function forbidden(): HtmlResponse
    {
        ob_start();
        include __DIR__ . '/../../403.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content, 403);
    }
}
