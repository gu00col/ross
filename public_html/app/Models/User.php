<?php
/**
 * Model de Usuário
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

class User extends BaseModel
{
    protected $table = 'users';
    
    // Campos da tabela
    protected $fillable = [
        'id',
        'nome',
        'email',
        'password',
        'active',
        'is_superuser',
        'created_at',
        'updated_at'
    ];
    
    // Status do usuário
    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Buscar usuário por email
     * 
     * @param string $email Email do usuário
     * @return array|null Dados do usuário
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    /**
     * Verificar se email já existe
     * 
     * @param string $email Email para verificar
     * @param string $excludeId ID para excluir da verificação (para updates)
     * @return bool Se email existe
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Criar novo usuário
     * 
     * @param array $data Dados do usuário
     * @return bool Sucesso da operação
     */
    public function create($data)
    {
        // Verificar se email já existe
        if (isset($data['email']) && $this->emailExists($data['email'])) {
            throw new \InvalidArgumentException('Email já está em uso');
        }
        
        // Criptografar senha se fornecida
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $validatedData = $this->validate($data);
        return $this->db->insert($this->table, $validatedData);
    }

    /**
     * Atualizar usuário
     * 
     * @param string $id ID do usuário
     * @param array $data Dados para atualizar
     * @return bool Sucesso da operação
     */
    public function update($id, $data)
    {
        // Verificar se email já existe (excluindo o próprio usuário)
        if (isset($data['email']) && $this->emailExists($data['email'], $id)) {
            throw new \InvalidArgumentException('Email já está em uso');
        }
        
        // Criptografar senha se fornecida
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Remover senha se estiver vazia para não sobrescrever
            unset($data['password']);
        }
        
        // Adicionar updated_at
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $validatedData = $this->validate($data, true);
        return $this->db->update($this->table, $validatedData, 'id = :id', ['id' => $id]);
    }

    /**
     * Verificar senha do usuário
     * 
     * @param string $email Email do usuário
     * @param string $password Senha para verificar
     * @return array|null Dados do usuário se senha correta
     */
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        
        // Remover senha do retorno
        unset($user['password']);
        return $user;
    }

    /**
     * Ativar usuário
     * 
     * @param string $id ID do usuário
     * @return bool Sucesso da operação
     */
    public function activate($id)
    {
        return $this->update($id, [
            'active' => self::STATUS_ACTIVE,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Desativar usuário
     * 
     * @param string $id ID do usuário
     * @return bool Sucesso da operação
     */
    public function deactivate($id)
    {
        return $this->update($id, [
            'active' => self::STATUS_INACTIVE,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Buscar usuários ativos
     * 
     * @param array $filters Filtros de busca
     * @return array Lista de usuários ativos
     */
    public function getActive($filters = [])
    {
        $filters['active'] = self::STATUS_ACTIVE;
        return $this->getAll($filters);
    }

    /**
     * Buscar superusuários
     * 
     * @return array Lista de superusuários
     */
    public function getSuperusers()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_superuser = true ORDER BY created_at";
        return $this->db->fetchAll($sql);
    }

    /**
     * Contar usuários por status
     * 
     * @param bool $active Status ativo/inativo
     * @return int Número de usuários
     */
    public function countByStatus($active)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE active = :active";
        $result = $this->db->fetch($sql, ['active' => $active]);
        return $result['count'] ?? 0;
    }

    /**
     * Obter estatísticas de usuários
     * 
     * @return array Estatísticas
     */
    public function getStats()
    {
        $stats = [];
        
        $stats['total'] = $this->count();
        $stats['active'] = $this->countByStatus(self::STATUS_ACTIVE);
        $stats['inactive'] = $this->countByStatus(self::STATUS_INACTIVE);
        $stats['superusers'] = $this->count(['is_superuser' => true]);
        
        return $stats;
    }

    /**
     * Buscar usuários recentes
     * 
     * @param int $limit Limite de resultados
     * @return array Usuários recentes
     */
    public function getRecent($limit = 5)
    {
        $sql = "SELECT id, nome, email, active, created_at FROM {$this->table} 
                ORDER BY created_at DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Validar dados antes de inserir/atualizar
     * 
     * @param array $data Dados para validar
     * @param bool $isUpdate Se é uma atualização
     * @return array Dados validados
     * @throws \InvalidArgumentException
     */
    public function validate($data, $isUpdate = false)
    {
        $validated = parent::validate($data, $isUpdate);
        
        // Validações específicas
        if (isset($validated['email'])) {
            if (!filter_var($validated['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Email inválido');
            }
        }
        
        if (isset($validated['nome']) && empty(trim($validated['nome']))) {
            throw new \InvalidArgumentException('Nome é obrigatório');
        }
        
        if (!$isUpdate && (!isset($validated['password']) || empty($validated['password']))) {
            throw new \InvalidArgumentException('Senha é obrigatória');
        }
        
        if (isset($validated['active']) && !is_bool($validated['active'])) {
            $validated['active'] = (bool) $validated['active'];
        }
        
        if (isset($validated['is_superuser']) && !is_bool($validated['is_superuser'])) {
            $validated['is_superuser'] = (bool) $validated['is_superuser'];
        }
        
        return $validated;
    }
}
