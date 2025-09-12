<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\User;
use App\Services\AuthService;
use App\Services\FlashMessageService;
use App\Services\AdminSetupService;

class AuthController
{
    private AuthService $authService;
    private FlashMessageService $flashService;
    private AdminSetupService $adminSetupService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->flashService = new FlashMessageService();
        $this->adminSetupService = new AdminSetupService();
    }
    
    public function showLogin(): ResponseInterface
    {
        // Se já estiver logado, redirecionar para home
        if ($this->authService->isLoggedIn()) {
            return new RedirectResponse('/home');
        }
        
        // Verificar se é a primeira vez (não existem usuários)
        if ($this->adminSetupService->needsInitialSetup()) {
            // Criar administrador padrão automaticamente
            $admin = $this->adminSetupService->createDefaultAdmin();
            
            if ($admin) {
                $this->flashService->info('Sistema configurado! Use as credenciais do administrador para fazer login.');
            } else {
                $this->flashService->error('Erro ao configurar sistema. Verifique as configurações do .env');
            }
        }
        
        // Incluir página de login
        ob_start();
        include __DIR__ . '/../../login.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content);
    }
    
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        
        try {
            $user = $this->authService->authenticate($email, $password);
            
            if ($user) {
                // Login bem-sucedido
                $this->flashService->success('Login realizado com sucesso!');
                return new RedirectResponse('/home');
            } else {
                // Credenciais inválidas
                $this->flashService->error('E-mail ou senha inválidos');
                return new RedirectResponse('/login');
            }
        } catch (\Exception $e) {
            $this->flashService->error('Erro interno do servidor');
            return new RedirectResponse('/login');
        }
    }
    
    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return new RedirectResponse('/login');
    }
    
    
    public function showPasswordRecovery(): HtmlResponse
    {
        // Incluir página de recuperação de senha
        ob_start();
        include __DIR__ . '/../../password_recovery.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content);
    }
    
    public function passwordRecovery(ServerRequestInterface $request): JsonResponse
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        
        try {
            $result = $this->authService->sendPasswordRecovery($email);
            
            if ($result) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'E-mail de recuperação enviado com sucesso'
                ]);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'E-mail não encontrado'
                ], 404);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }
}

