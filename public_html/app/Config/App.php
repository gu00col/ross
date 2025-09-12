<?php

namespace App\Config;

use Dotenv\Dotenv;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\ErrorMiddleware;
use App\Middleware\FormValidationMiddleware;

class App
{
    private Container $container;
    private Router $router;
    
    public function __construct()
    {
        $this->loadEnvironment();
        $this->container = new Container();
        $this->router = new Router();
        $this->configureContainer();
        $this->configureRouter();
    }
    
    private function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
    
    private function configureContainer(): void
    {
        // Configurações do container
        $this->container->add('config', [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'ROSS',
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => $_ENV['APP_DEBUG'] ?? false,
                'url' => $_ENV['APP_URL'] ?? 'http://localhost',
            ],
            'database' => [
                'connection' => $_ENV['DB_CONNECTION'] ?? 'pgsql',
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? '5432',
                'database' => $_ENV['DB_DATABASE'] ?? 'ross',
                'username' => $_ENV['DB_USERNAME'] ?? 'postgres',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ],
            'session' => [
                'driver' => $_ENV['SESSION_DRIVER'] ?? 'file',
                'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120,
                'encrypt' => $_ENV['SESSION_ENCRYPT'] ?? false,
                'path' => $_ENV['SESSION_PATH'] ?? '/',
                'domain' => $_ENV['SESSION_DOMAIN'] ?? 'localhost',
            ]
        ]);
        
        // Middleware
        $this->container->add(AuthMiddleware::class);
        $this->container->add(CorsMiddleware::class);
        $this->container->add(ErrorMiddleware::class);
        $this->container->add(FormValidationMiddleware::class);
        
        // Controllers
        $this->container->add(\App\Controllers\ErrorController::class);
    }
    
    private function configureRouter(): void
    {
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);
        $this->router->setStrategy($strategy);
        
        // Middleware global
        $this->router->middleware($this->container->get(CorsMiddleware::class));
        $this->router->middleware($this->container->get(ErrorMiddleware::class));
        
        // Definir rotas
        $this->defineRoutes();
    }
    
    private function defineRoutes(): void
    {
        // Rotas públicas
        $this->router->map('GET', '/', 'App\Controllers\HomeController::index');
        $this->router->map('GET', '/login', 'App\Controllers\AuthController::showLogin');
        $this->router->map('POST', '/login', 'App\Controllers\AuthController::login');
        $this->router->map('GET', '/logout', 'App\Controllers\AuthController::logout');
        $this->router->map('GET', '/recuperar-senha', 'App\Controllers\AuthController::showPasswordRecovery');
        $this->router->map('POST', '/recuperar-senha', 'App\Controllers\AuthController::passwordRecovery');
        
        // Rotas protegidas
        $this->router->group('', function ($router) {
            $router->map('GET', '/home', 'App\Controllers\DashboardController::home');
            $router->map('GET', '/dashboard', 'App\Controllers\DashboardController::index');
            $router->map('GET', '/contracts', 'App\Controllers\ContractsController::index');
            $router->map('GET', '/contract/{id}', 'App\Controllers\ContractController::show');
            $router->map('GET', '/contracts/{id}', 'App\Controllers\ContractController::show');
            $router->map('POST', '/contracts', 'App\Controllers\ContractController::store');
            $router->map('PUT', '/contracts/{id}', 'App\Controllers\ContractController::update');
            $router->map('DELETE', '/contracts/{id}', 'App\Controllers\ContractController::delete');
            $router->map('GET', '/perfil', 'App\Controllers\UserController::profile');
            $router->map('PUT', '/perfil', 'App\Controllers\UserController::updateProfile');
            $router->map('GET', '/configuracoes', 'App\Controllers\SettingsController::index');
            $router->map('PUT', '/configuracoes', 'App\Controllers\SettingsController::update');
        })->middleware($this->container->get(AuthMiddleware::class));
        
        // Rotas da API
        $this->router->group('/api', function ($router) {
            $router->map('POST', '/auth/login', 'App\Controllers\Api\AuthController::login');
            $router->map('POST', '/auth/refresh', 'App\Controllers\Api\AuthController::refresh');
            $router->map('POST', '/auth/logout', 'App\Controllers\Api\AuthController::logout');
            
            $router->map('GET', '/contracts', 'App\Controllers\Api\ContractController::index');
            $router->map('GET', '/contracts/{id}', 'App\Controllers\Api\ContractController::show');
            $router->map('POST', '/contracts', 'App\Controllers\Api\ContractController::store');
            $router->map('POST', '/contracts/upload', 'App\Controllers\Api\ContractController::upload');
            $router->map('PUT', '/contracts/{id}', 'App\Controllers\Api\ContractController::update');
            $router->map('DELETE', '/contracts/{id}', 'App\Controllers\Api\ContractController::delete');
            $router->map('POST', '/contracts/{id}/analyze', 'App\Controllers\Api\ContractController::analyze');
        })->middleware($this->container->get(AuthMiddleware::class));
        
    }
    
    public function run(): void
    {
        $request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        
        try {
            $response = $this->router->dispatch($request);
        } catch (\League\Route\Http\Exception\NotFoundException $e) {
            // Capturar rota não encontrada e redirecionar para 404
            $errorController = $this->container->get(\App\Controllers\ErrorController::class);
            $response = $errorController->notFound();
        } catch (\Exception $e) {
            // Capturar outros erros e redirecionar para 500
            $errorController = $this->container->get(\App\Controllers\ErrorController::class);
            $response = $errorController->internalError();
        }
        
        // Enviar headers
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        
        // Enviar status code
        http_response_code($response->getStatusCode());
        
        // Enviar corpo da resposta
        echo $response->getBody();
    }
    
    public function getContainer(): Container
    {
        return $this->container;
    }
    
    public function getRouter(): Router
    {
        return $this->router;
    }
}

