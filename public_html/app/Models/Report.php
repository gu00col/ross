<?php
/**
 * Model de Relatórios e Estatísticas
 * Sistema de Análise Contratual Automatizada
 * 
 * @package App\Models
 * @author Sistema Ross
 * @version 1.0.0
 */

namespace App\Models;

use App\Core\Database;

class Report
{
    protected $db;
    
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Obter dashboard principal
     * 
     * @return array Dados do dashboard
     */
    public function getDashboard()
    {
        $stats = $this->getGeneralStats();
        $recentContracts = $this->getRecentContracts(5);
        $monthlyTrends = $this->getMonthlyTrends(6);
        $statusDistribution = $this->getStatusDistribution();
        
        return [
            'stats' => $stats,
            'recent_contracts' => $recentContracts,
            'monthly_trends' => $monthlyTrends,
            'status_distribution' => $statusDistribution
        ];
    }

    /**
     * Obter estatísticas gerais
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
                    AVG(CASE WHEN status = 'processed' THEN total_analysis_points END) as media_pontos_analise,
                    MAX(created_at) as ultimo_contrato,
                    MIN(created_at) as primeiro_contrato
                FROM v_contracts_summary";
        
        return $this->db->fetch($sql);
    }

    /**
     * Obter distribuição por status
     * 
     * @return array Distribuição
     */
    public function getStatusDistribution()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as quantidade,
                    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentual
                FROM contracts 
                GROUP BY status 
                ORDER BY quantidade DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obter contratos recentes
     * 
     * @param int $limit Limite de resultados
     * @return array Contratos recentes
     */
    public function getRecentContracts($limit = 10)
    {
        $sql = "SELECT * FROM v_contracts_summary 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Obter tendências mensais
     * 
     * @param int $months Número de meses
     * @return array Tendências
     */
    public function getMonthlyTrends($months = 12)
    {
        $sql = "SELECT 
                    DATE_TRUNC('month', created_at) as month,
                    TO_CHAR(DATE_TRUNC('month', created_at), 'YYYY-MM') as month_label,
                    COUNT(*) as total_contratos,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processados,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pendentes,
                    COUNT(CASE WHEN status = 'error' THEN 1 END) as com_erro,
                    ROUND(AVG(total_analysis_points), 2) as media_pontos
                FROM v_contracts_summary 
                WHERE created_at >= NOW() - INTERVAL '{$months} months'
                GROUP BY DATE_TRUNC('month', created_at)
                ORDER BY month DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obter análise por seção
     * 
     * @return array Análise por seção
     */
    public function getAnalysisBySection()
    {
        $sql = "SELECT 
                    'Dados Essenciais' as section_name,
                    SUM(dados_essenciais) as total_pontos,
                    AVG(dados_essenciais) as media_pontos,
                    COUNT(CASE WHEN dados_essenciais > 0 THEN 1 END) as contratos_com_pontos
                FROM v_contracts_summary
                UNION ALL
                SELECT 
                    'Riscos e Cláusulas' as section_name,
                    SUM(riscos_clausulas) as total_pontos,
                    AVG(riscos_clausulas) as media_pontos,
                    COUNT(CASE WHEN riscos_clausulas > 0 THEN 1 END) as contratos_com_pontos
                FROM v_contracts_summary
                UNION ALL
                SELECT 
                    'Brechas e Inconsistências' as section_name,
                    SUM(brechas_inconsistencias) as total_pontos,
                    AVG(brechas_inconsistencias) as media_pontos,
                    COUNT(CASE WHEN brechas_inconsistencias > 0 THEN 1 END) as contratos_com_pontos
                FROM v_contracts_summary
                UNION ALL
                SELECT 
                    'Parecer Final' as section_name,
                    SUM(parecer_final) as total_pontos,
                    AVG(parecer_final) as media_pontos,
                    COUNT(CASE WHEN parecer_final > 0 THEN 1 END) as contratos_com_pontos
                FROM v_contracts_summary
                ORDER BY total_pontos DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obter performance de processamento
     * 
     * @param int $days Número de dias para análise
     * @return array Performance
     */
    public function getProcessingPerformance($days = 30)
    {
        $sql = "SELECT 
                    DATE(created_at) as data,
                    COUNT(*) as total_recebidos,
                    COUNT(CASE WHEN status = 'processed' THEN 1 END) as processados,
                    COUNT(CASE WHEN status = 'error' THEN 1 END) as com_erro,
                    ROUND(COUNT(CASE WHEN status = 'processed' THEN 1 END) * 100.0 / COUNT(*), 2) as taxa_sucesso
                FROM contracts 
                WHERE created_at >= NOW() - INTERVAL '{$days} days'
                GROUP BY DATE(created_at)
                ORDER BY data DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Obter contratos com problemas
     * 
     * @param int $maxPoints Máximo de pontos para considerar problema
     * @return array Contratos com problemas
     */
    public function getProblematicContracts($maxPoints = 5)
    {
        $sql = "SELECT 
                    id,
                    original_filename,
                    status,
                    total_analysis_points,
                    created_at,
                    analyzed_at
                FROM v_contracts_summary 
                WHERE total_analysis_points <= :max_points 
                AND status = 'processed'
                ORDER BY total_analysis_points ASC, created_at DESC";
        
        return $this->db->fetchAll($sql, ['max_points' => $maxPoints]);
    }

    /**
     * Obter top contratos mais analisados
     * 
     * @param int $limit Limite de resultados
     * @return array Top contratos
     */
    public function getTopAnalyzedContracts($limit = 10)
    {
        $sql = "SELECT 
                    id,
                    original_filename,
                    status,
                    total_analysis_points,
                    dados_essenciais,
                    riscos_clausulas,
                    brechas_inconsistencias,
                    parecer_final,
                    created_at
                FROM v_contracts_summary 
                WHERE status = 'processed'
                ORDER BY total_analysis_points DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Obter relatório de qualidade
     * 
     * @return array Relatório de qualidade
     */
    public function getQualityReport()
    {
        $sql = "SELECT 
                    COUNT(*) as total_contratos,
                    COUNT(CASE WHEN total_analysis_points >= 10 THEN 1 END) as alta_qualidade,
                    COUNT(CASE WHEN total_analysis_points BETWEEN 5 AND 9 THEN 1 END) as media_qualidade,
                    COUNT(CASE WHEN total_analysis_points < 5 THEN 1 END) as baixa_qualidade,
                    ROUND(AVG(total_analysis_points), 2) as media_geral,
                    ROUND(COUNT(CASE WHEN total_analysis_points >= 10 THEN 1 END) * 100.0 / COUNT(*), 2) as percentual_alta_qualidade
                FROM v_contracts_summary 
                WHERE status = 'processed'";
        
        return $this->db->fetch($sql);
    }

    /**
     * Obter relatório de tempo de processamento
     * 
     * @param int $days Número de dias para análise
     * @return array Relatório de tempo
     */
    public function getProcessingTimeReport($days = 30)
    {
        $sql = "SELECT 
                    id,
                    original_filename,
                    created_at,
                    analyzed_at,
                    EXTRACT(EPOCH FROM (analyzed_at - created_at)) / 3600 as horas_processamento
                FROM contracts 
                WHERE status = 'processed' 
                AND analyzed_at IS NOT NULL
                AND created_at >= NOW() - INTERVAL '{$days} days'
                ORDER BY horas_processamento DESC";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Exportar relatório completo
     * 
     * @param array $filters Filtros de busca
     * @return array Dados para exportação
     */
    public function exportFullReport($filters = [])
    {
        $contracts = $this->getContractsSummary($filters);
        $stats = $this->getGeneralStats();
        $monthlyTrends = $this->getMonthlyTrends(12);
        $qualityReport = $this->getQualityReport();
        
        return [
            'metadata' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'filters' => $filters,
                'total_contracts' => count($contracts)
            ],
            'summary' => $stats,
            'quality' => $qualityReport,
            'trends' => $monthlyTrends,
            'contracts' => $contracts
        ];
    }

    /**
     * Obter resumo de contratos com filtros
     * 
     * @param array $filters Filtros de busca
     * @return array Contratos filtrados
     */
    private function getContractsSummary($filters = [])
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

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
