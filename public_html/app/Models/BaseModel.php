<?php

namespace App\Models;

/**
 * Classe base para todos os models
 * Sistema ROSS - Analista Jurídico
 */

abstract class BaseModel
{
    /**
     * @var \PDO Instância da conexão com o banco de dados
     */
    protected $db;

    /**
     * @var string Nome da tabela
     */
    protected $table;

    /**
     * @var string Nome da chave primária
     */
    protected $primaryKey = 'id';

    /**
     * Construtor da classe base
     */
    public function __construct()
    {
        $this->db = $this->getConnection();
    }

    /**
     * Obtém a conexão com o banco de dados
     * @return \PDO
     * @throws \Exception
     */
    private function getConnection(): \PDO
    {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '5432';
            $dbname = $_ENV['DB_DATABASE'] ?? 'ross';
            $username = $_ENV['DB_USERNAME'] ?? 'postgres';
            $password = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Busca um registro por ID
     * @param string $id ID do registro
     * @return array|null
     */
    public function findById(string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca todos os registros
     * @param array $conditions Condições WHERE
     * @param string $orderBy Campo para ordenação
     * @param string $direction Direção da ordenação (ASC/DESC)
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array
     */
    public function findAll(array $conditions = [], string $orderBy = null, string $direction = 'ASC', int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$direction}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Insere um novo registro
     * @param array $data Dados para inserção
     * @return string ID do registro inserido
     */
    public function create(array $data): string
    {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        // Tratar tipos de dados especiais
        $processedData = [];
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $processedData[$key] = $value ? 'true' : 'false';
            } else {
                $processedData[$key] = $value;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($processedData);
        
        // Para PostgreSQL, usar RETURNING id
        if ($this->db->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'pgsql') {
            $sqlWithReturning = $sql . " RETURNING {$this->primaryKey}";
            $stmt = $this->db->prepare($sqlWithReturning);
            $stmt->execute($processedData);
            $result = $stmt->fetch();
            return $result ? (string)$result[$this->primaryKey] : null;
        }
        
        $lastId = $this->db->lastInsertId();
        return $lastId ? (string)$lastId : null;
    }

    /**
     * Atualiza um registro
     * @param string $id ID do registro
     * @param array $data Dados para atualização
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = :{$field}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Remove um registro
     * @param string $id ID do registro
     * @return bool
     */
    public function delete(string $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    /**
     * Conta registros com condições
     * @param array $conditions Condições WHERE
     * @return int
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    /**
     * Executa uma consulta SQL personalizada
     * @param string $sql SQL da consulta
     * @param array $params Parâmetros da consulta
     * @return array
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Executa uma consulta SQL personalizada e retorna um único resultado
     * @param string $sql SQL da consulta
     * @param array $params Parâmetros da consulta
     * @return array|null
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Inicia uma transação
     * @return bool
     */
    protected function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirma uma transação
     * @return bool
     */
    protected function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Desfaz uma transação
     * @return bool
     */
    protected function rollback(): bool
    {
        return $this->db->rollback();
    }

    /**
     * Verifica se existem registros na tabela
     * @return bool
     */
    public function hasRecords(): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}