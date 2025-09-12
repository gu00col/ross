<?php

namespace App\Services;

use App\Models\User;

/**
 * Serviço para configuração automática do administrador
 * Sistema ROSS - Analista Jurídico
 */
class AdminSetupService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Verifica se o sistema precisa de configuração inicial
     */
    public function needsInitialSetup(): bool
    {
        return !$this->userModel->hasRecords();
    }

    /**
     * Cria o usuário administrador padrão baseado no .env
     */
    public function createDefaultAdmin(): ?array
    {
        try {
            // Verificar se já existem usuários
            if (!$this->needsInitialSetup()) {
                return null;
            }

            // Obter dados do .env
            $adminData = $this->getAdminDataFromEnv();
            
            if (!$adminData) {
                throw new \Exception('Dados do administrador não encontrados no .env');
            }

            // Criar usuário administrador
            $userId = $this->userModel->createUser($adminData);
            
            error_log('AdminSetupService - ID retornado: ' . $userId);
            
            if ($userId) {
                $user = $this->userModel->findById($userId);
                error_log('AdminSetupService - Usuário encontrado: ' . json_encode($user));
                return $user;
            }

            error_log('AdminSetupService - Falha ao criar usuário');
            return null;
        } catch (\Exception $e) {
            error_log('Erro ao criar administrador padrão: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém dados do administrador do arquivo .env
     */
    private function getAdminDataFromEnv(): ?array
    {
        $adminName = $_ENV['ADMIN_NAME'] ?? null;
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? null;
        $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? null;
        $adminIsSuperuser = $_ENV['ADMIN_IS_SUPERUSER'] ?? 'true';

        if (!$adminName || !$adminEmail || !$adminPassword) {
            return null;
        }

        return [
            'nome' => $adminName,
            'email' => $adminEmail,
            'password' => $adminPassword,
            'active' => true,
            'is_superuser' => filter_var($adminIsSuperuser, FILTER_VALIDATE_BOOLEAN)
        ];
    }

    /**
     * Verifica se o e-mail fornecido é o administrador padrão
     */
    public function isDefaultAdminEmail(string $email): bool
    {
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? null;
        return $adminEmail && $email === $adminEmail;
    }

    /**
     * Obtém informações do administrador padrão para exibição
     */
    public function getDefaultAdminInfo(): array
    {
        return [
            'nome' => $_ENV['ADMIN_NAME'] ?? 'Administrador',
            'email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@localhost'
        ];
    }
}
