<?php
/**
 * Model de Ponto de Análise
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

class AnalysisDataPoint extends BaseModel
{
    protected $table = 'analysis_data_points';
    
    // Campos da tabela
    protected $fillable = [
        'id',
        'contract_id',
        'section_id',
        'display_order',
        'label',
        'content',
        'details',
        'created_at'
    ];
    
    // IDs das seções
    const SECTION_ESSENTIAL_DATA = 1;    // Dados Essenciais
    const SECTION_RISKS = 2;             // Riscos e Cláusulas
    const SECTION_BREACHES = 3;          // Brechas e Inconsistências
    const SECTION_OPINION = 4;           // Parecer Final
    
    // Nomes das seções
    const SECTION_NAMES = [
        self::SECTION_ESSENTIAL_DATA => 'Dados Essenciais',
        self::SECTION_RISKS => 'Riscos e Cláusulas',
        self::SECTION_BREACHES => 'Brechas e Inconsistências',
        self::SECTION_OPINION => 'Parecer Final'
    ];

    /**
     * Buscar todos os pontos de análise
     * 
     * @param array $filters Filtros de busca
     * @return array Lista de pontos de análise
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $conditions = [];

        if (!empty($filters['contract_id'])) {
            $conditions[] = "contract_id = :contract_id";
            $params['contract_id'] = $filters['contract_id'];
        }

        if (!empty($filters['section_id'])) {
            $conditions[] = "section_id = :section_id";
            $params['section_id'] = $filters['section_id'];
        }

        if (!empty($filters['label'])) {
            $conditions[] = "label ILIKE :label";
            $params['label'] = '%' . $filters['label'] . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY section_id, display_order, created_at";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Buscar ponto de análise por ID
     * 
     * @param string $id ID do ponto de análise
     * @return array|null Dados do ponto de análise
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Criar novo ponto de análise
     * 
     * @param array $data Dados do ponto de análise
     * @return bool Sucesso da operação
     */
    public function create($data)
    {
        $validatedData = $this->validate($data);
        return $this->db->insert($this->table, $validatedData);
    }

    /**
     * Atualizar ponto de análise
     * 
     * @param string $id ID do ponto de análise
     * @param array $data Dados para atualizar
     * @return bool Sucesso da operação
     */
    public function update($id, $data)
    {
        $validatedData = $this->validate($data, true);
        return $this->db->update($this->table, $validatedData, 'id = :id', ['id' => $id]);
    }

    /**
     * Deletar ponto de análise
     * 
     * @param string $id ID do ponto de análise
     * @return bool Sucesso da operação
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Buscar pontos de análise por contrato
     * 
     * @param string $contractId ID do contrato
     * @return array Lista de pontos de análise
     */
    public function getByContract($contractId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE contract_id = :contract_id 
                ORDER BY section_id, display_order, created_at";
        return $this->db->fetchAll($sql, ['contract_id' => $contractId]);
    }

    /**
     * Buscar pontos de análise por seção
     * 
     * @param string $contractId ID do contrato
     * @param int $sectionId ID da seção
     * @return array Lista de pontos de análise
     */
    public function getBySection($contractId, $sectionId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE contract_id = :contract_id AND section_id = :section_id 
                ORDER BY display_order, created_at";
        return $this->db->fetchAll($sql, [
            'contract_id' => $contractId,
            'section_id' => $sectionId
        ]);
    }

    /**
     * Buscar pontos de análise agrupados por seção
     * 
     * @param string $contractId ID do contrato
     * @return array Pontos agrupados por seção
     */
    public function getGroupedBySection($contractId)
    {
        $points = $this->getByContract($contractId);
        $grouped = [];

        foreach ($points as $point) {
            $sectionId = $point['section_id'];
            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => self::SECTION_NAMES[$sectionId] ?? 'Desconhecido',
                    'points' => []
                ];
            }
            $grouped[$sectionId]['points'][] = $point;
        }

        return $grouped;
    }

    /**
     * Contar pontos por seção
     * 
     * @param string $contractId ID do contrato
     * @return array Contagem por seção
     */
    public function countBySection($contractId)
    {
        $sql = "SELECT 
                    section_id,
                    COUNT(*) as count
                FROM {$this->table} 
                WHERE contract_id = :contract_id 
                GROUP BY section_id
                ORDER BY section_id";
        
        $results = $this->db->fetchAll($sql, ['contract_id' => $contractId]);
        
        $counts = [];
        foreach ($results as $result) {
            $counts[$result['section_id']] = $result['count'];
        }
        
        return $counts;
    }

    /**
     * Obter estatísticas de análise
     * 
     * @param string $contractId ID do contrato
     * @return array Estatísticas
     */
    public function getStats($contractId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_points,
                    COUNT(CASE WHEN section_id = 1 THEN 1 END) as essential_data,
                    COUNT(CASE WHEN section_id = 2 THEN 1 END) as risks,
                    COUNT(CASE WHEN section_id = 3 THEN 1 END) as breaches,
                    COUNT(CASE WHEN section_id = 4 THEN 1 END) as opinion
                FROM {$this->table} 
                WHERE contract_id = :contract_id";
        
        return $this->db->fetch($sql, ['contract_id' => $contractId]);
    }

    /**
     * Buscar pontos com detalhes JSON
     * 
     * @param string $contractId ID do contrato
     * @param string $key Chave específica nos detalhes JSON
     * @return array Pontos com detalhes filtrados
     */
    public function getWithDetails($contractId, $key = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE contract_id = :contract_id";
        
        if ($key) {
            $sql .= " AND details ? :key";
        }
        
        $sql .= " ORDER BY section_id, display_order";
        
        $params = ['contract_id' => $contractId];
        if ($key) {
            $params['key'] = $key;
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Deletar todos os pontos de um contrato
     * 
     * @param string $contractId ID do contrato
     * @return bool Sucesso da operação
     */
    public function deleteByContract($contractId)
    {
        return $this->db->delete($this->table, 'contract_id = :contract_id', ['contract_id' => $contractId]);
    }

    /**
     * Criar múltiplos pontos de análise
     * 
     * @param array $points Array de pontos para inserir
     * @return bool Sucesso da operação
     */
    public function createMultiple($points)
    {
        if (empty($points) || !is_array($points)) {
            return false;
        }

        $this->db->beginTransaction();
        
        try {
            foreach ($points as $point) {
                $this->create($point);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Obter nome da seção
     * 
     * @param int $sectionId ID da seção
     * @return string Nome da seção
     */
    public static function getSectionName($sectionId)
    {
        return self::SECTION_NAMES[$sectionId] ?? 'Desconhecido';
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
        if (isset($validated['section_id']) && !in_array($validated['section_id'], [
            self::SECTION_ESSENTIAL_DATA,
            self::SECTION_RISKS,
            self::SECTION_BREACHES,
            self::SECTION_OPINION
        ])) {
            throw new \InvalidArgumentException('ID da seção inválido');
        }
        
        if (isset($validated['contract_id']) && empty($validated['contract_id'])) {
            throw new \InvalidArgumentException('ID do contrato é obrigatório');
        }
        
        if (isset($validated['label']) && empty($validated['label'])) {
            throw new \InvalidArgumentException('Label é obrigatório');
        }
        
        if (isset($validated['content']) && empty($validated['content'])) {
            throw new \InvalidArgumentException('Conteúdo é obrigatório');
        }
        
        // Validar JSON details
        if (isset($validated['details']) && !empty($validated['details'])) {
            if (is_string($validated['details'])) {
                $decoded = json_decode($validated['details'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Details deve ser um JSON válido');
                }
            } elseif (!is_array($validated['details'])) {
                throw new \InvalidArgumentException('Details deve ser um array ou JSON válido');
            }
        }
        
        return $validated;
    }
}
