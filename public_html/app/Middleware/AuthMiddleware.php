<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->isUserLoggedIn()) {
            // Se for uma requisição AJAX, retornar JSON
            if ($this->isAjaxRequest($request)) {
                return new JsonResponse([
                    'error' => 'Unauthorized',
                    'message' => 'Você precisa estar logado para acessar este recurso'
                ], 401);
            }
            
            // Redirecionar para login
            return new RedirectResponse('/login');
        }
        
        return $handler->handle($request);
    }
    
    private function isUserLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    private function isAjaxRequest(ServerRequestInterface $request): bool
    {
        $headers = $request->getHeaders();
        return isset($headers['X-Requested-With']) && 
               in_array('XMLHttpRequest', $headers['X-Requested-With']);
    }
}

