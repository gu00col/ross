<?php

namespace App\Models;

use MF\Model\Model;

/**
 * Model para gerenciar usuários do sistema
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class User extends Model
{
    /**
     * Obtém todos os usuários ativos
     * 
     * @return array Lista de usuários ativos
     */
    public function getActiveUsers(): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, active, is_superuser, created_at, updated_at 
            FROM users 
            WHERE active = true 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém todos os usuários
     * 
     * @return array Lista de todos os usuários
     */
    public function getAllUsers(): array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, active, is_superuser, created_at, updated_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém um usuário por ID
     * 
     * @param string $id UUID do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function getUserById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, active, is_superuser, created_at, updated_at 
            FROM users 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtém um usuário por email
     * 
     * @param string $email Email do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, password, active, is_superuser, created_at, updated_at 
            FROM users 
            WHERE email = :email
        ");
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria um novo usuário
     * 
     * @param string $nome Nome do usuário
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @param bool $isSuperuser Se é superusuário
     * @return string UUID do usuário criado
     */
    public function createUser(string $nome, string $email, string $password, bool $isSuperuser = false): string
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (nome, email, password, is_superuser) 
            VALUES (:nome, :email, :password, :is_superuser)
            RETURNING id
        ");
        $stmt->bindParam(':nome', $nome, \PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $passwordHash, \PDO::PARAM_STR);
        $stmt->bindParam(':is_superuser', $isSuperuser, \PDO::PARAM_BOOL);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Atualiza um usuário
     * 
     * @param string $id UUID do usuário
     * @param string $nome Nome do usuário
     * @param string $email Email do usuário
     * @param string|null $password Nova senha (opcional)
     * @param bool $active Status ativo
     * @param bool $isSuperuser Se é superusuário
     * @return int Número de linhas afetadas
     */
    public function updateUser(string $id, string $nome, string $email, ?string $password = null, bool $active = true, bool $isSuperuser = false): int
    {
        $sql = "UPDATE users SET nome = :nome, email = :email, active = :active, is_superuser = :is_superuser, updated_at = CURRENT_TIMESTAMP";
        $params = [
            ':id' => $id,
            ':nome' => $nome,
            ':email' => $email,
            ':active' => $active,
            ':is_superuser' => $isSuperuser
        ];

        if ($password !== null) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_bool($value) ? \PDO::PARAM_BOOL : \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Desativa um usuário (soft delete)
     * 
     * @param string $id UUID do usuário
     * @return int Número de linhas afetadas
     */
    public function deactivateUser(string $id): int
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET active = false, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove um usuário permanentemente
     * 
     * @param string $id UUID do usuário
     * @return int Número de linhas afetadas
     */
    public function deleteUser(string $id): int
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Valida credenciais de login
     * 
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @return array|null Dados do usuário se válido, null caso contrário
     */
    public function validateCredentials(string $email, string $password): ?array
    {
        $user = $this->getUserByEmail($email);
        
        if ($user && $user['active'] && password_verify($password, $user['password'])) {
            // Remove a senha do retorno por segurança
            unset($user['password']);
            return $user;
        }
        
        return null;
    }

    /**
     * Verifica se um email já existe
     * 
     * @param string $email Email a verificar
     * @param string|null $excludeId ID do usuário a excluir da verificação
     * @return bool True se email existe, false caso contrário
     */
    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}
