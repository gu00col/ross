<?php

namespace App\Models;

use MF\Model\Model;

/**
 * Model para gerenciar pontos de dados de análise
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class AnalysisDataPoint extends Model
{
    /**
     * Obtém todos os pontos de dados de um contrato
     * 
     * @param string $contractId ID do contrato
     * @return array Lista de pontos de dados
     */
    public function getDataPointsByContract(string $contractId): array
    {
        $stmt = $this->db->prepare("
            SELECT adp.*, asec.title as section_title, asec.display_order as section_order
            FROM analysis_data_points adp
            JOIN analysis_sections asec ON adp.section_id = asec.id
            WHERE adp.contract_id = :contract_id
            ORDER BY asec.display_order ASC, adp.display_order ASC
        ");
        $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém pontos de dados por seção
     * 
     * @param string $contractId ID do contrato
     * @param int $sectionId ID da seção
     * @return array Lista de pontos de dados da seção
     */
    public function getDataPointsBySection(string $contractId, int $sectionId): array
    {
        $stmt = $this->db->prepare("
            SELECT adp.*, asec.title as section_title
            FROM analysis_data_points adp
            JOIN analysis_sections asec ON adp.section_id = asec.id
            WHERE adp.contract_id = :contract_id AND adp.section_id = :section_id
            ORDER BY adp.display_order ASC
        ");
        $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
        $stmt->bindParam(':section_id', $sectionId, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém um ponto de dados por ID
     * 
     * @param string $id ID do ponto de dados
     * @return array|null Dados do ponto ou null se não encontrado
     */
    public function getDataPointById(string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT adp.*, asec.title as section_title, asec.display_order as section_order
            FROM analysis_data_points adp
            JOIN analysis_sections asec ON adp.section_id = asec.id
            WHERE adp.id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria um novo ponto de dados
     * 
     * @param string $id ID único do ponto de dados
     * @param string $contractId ID do contrato
     * @param int $sectionId ID da seção
     * @param string $label Rótulo do ponto de dados
     * @param string $content Conteúdo do ponto de dados
     * @param array|null $details Detalhes adicionais em JSON
     * @param int $displayOrder Ordem de exibição
     * @return string ID do ponto de dados criado
     */
    public function createDataPoint(string $id, string $contractId, int $sectionId, string $label, string $content, ?array $details = null, int $displayOrder = 0): string
    {
        $detailsJson = $details ? json_encode($details) : null;
        
        $stmt = $this->db->prepare("
            INSERT INTO analysis_data_points (id, contract_id, section_id, label, content, details, display_order) 
            VALUES (:id, :contract_id, :section_id, :label, :content, :details, :display_order)
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
        $stmt->bindParam(':section_id', $sectionId, \PDO::PARAM_INT);
        $stmt->bindParam(':label', $label, \PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->bindParam(':details', $detailsJson, \PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $displayOrder, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $id;
    }

    /**
     * Atualiza um ponto de dados
     * 
     * @param string $id ID do ponto de dados
     * @param string|null $label Rótulo do ponto de dados
     * @param string|null $content Conteúdo do ponto de dados
     * @param array|null $details Detalhes adicionais em JSON
     * @param int|null $displayOrder Ordem de exibição
     * @return int Número de linhas afetadas
     */
    public function updateDataPoint(string $id, ?string $label = null, ?string $content = null, ?array $details = null, ?int $displayOrder = null): int
    {
        $fields = [];
        $params = [':id' => $id];

        if ($label !== null) {
            $fields[] = "label = :label";
            $params[':label'] = $label;
        }
        if ($content !== null) {
            $fields[] = "content = :content";
            $params[':content'] = $content;
        }
        if ($details !== null) {
            $fields[] = "details = :details";
            $params[':details'] = json_encode($details);
        }
        if ($displayOrder !== null) {
            $fields[] = "display_order = :display_order";
            $params[':display_order'] = $displayOrder;
        }

        if (empty($fields)) {
            return 0;
        }

        $sql = "UPDATE analysis_data_points SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove um ponto de dados
     * 
     * @param string $id ID do ponto de dados
     * @return int Número de linhas afetadas
     */
    public function deleteDataPoint(string $id): int
    {
        $stmt = $this->db->prepare("DELETE FROM analysis_data_points WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove todos os pontos de dados de um contrato
     * 
     * @param string $contractId ID do contrato
     * @return int Número de linhas afetadas
     */
    public function deleteDataPointsByContract(string $contractId): int
    {
        $stmt = $this->db->prepare("DELETE FROM analysis_data_points WHERE contract_id = :contract_id");
        $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove todos os pontos de dados de uma seção
     * 
     * @param int $sectionId ID da seção
     * @return int Número de linhas afetadas
     */
    public function deleteDataPointsBySection(int $sectionId): int
    {
        $stmt = $this->db->prepare("DELETE FROM analysis_data_points WHERE section_id = :section_id");
        $stmt->bindParam(':section_id', $sectionId, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Obtém estatísticas dos pontos de dados
     * 
     * @param string|null $contractId ID do contrato (opcional)
     * @return array Estatísticas dos pontos de dados
     */
    public function getDataPointStats(?string $contractId = null): array
    {
        $sql = "SELECT COUNT(*) as total_points";
        $params = [];
        
        if ($contractId !== null) {
            $sql .= " FROM analysis_data_points WHERE contract_id = :contract_id";
            $params[':contract_id'] = $contractId;
        } else {
            $sql .= " FROM analysis_data_points";
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca pontos de dados por conteúdo
     * 
     * @param string $search Termo de busca
     * @param string|null $contractId ID do contrato (opcional)
     * @param int $limit Limite de registros
     * @param int $offset Offset para paginação
     * @return array Lista de pontos de dados encontrados
     */
    public function searchDataPoints(string $search, ?string $contractId = null, int $limit = 50, int $offset = 0): array
    {
        $searchTerm = '%' . $search . '%';
        $sql = "
            SELECT adp.*, asec.title as section_title, asec.display_order as section_order
            FROM analysis_data_points adp
            JOIN analysis_sections asec ON adp.section_id = asec.id
            WHERE (adp.label ILIKE :search OR adp.content ILIKE :search)
        ";
        $params = [':search' => $searchTerm];
        
        if ($contractId !== null) {
            $sql .= " AND adp.contract_id = :contract_id";
            $params[':contract_id'] = $contractId;
        }
        
        $sql .= " ORDER BY asec.display_order ASC, adp.display_order ASC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Reordena pontos de dados de uma seção
     * 
     * @param string $contractId ID do contrato
     * @param int $sectionId ID da seção
     * @param array $dataPointOrders Array com [id => display_order]
     * @return bool True se sucesso, false caso contrário
     */
    public function reorderDataPoints(string $contractId, int $sectionId, array $dataPointOrders): bool
    {
        try {
            $this->db->beginTransaction();
            
            foreach ($dataPointOrders as $id => $displayOrder) {
                $stmt = $this->db->prepare("
                    UPDATE analysis_data_points 
                    SET display_order = :display_order 
                    WHERE id = :id AND contract_id = :contract_id AND section_id = :section_id
                ");
                $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                $stmt->bindParam(':display_order', $displayOrder, \PDO::PARAM_INT);
                $stmt->bindParam(':contract_id', $contractId, \PDO::PARAM_STR);
                $stmt->bindParam(':section_id', $sectionId, \PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
