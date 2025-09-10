<?php
/**
 * Model Base
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected $table;
    protected $db;
    protected $fillable = [];
    protected $primaryKey = 'id';
    
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Buscar todos os registros
     * 
     * @param array $filters Filtros de busca
     * @param string $orderBy Campo para ordenação
     * @param string $orderDirection Direção da ordenação (ASC/DESC)
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de registros
     */
    public function getAll($filters = [], $orderBy = null, $orderDirection = 'ASC', $limit = null, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        // Aplicar filtros
        foreach ($filters as $field => $value) {
            if (in_array($field, $this->fillable) && $value !== null && $value !== '') {
                if (is_array($value)) {
                    $placeholders = [];
                    foreach ($value as $i => $v) {
                        $placeholder = $field . '_' . $i;
                        $placeholders[] = ':' . $placeholder;
                        $params[$placeholder] = $v;
                    }
                    $conditions[] = "{$field} IN (" . implode(',', $placeholders) . ")";
                } else {
                    $conditions[] = "{$field} = :{$field}";
                    $params[$field] = $value;
                }
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        // Ordenação
        if ($orderBy && in_array($orderBy, $this->fillable)) {
            $sql .= " ORDER BY {$orderBy} {$orderDirection}";
        }

        // Limite e offset
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Buscar registro por ID
     * 
     * @param mixed $id ID do registro
     * @return array|null Dados do registro
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Criar novo registro
     * 
     * @param array $data Dados do registro
     * @return bool Sucesso da operação
     */
    public function create($data)
    {
        $validatedData = $this->validate($data);
        return $this->db->insert($this->table, $validatedData);
    }

    /**
     * Atualizar registro
     * 
     * @param mixed $id ID do registro
     * @param array $data Dados para atualizar
     * @return bool Sucesso da operação
     */
    public function update($id, $data)
    {
        $validatedData = $this->validate($data, true);
        return $this->db->update($this->table, $validatedData, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    /**
     * Deletar registro
     * 
     * @param mixed $id ID do registro
     * @return bool Sucesso da operação
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    /**
     * Contar registros
     * 
     * @param array $filters Filtros de busca
     * @return int Número de registros
     */
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $conditions = [];

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->fillable) && $value !== null && $value !== '') {
                $conditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Verificar se registro existe
     * 
     * @param mixed $id ID do registro
     * @return bool Se existe
     */
    public function exists($id)
    {
        return $this->findById($id) !== null;
    }

    /**
     * Buscar primeiro registro que atenda aos critérios
     * 
     * @param array $filters Filtros de busca
     * @return array|null Primeiro registro encontrado
     */
    public function findFirst($filters = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        foreach ($filters as $field => $value) {
            if (in_array($field, $this->fillable) && $value !== null && $value !== '') {
                $conditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " LIMIT 1";

        return $this->db->fetch($sql, $params);
    }

    /**
     * Executar consulta SQL personalizada
     * 
     * @param string $sql SQL da consulta
     * @param array $params Parâmetros da consulta
     * @return array Resultado da consulta
     */
    public function query($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Executar comando SQL personalizado
     * 
     * @param string $sql SQL do comando
     * @param array $params Parâmetros do comando
     * @return bool Sucesso da operação
     */
    public function execute($sql, $params = [])
    {
        return $this->db->execute($sql, $params);
    }

    /**
     * Iniciar transação
     * 
     * @return bool Sucesso da operação
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirmar transação
     * 
     * @return bool Sucesso da operação
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Reverter transação
     * 
     * @return bool Sucesso da operação
     */
    public function rollback()
    {
        return $this->db->rollback();
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
        $validated = [];
        
        foreach ($this->fillable as $field) {
            if ($field === $this->primaryKey && !$isUpdate) {
                continue; // Chave primária é gerada automaticamente
            }
            
            if ($field === 'created_at' && !$isUpdate) {
                continue; // created_at é gerado automaticamente
            }
            
            if (isset($data[$field])) {
                $validated[$field] = $data[$field];
            }
        }
        
        return $validated;
    }

    /**
     * Obter nome da tabela
     * 
     * @return string Nome da tabela
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Obter campos preenchíveis
     * 
     * @return array Campos preenchíveis
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Obter chave primária
     * 
     * @return string Chave primária
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
