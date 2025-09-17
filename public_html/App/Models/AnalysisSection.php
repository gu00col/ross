<?php

namespace App\Models;

use MF\Model\Model;

/**
 * Model para gerenciar seções de análise
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class AnalysisSection extends Model
{
    /**
     * Obtém todas as seções ordenadas por display_order
     * 
     * @return array Lista de seções
     */
    public function getAllSections(): array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, display_order 
            FROM analysis_sections 
            ORDER BY display_order ASC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtém uma seção por ID
     * 
     * @param int $id ID da seção
     * @return array|null Dados da seção ou null se não encontrada
     */
    public function getSectionById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, display_order 
            FROM analysis_sections 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtém uma seção por título
     * 
     * @param string $title Título da seção
     * @return array|null Dados da seção ou null se não encontrada
     */
    public function getSectionByTitle(string $title): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, display_order 
            FROM analysis_sections 
            WHERE title = :title
        ");
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Cria uma nova seção
     * 
     * @param string $title Título da seção
     * @param int $displayOrder Ordem de exibição
     * @return int ID da seção criada
     */
    public function createSection(string $title, int $displayOrder): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO analysis_sections (title, display_order) 
            VALUES (:title, :display_order)
            RETURNING id
        ");
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $displayOrder, \PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['id'];
    }

    /**
     * Atualiza uma seção
     * 
     * @param int $id ID da seção
     * @param string $title Título da seção
     * @param int $displayOrder Ordem de exibição
     * @return int Número de linhas afetadas
     */
    public function updateSection(int $id, string $title, int $displayOrder): int
    {
        $stmt = $this->db->prepare("
            UPDATE analysis_sections 
            SET title = :title, display_order = :display_order 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':display_order', $displayOrder, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Remove uma seção
     * 
     * @param int $id ID da seção
     * @return int Número de linhas afetadas
     */
    public function deleteSection(int $id): int
    {
        $stmt = $this->db->prepare("DELETE FROM analysis_sections WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    /**
     * Reordena as seções
     * 
     * @param array $sectionOrders Array com [id => display_order]
     * @return bool True se sucesso, false caso contrário
     */
    public function reorderSections(array $sectionOrders): bool
    {
        try {
            $this->db->beginTransaction();
            
            foreach ($sectionOrders as $id => $displayOrder) {
                $stmt = $this->db->prepare("
                    UPDATE analysis_sections 
                    SET display_order = :display_order 
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
                $stmt->bindParam(':display_order', $displayOrder, \PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Obtém o próximo display_order disponível
     * 
     * @return int Próximo display_order
     */
    public function getNextDisplayOrder(): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(MAX(display_order), 0) + 1 as next_order 
            FROM analysis_sections
        ");
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['next_order'];
    }

    /**
     * Verifica se um título já existe
     * 
     * @param string $title Título a verificar
     * @param int|null $excludeId ID da seção a excluir da verificação
     * @return bool True se título existe, false caso contrário
     */
    public function titleExists(string $title, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM analysis_sections WHERE title = :title";
        $params = [':title' => $title];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se um display_order já existe
     * 
     * @param int $displayOrder Ordem a verificar
     * @param int|null $excludeId ID da seção a excluir da verificação
     * @return bool True se ordem existe, false caso contrário
     */
    public function displayOrderExists(int $displayOrder, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM analysis_sections WHERE display_order = :display_order";
        $params = [':display_order' => $displayOrder];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, \PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}
