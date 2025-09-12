<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;

/**
 * Middleware para validação de formulários
 * Sistema ROSS - Analista Jurídico
 */
class FormValidationMiddleware implements MiddlewareInterface
{
    /**
     * Processa a requisição
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        
        // Aplicar validações apenas para rotas de formulários
        if ($this->isFormRoute($path, $method)) {
            // Verificar se é uma requisição POST válida
            if ($method === 'POST') {
                if (!$this->isValidFormRequest($request)) {
                    // Redirecionar para a página de origem com erro
                    $referer = $request->getHeaderLine('Referer') ?: '/';
                    return new RedirectResponse($referer);
                }
            }
        }
        
        return $handler->handle($request);
    }
    
    /**
     * Verifica se é uma rota de formulário
     */
    private function isFormRoute(string $path, string $method): bool
    {
        $formRoutes = [
            'POST' => ['/login', '/cadastro', '/recuperar-senha']
        ];
        
        return isset($formRoutes[$method]) && in_array($path, $formRoutes[$method]);
    }
    
    /**
     * Valida se é uma requisição de formulário válida
     */
    private function isValidFormRequest(ServerRequestInterface $request): bool
    {
        // Verificar Content-Type
        $contentType = $request->getHeaderLine('Content-Type');
        if (!str_contains($contentType, 'application/x-www-form-urlencoded') && 
            !str_contains($contentType, 'multipart/form-data')) {
            return false;
        }
        
        // Verificar se tem dados no corpo da requisição
        $body = $request->getParsedBody();
        if (empty($body)) {
            return false;
        }
        
        // Verificar se não é uma requisição de API (sem referer ou com referer externo)
        $referer = $request->getHeaderLine('Referer');
        if (empty($referer)) {
            return false;
        }
        
        // Verificar se o referer é do mesmo domínio
        $refererHost = parse_url($referer, PHP_URL_HOST);
        $requestHost = $request->getUri()->getHost();
        
        if ($refererHost !== $requestHost) {
            return false;
        }
        
        return true;
    }
}
