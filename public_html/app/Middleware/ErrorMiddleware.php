<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;

class ErrorMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return $this->handleException($e, $request);
        }
    }
    
    private function handleException(\Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        // Log do erro
        error_log("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        
        // Se for uma requisição AJAX, retornar JSON
        if ($this->isAjaxRequest($request)) {
            return new JsonResponse([
                'error' => 'Internal Server Error',
                'message' => $_ENV['APP_DEBUG'] ? $e->getMessage() : 'Ocorreu um erro interno do servidor'
            ], 500);
        }
        
        // Retornar página de erro
        $statusCode = $e->getCode() ?: 500;
        $message = $_ENV['APP_DEBUG'] ? $e->getMessage() : 'Ocorreu um erro interno do servidor';
        
        return new HtmlResponse($this->renderErrorPage($statusCode, $message), $statusCode);
    }
    
    private function isAjaxRequest(ServerRequestInterface $request): bool
    {
        $headers = $request->getHeaders();
        return isset($headers['X-Requested-With']) && 
               in_array('XMLHttpRequest', $headers['X-Requested-With']);
    }
    
    private function renderErrorPage(int $statusCode, string $message): string
    {
        $title = match($statusCode) {
            404 => 'Página não encontrada',
            500 => 'Erro interno do servidor',
            default => 'Erro'
        };
        
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title} - ROSS</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                body { 
                    background: linear-gradient(135deg, #0D2149 0%, #1a3a7a 100%); 
                    min-height: 100vh; 
                    font-family: 'Inter', sans-serif;
                }
                .error-container { 
                    min-height: 100vh; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                }
                .error-card { 
                    background: white; 
                    border-radius: 15px; 
                    padding: 3rem; 
                    text-align: center; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
                }
                .error-code { 
                    font-size: 6rem; 
                    font-weight: 900; 
                    color: #0D2149; 
                    margin-bottom: 1rem; 
                }
                .error-message { 
                    font-size: 1.5rem; 
                    color: #666; 
                    margin-bottom: 2rem; 
                }
                .btn-primary { 
                    background: #0D2149; 
                    border: none; 
                    padding: 12px 30px; 
                    border-radius: 8px; 
                    text-decoration: none;
                    color: white;
                    display: inline-block;
                }
                .btn-primary:hover { 
                    background: #1a3a7a; 
                    color: white;
                }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <div class='error-card'>
                    <div class='error-code'>{$statusCode}</div>
                    <h1 class='error-message'>{$title}</h1>
                    <p class='text-muted mb-4'>{$message}</p>
                    <a href='/' class='btn btn-primary'>Voltar ao Início</a>
                </div>
            </div>
        </body>
        </html>";
    }
}

