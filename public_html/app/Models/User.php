<?php

namespace App\Models;

/**
 * Model para a tabela users
 * Sistema ROSS - Analista Jurídico
 */


class User extends BaseModel
{
    /**
     * @var string Nome da tabela
     */
    protected $table = 'users';

    /**
     * @var string Nome da chave primária
     */
    protected $primaryKey = 'id';

    /**
     * Busca usuário por e-mail
     * @param string $email E-mail do usuário
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca usuários ativos
     * @param array $conditions Condições adicionais
     * @param string $orderBy Campo para ordenação
     * @param string $direction Direção da ordenação
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array
     */
    public function findActive(array $conditions = [], string $orderBy = 'created_at', string $direction = 'DESC', int $limit = null, int $offset = 0): array
    {
        $conditions['active'] = true;
        return $this->findAll($conditions, $orderBy, $direction, $limit, $offset);
    }

    /**
     * Busca superusuários
     * @return array
     */
    public function findSuperUsers(): array
    {
        return $this->findAll(['is_superuser' => true, 'active' => true], 'created_at', 'ASC');
    }

    /**
     * Cria um novo usuário
     * @param array $userData Dados do usuário
     * @return string ID do usuário criado
     * @throws Exception
     */
    public function createUser(array $userData): string
    {
        // Validar dados obrigatórios
        $requiredFields = ['nome', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                throw new Exception("Campo obrigatório '{$field}' não fornecido");
            }
        }

        // Verificar se e-mail já existe
        if ($this->findByEmail($userData['email'])) {
            throw new Exception("E-mail já cadastrado no sistema");
        }

        // Criptografar senha
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Remover campos que não devem ser salvos no banco
        unset($userData['password_confirmation']);

        // Limpar dados - remover campos vazios que podem causar problemas
        $userData = array_filter($userData, function($value) {
            return $value !== '' && $value !== null;
        });

        // Definir valores padrão
        $userData['active'] = isset($userData['active']) ? (bool)$userData['active'] : true;
        $userData['is_superuser'] = isset($userData['is_superuser']) ? (bool)$userData['is_superuser'] : false;
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');

        return $this->create($userData);
    }

    /**
     * Atualiza dados do usuário
     * @param string $id ID do usuário
     * @param array $userData Dados para atualização
     * @return bool
     * @throws Exception
     */
    public function updateUser(string $id, array $userData): bool
    {
        // Verificar se usuário existe
        $user = $this->findById($id);
        if (!$user) {
            throw new Exception("Usuário não encontrado");
        }

        // Se e-mail foi alterado, verificar se já existe
        if (isset($userData['email']) && $userData['email'] !== $user['email']) {
            $existingUser = $this->findByEmail($userData['email']);
            if ($existingUser && $existingUser['id'] !== $id) {
                throw new Exception("E-mail já cadastrado para outro usuário");
            }
        }

        // Se senha foi fornecida, criptografar
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }

        // Atualizar timestamp
        $userData['updated_at'] = date('Y-m-d H:i:s');

        return $this->update($id, $userData);
    }

    /**
     * Verifica credenciais de login
     * @param string $email E-mail do usuário
     * @param string $password Senha do usuário
     * @return array|null Dados do usuário se credenciais válidas
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }

        if (!$user['active']) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        // Remover senha do retorno por segurança
        unset($user['password']);
        
        return $user;
    }

    /**
     * Ativa/desativa usuário
     * @param string $id ID do usuário
     * @param bool $active Status ativo/inativo
     * @return bool
     */
    public function setActiveStatus(string $id, bool $active): bool
    {
        return $this->update($id, [
            'active' => $active,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Define usuário como superusuário
     * @param string $id ID do usuário
     * @param bool $isSuperuser Status de superusuário
     * @return bool
     */
    public function setSuperUserStatus(string $id, bool $isSuperuser): bool
    {
        return $this->update($id, [
            'is_superuser' => $isSuperuser,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Altera senha do usuário
     * @param string $id ID do usuário
     * @param string $newPassword Nova senha
     * @return bool
     */
    public function changePassword(string $id, string $newPassword): bool
    {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca usuários por período de criação
     * @param string $startDate Data inicial (Y-m-d)
     * @param string $endDate Data final (Y-m-d)
     * @return array
     */
    public function findByDateRange(string $startDate, string $endDate): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE DATE(created_at) BETWEEN :start_date AND :end_date 
                ORDER BY created_at DESC";
        
        return $this->query($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Estatísticas de usuários
     * @return array
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN active = true THEN 1 END) as active_users,
                    COUNT(CASE WHEN active = false THEN 1 END) as inactive_users,
                    COUNT(CASE WHEN is_superuser = true THEN 1 END) as super_users,
                    COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) as users_today,
                    COUNT(CASE WHEN DATE(created_at) >= CURRENT_DATE - INTERVAL '7 days' THEN 1 END) as users_this_week,
                    COUNT(CASE WHEN DATE(created_at) >= CURRENT_DATE - INTERVAL '30 days' THEN 1 END) as users_this_month
                FROM {$this->table}";
        
        $result = $this->queryOne($sql);
        
        return [
            'total_users' => (int) $result['total_users'],
            'active_users' => (int) $result['active_users'],
            'inactive_users' => (int) $result['inactive_users'],
            'super_users' => (int) $result['super_users'],
            'users_today' => (int) $result['users_today'],
            'users_this_week' => (int) $result['users_this_week'],
            'users_this_month' => (int) $result['users_this_month']
        ];
    }

    /**
     * Busca usuários com paginação
     * @param int $page Página atual
     * @param int $perPage Registros por página
     * @param array $filters Filtros de busca
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];

        // Aplicar filtros
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $sql = "SELECT * FROM {$this->table} 
                    WHERE (nome ILIKE :search OR email ILIKE :search)";
            $params = ['search' => "%{$search}%"];
        } else {
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
        }

        if (!empty($filters['active'])) {
            $sql .= empty($filters['search']) ? " WHERE" : " AND";
            $sql .= " active = :active";
            $params['active'] = $filters['active'];
        }

        if (!empty($filters['is_superuser'])) {
            $sql .= (strpos($sql, 'WHERE') !== false) ? " AND" : " WHERE";
            $sql .= " is_superuser = :is_superuser";
            $params['is_superuser'] = $filters['is_superuser'];
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        // Contar total de registros
        $countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
        $countSql = preg_replace('/LIMIT.*$/', '', $countSql);
        $countStmt = $this->db->prepare($countSql);
        unset($params['limit'], $params['offset']);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        return [
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int) $total,
                'total_pages' => ceil($total / $perPage),
                'has_next' => $page < ceil($total / $perPage),
                'has_prev' => $page > 1
            ]
        ];
    }
}