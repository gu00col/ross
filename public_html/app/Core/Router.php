<?php
/**
 * Sistema de roteamento do ROSS
 * Gerencia rotas amigáveis e redirecionamentos
 */

class Router
{
    /**
     * @var array Rotas configuradas
     */
    private $routes;
    
    /**
     * @var string Rota atual
     */
    private $currentRoute;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->routes = require_once __DIR__ . '/../Config/routes.php';
        $this->currentRoute = $this->getCurrentRoute();
    }
    
    /**
     * Obtém a rota atual
     * @return string
     */
    private function getCurrentRoute(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];
        
        // Remover query string
        $requestUri = strtok($requestUri, '?');
        
        // Remover barra inicial
        $requestUri = ltrim($requestUri, '/');
        
        // Remover script name se estiver presente
        if (strpos($requestUri, basename($scriptName)) === 0) {
            $requestUri = substr($requestUri, strlen(basename($scriptName)));
            $requestUri = ltrim($requestUri, '/');
        }
        
        return $requestUri ?: '/';
    }
    
    /**
     * Resolve a rota atual
     * @return string|null
     */
    public function resolve(): ?string
    {
        // Verificar redirecionamentos especiais
        if (isset($this->routes['redirects'][$this->currentRoute])) {
            return $this->routes['redirects'][$this->currentRoute];
        }
        
        // Verificar rotas públicas
        if (isset($this->routes['public'][$this->currentRoute])) {
            return $this->routes['public'][$this->currentRoute];
        }
        
        // Verificar rotas protegidas
        if (isset($this->routes['protected'][$this->currentRoute])) {
            // Verificar se usuário está logado
            if (!$this->isUserLoggedIn()) {
                $this->redirectToLogin();
                return null;
            }
            return $this->routes['protected'][$this->currentRoute];
        }
        
        // Verificar rotas da API
        if (strpos($this->currentRoute, 'api/') === 0) {
            $apiRoute = substr($this->currentRoute, 4); // Remove 'api/'
            if (isset($this->routes['api'][$apiRoute])) {
                return $this->routes['api'][$apiRoute];
            }
        }
        
        // Rota não encontrada
        return null;
    }
    
    /**
     * Verifica se usuário está logado
     * @return bool
     */
    private function isUserLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Redireciona para login
     */
    private function redirectToLogin(): void
    {
        header('Location: /login');
        exit;
    }
    
    /**
     * Redireciona para uma rota
     * @param string $route
     */
    public function redirect(string $route): void
    {
        header("Location: /{$route}");
        exit;
    }
    
    /**
     * Gera URL para uma rota
     * @param string $route
     * @return string
     */
    public function url(string $route): string
    {
        return "/{$route}";
    }
    
    /**
     * Verifica se a rota atual é protegida
     * @return bool
     */
    public function isProtectedRoute(): bool
    {
        return isset($this->routes['protected'][$this->currentRoute]);
    }
    
    /**
     * Verifica se a rota atual é pública
     * @return bool
     */
    public function isPublicRoute(): bool
    {
        return isset($this->routes['public'][$this->currentRoute]);
    }
    
    /**
     * Obtém todas as rotas
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
    
    /**
     * Obtém a rota atual
     * @return string
     */
    public function getCurrentRouteName(): string
    {
        return $this->currentRoute;
    }
}