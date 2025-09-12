<?php
/**
 * Classe de Conexão com Banco de Dados
 * Sistema de Análise Contratual
 */

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private $connection;
    private $config;

    public function __construct()
    {
        $this->config = config('database');
        $this->connect();
    }

    /**
     * Conectar ao banco de dados
     */
    private function connect()
    {
        try {
            $config = $this->config['connections'][$this->config['default']];
            
            $dsn = $this->buildDsn($config);
            
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? []
            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new \Exception('Erro ao conectar com o banco de dados: ' . $e->getMessage());
        }
    }

    /**
     * Construir DSN
     */
    private function buildDsn($config)
    {
        switch ($config['driver']) {
            case 'pgsql':
                return "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']};sslmode={$config['sslmode']}";
            case 'mysql':
                return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            default:
                throw new \Exception('Driver de banco não suportado: ' . $config['driver']);
        }
    }

    /**
     * Obter conexão
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Executar query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception('Erro ao executar query: ' . $e->getMessage());
        }
    }

    /**
     * Buscar um registro
     */
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Buscar todos os registros
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Inserir registro
     */
    public function insert($table, $data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }

    /**
     * Atualizar registro
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params);
    }

    /**
     * Deletar registro
     */
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params);
    }

    /**
     * Iniciar transação
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirmar transação
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Reverter transação
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }

    /**
     * Verificar se está em transação
     */
    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }
}
