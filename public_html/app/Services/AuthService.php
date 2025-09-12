<?php

namespace App\Services;

use App\Models\User;
use App\Services\EmailService;
use App\Services\TokenService;

class AuthService
{
    private User $userModel;
    private EmailService $emailService;
    private TokenService $tokenService;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->emailService = new EmailService();
        $this->tokenService = new TokenService();
    }
    
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            $this->startSession($user);
            return $user;
        }
        
        return null;
    }
    
    public function register(array $data): ?array
    {
        // Validar dados
        $this->validateRegistrationData($data);
        
        // Criar usuário
        $userId = $this->userModel->createUser($data);
        
        if ($userId) {
            // Buscar usuário criado
            $user = $this->userModel->findById($userId);
            return $user ?: null;
        }
        
        return null;
    }
    
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpar sessão
        session_unset();
        session_destroy();
        
        // Iniciar nova sessão
        session_start();
    }
    
    public function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->findById($_SESSION['user_id']);
    }
    
    public function sendPasswordRecovery(string $email): bool
    {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        // Gerar token de recuperação
        $token = $this->tokenService->generatePasswordRecoveryToken($user['id']);
        
        // Enviar e-mail
        return $this->emailService->sendPasswordRecovery($user['email'], $user['nome'], $token);
    }
    
    public function resetPassword(string $token, string $newPassword): bool
    {
        $userId = $this->tokenService->validatePasswordRecoveryToken($token);
        
        if (!$userId) {
            return false;
        }
        
        // Atualizar senha
        return $this->userModel->changePassword($userId, $newPassword);
    }
    
    private function startSession(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_is_superuser'] = $user['is_superuser'] ?? false;
        $_SESSION['login_time'] = time();
    }
    
    private function validateRegistrationData(array $data): void
    {
        $required = ['nome', 'email', 'password', 'password_confirmation'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Campo '{$field}' é obrigatório");
            }
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('E-mail inválido');
        }
        
        if (strlen($data['password']) < 8) {
            throw new \InvalidArgumentException('Senha deve ter pelo menos 8 caracteres');
        }
        
        if ($data['password'] !== $data['password_confirmation']) {
            throw new \InvalidArgumentException('Senhas não coincidem');
        }
    }
}

