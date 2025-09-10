<?php
/**
 * Model de Contrato
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

class Contract extends BaseModel
{
    protected $table = 'contracts';
    
    // Campos da tabela
    protected $fillable = [
        'id',
        'user_id',
        'original_filename',
        'storage_path',
        'raw_text',
        'text_embedding',
        'status',
        'analyzed_at',
        'created_at'
    ];
    
    // Status possíveis
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_ERROR = 'error';

    /**
     * Buscar todos os contratos
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "original_filename ILIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Buscar contrato por ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Criar novo contrato
     */
    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualizar contrato
     */
    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Deletar contrato
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Buscar contratos por status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, ['status' => $status]);
    }

    /**
     * Contar contratos por status
     */
    public function countByStatus($status)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = :status";
        $result = $this->db->fetch($sql, ['status' => $status]);
        return $result['count'] ?? 0;
    }

    /**
     * Obter estatísticas
     */
    public function getStats()
    {
        $stats = [];
        
        $stats['total'] = $this->db->fetch("SELECT COUNT(*) as count FROM {$this->table}")['count'];
        $stats['pending'] = $this->countByStatus('pending');
        $stats['processing'] = $this->countByStatus('processing');
        $stats['processed'] = $this->countByStatus('processed');
        $stats['error'] = $this->countByStatus('error');
        
        return $stats;
    }

    /**
     * Buscar contratos recentes
     */
    public function getRecent($limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Buscar contratos por usuário
     * 
     * @param string $userId ID do usuário
     * @param array $filters Filtros adicionais
     * @return array Lista de contratos do usuário
     */
    public function getByUser($userId, $filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        $conditions = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "original_filename ILIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " AND " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Contar contratos por usuário
     * 
     * @param string $userId ID do usuário
     * @param array $filters Filtros adicionais
     * @return int Número de contratos
     */
    public function countByUser($userId, $filters = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        $conditions = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($conditions)) {
            $sql .= " AND " . implode(' AND ', $conditions);
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Obter estatísticas por usuário
     * 
     * @param string $userId ID do usuário
     * @return array Estatísticas do usuário
     */
    public function getStatsByUser($userId)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processed,
                    COUNT(CASE WHEN status = 'error' THEN 1 END) as error
                FROM {$this->table} 
                WHERE user_id = :user_id";
        
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }

    /**
     * Busca semântica usando pgvector
     * 
     * @param array $embedding Array de números representando o embedding
     * @param int $limit Limite de resultados
     * @param float $threshold Limiar de similaridade (0-1)
     * @return array Contratos similares ordenados por similaridade
     */
    public function semanticSearch($embedding, $limit = 10, $threshold = 0.7)
    {
        if (empty($embedding) || !is_array($embedding)) {
            return [];
        }

        // Converter array para formato de vector do PostgreSQL
        $vectorString = '[' . implode(',', $embedding) . ']';
        
        $sql = "SELECT 
                    id, 
                    original_filename, 
                    status,
                    created_at,
                    1 - (text_embedding <=> :embedding::vector) as similarity
                FROM {$this->table} 
                WHERE text_embedding IS NOT NULL
                AND (1 - (text_embedding <=> :embedding::vector)) >= :threshold
                ORDER BY text_embedding <=> :embedding::vector
                LIMIT :limit";
                
        return $this->db->fetchAll($sql, [
            'embedding' => $vectorString,
            'threshold' => $threshold,
            'limit' => $limit
        ]);
    }

    /**
     * Atualizar embedding do contrato
     * 
     * @param string $id ID do contrato
     * @param array $embedding Array de números representando o embedding
     * @return bool Sucesso da operação
     */
    public function updateEmbedding($id, $embedding)
    {
        if (empty($embedding) || !is_array($embedding)) {
            return false;
        }

        $vectorString = '[' . implode(',', $embedding) . ']';
        
        $sql = "UPDATE {$this->table} 
                SET text_embedding = :embedding::vector 
                WHERE id = :id";
                
        return $this->db->execute($sql, [
            'embedding' => $vectorString,
            'id' => $id
        ]);
    }

    /**
     * Buscar contratos com resumo usando view
     * 
     * @param array $filters Filtros de busca
     * @return array Contratos com resumo
     */
    public function getWithSummary($filters = [])
    {
        $sql = "SELECT * FROM v_contracts_summary";
        $params = [];
        $conditions = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "original_filename ILIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obter análise completa de um contrato
     * 
     * @param string $contractId ID do contrato
     * @return array Análise completa
     */
    public function getFullAnalysis($contractId)
    {
        $sql = "SELECT * FROM v_contract_analysis WHERE contract_id = :contract_id";
        return $this->db->fetchAll($sql, ['contract_id' => $contractId]);
    }

    /**
     * Marcar contrato como processado
     * 
     * @param string $id ID do contrato
     * @return bool Sucesso da operação
     */
    public function markAsProcessed($id)
    {
        return $this->update($id, [
            'status' => self::STATUS_PROCESSED,
            'analyzed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Marcar contrato como erro
     * 
     * @param string $id ID do contrato
     * @param string $error Mensagem de erro
     * @return bool Sucesso da operação
     */
    public function markAsError($id, $error = null)
    {
        $data = ['status' => self::STATUS_ERROR];
        
        if ($error) {
            $data['error_message'] = $error;
        }
        
        return $this->update($id, $data);
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
            if ($field === 'id' && !$isUpdate) {
                continue; // ID é gerado automaticamente
            }
            
            if ($field === 'created_at' && !$isUpdate) {
                continue; // created_at é gerado automaticamente
            }
            
            if (isset($data[$field])) {
                $validated[$field] = $data[$field];
            }
        }
        
        // Validações específicas
        if (isset($validated['status']) && !in_array($validated['status'], [
            self::STATUS_PENDING, 
            self::STATUS_PROCESSING, 
            self::STATUS_PROCESSED, 
            self::STATUS_ERROR
        ])) {
            throw new \InvalidArgumentException('Status inválido');
        }
        
        if (isset($validated['original_filename']) && empty($validated['original_filename'])) {
            throw new \InvalidArgumentException('Nome do arquivo é obrigatório');
        }
        
        return $validated;
    }
}
