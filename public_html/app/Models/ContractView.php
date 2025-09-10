<?php
/**
 * Model para Views de Contrato
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

class ContractView
{
    protected $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Buscar resumo de todos os contratos
     * 
     * @param array $filters Filtros de busca
     * @return array Lista de contratos com resumo
     */
    public function getContractsSummary($filters = [])
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
     * Buscar análise completa de um contrato
     * 
     * @param string $contractId ID do contrato
     * @return array Análise completa
     */
    public function getContractAnalysis($contractId)
    {
        $sql = "SELECT * FROM v_contract_analysis WHERE contract_id = :contract_id";
        return $this->db->fetchAll($sql, ['contract_id' => $contractId]);
    }

    /**
     * Buscar análise agrupada por seção
     * 
     * @param string $contractId ID do contrato
     * @return array Análise agrupada por seção
     */
    public function getGroupedAnalysis($contractId)
    {
        $analysis = $this->getContractAnalysis($contractId);
        $grouped = [];

        foreach ($analysis as $item) {
            $sectionId = $item['section_id'];
            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [
                    'section_id' => $sectionId,
                    'section_name' => $item['section_name'],
                    'points' => []
                ];
            }
            $grouped[$sectionId]['points'][] = $item;
        }

        return $grouped;
    }

    /**
     * Obter estatísticas gerais dos contratos
     * 
     * @return array Estatísticas
     */
    public function getGeneralStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_contratos,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processados,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pendentes,
                    COUNT(CASE WHEN status = 'processing' THEN 1 END) as processando,
                    COUNT(CASE WHEN status = 'error' THEN 1 END) as com_erro,
                    AVG(total_analysis_points) as media_pontos_analise
                FROM v_contracts_summary";
        
        return $this->db->fetch($sql);
    }

    /**
     * Buscar contratos por período
     * 
     * @param string $startDate Data inicial
     * @param string $endDate Data final
     * @return array Contratos do período
     */
    public function getByPeriod($startDate, $endDate)
    {
        $sql = "SELECT * FROM v_contracts_summary 
                WHERE created_at BETWEEN :start_date AND :end_date 
                ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Buscar contratos com mais pontos de análise
     * 
     * @param int $limit Limite de resultados
     * @return array Contratos com mais análise
     */
    public function getMostAnalyzed($limit = 10)
    {
        $sql = "SELECT * FROM v_contracts_summary 
                ORDER BY total_analysis_points DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Buscar contratos por seção específica
     * 
     * @param int $sectionId ID da seção
     * @param int $minPoints Mínimo de pontos na seção
     * @return array Contratos da seção
     */
    public function getBySection($sectionId, $minPoints = 1)
    {
        $columnMap = [
            1 => 'dados_essenciais',
            2 => 'riscos_clausulas',
            3 => 'brechas_inconsistencias',
            4 => 'parecer_final'
        ];

        if (!isset($columnMap[$sectionId])) {
            return [];
        }

        $column = $columnMap[$sectionId];
        $sql = "SELECT * FROM v_contracts_summary 
                WHERE {$column} >= :min_points 
                ORDER BY {$column} DESC, created_at DESC";
        
        return $this->db->fetchAll($sql, ['min_points' => $minPoints]);
    }

    /**
     * Buscar contratos com problemas (poucos pontos de análise)
     * 
     * @param int $maxPoints Máximo de pontos para considerar problema
     * @return array Contratos com problemas
     */
    public function getWithProblems($maxPoints = 5)
    {
        $sql = "SELECT * FROM v_contracts_summary 
                WHERE total_analysis_points <= :max_points 
                AND status = 'processed'
                ORDER BY total_analysis_points ASC, created_at DESC";
        
        return $this->db->fetchAll($sql, ['max_points' => $maxPoints]);
    }

    /**
     * Buscar tendências por mês
     * 
     * @param int $months Número de meses para análise
     * @return array Tendências mensais
     */
    public function getMonthlyTrends($months = 12)
    {
        $sql = "SELECT 
                    DATE_TRUNC('month', created_at) as month,
                    COUNT(*) as total_contratos,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processados,
                    AVG(total_analysis_points) as media_pontos
                FROM v_contracts_summary 
                WHERE created_at >= NOW() - INTERVAL '{$months} months'
                GROUP BY DATE_TRUNC('month', created_at)
                ORDER BY month DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Buscar contratos similares por nome de arquivo
     * 
     * @param string $filename Nome do arquivo
     * @param int $limit Limite de resultados
     * @return array Contratos similares
     */
    public function getSimilarByFilename($filename, $limit = 5)
    {
        $sql = "SELECT * FROM v_contracts_summary 
                WHERE original_filename ILIKE :pattern 
                AND id != :exclude_id
                ORDER BY similarity(original_filename, :filename) DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'pattern' => '%' . $filename . '%',
            'exclude_id' => 'exclude', // Será substituído na implementação
            'filename' => $filename,
            'limit' => $limit
        ]);
    }

    /**
     * Exportar dados para relatório
     * 
     * @param array $filters Filtros de busca
     * @return array Dados para exportação
     */
    public function exportData($filters = [])
    {
        $contracts = $this->getContractsSummary($filters);
        $exportData = [];

        foreach ($contracts as $contract) {
            $analysis = $this->getContractAnalysis($contract['id']);
            
            $exportData[] = [
                'contract' => $contract,
                'analysis' => $analysis
            ];
        }

        return $exportData;
    }
}
