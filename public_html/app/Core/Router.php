<?php
/**
 * Classe de Roteamento
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Router
{
    private $routes = [];
    private $middleware = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Carregar rotas
     */
    private function loadRoutes()
    {
        // Rotas básicas
        $this->routes = [
            'GET' => [
                '/' => 'HomeController@index',
                '/login' => 'AuthController@login',
                '/logout' => 'AuthController@logout',
                '/home' => 'DashboardController@index',
                '/contracts' => 'ContractController@index',
                '/contract/{id}' => 'ContractController@show',
                '/my-account' => 'UserController@account',
                '/settings' => 'SettingsController@index',
            ],
            'POST' => [
                '/login' => 'AuthController@loginProcess',
                '/logout' => 'AuthController@logoutProcess',
                '/upload' => 'ContractController@upload',
                '/update-profile' => 'UserController@updateProfile',
                '/change-password' => 'UserController@changePassword',
            ],
        ];
    }

    /**
     * Despachar requisição
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover barra final
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        // Buscar rota
        $route = $this->findRoute($method, $uri);
        
        if (!$route) {
            $this->handle404();
            return;
        }

        // Executar middleware
        $this->runMiddleware($route);

        // Executar controller
        $this->runController($route);
    }

    /**
     * Buscar rota
     */
    private function findRoute($method, $uri)
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if ($this->matchRoute($route, $uri)) {
                return [
                    'route' => $route,
                    'handler' => $handler,
                    'params' => $this->extractParams($route, $uri)
                ];
            }
        }

        return null;
    }

    /**
     * Verificar se rota corresponde
     */
    private function matchRoute($route, $uri)
    {
        // Converter parâmetros {id} para regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
    }

    /**
     * Extrair parâmetros da rota
     */
    private function extractParams($route, $uri)
    {
        $params = [];
        $routeParts = explode('/', $route);
        $uriParts = explode('/', $uri);
        
        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $params[$matches[1]] = $uriParts[$index] ?? null;
            }
        }
        
        return $params;
    }

    /**
     * Executar middleware
     */
    private function runMiddleware($route)
    {
        // Implementar middleware se necessário
    }

    /**
     * Executar controller
     */
    private function runController($route)
    {
        list($controller, $method) = explode('@', $route['handler']);
        
        $controllerClass = "App\\Controllers\\{$controller}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} não encontrado");
        }
        
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $method)) {
            throw new \Exception("Método {$method} não encontrado no controller {$controllerClass}");
        }
        
        // Passar parâmetros para o método
        call_user_func_array([$controllerInstance, $method], $route['params']);
    }

    /**
     * Tratar erro 404
     */
    private function handle404()
    {
        http_response_code(404);
        
        if (file_exists(APP_PATH . '/Views/errors/404.php')) {
            include APP_PATH . '/Views/errors/404.php';
        } else {
            echo '<h1>404 - Página não encontrada</h1>';
        }
    }

    /**
     * Adicionar rota
     */
    public function addRoute($method, $route, $handler)
    {
        $this->routes[$method][$route] = $handler;
    }

    /**
     * Adicionar middleware
     */
    public function addMiddleware($name, $callback)
    {
        $this->middleware[$name] = $callback;
    }
}
