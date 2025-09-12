<?php
/**
 * Serviço de Estatísticas
 * Sistema ROSS - Analista Jurídico
 */

namespace App\Services;

use App\Models\Contract;

class StatsService
{
    private Contract $contractModel;
    
    public function __construct()
    {
        $this->contractModel = new Contract();
    }
    
    /**
     * Obter estatísticas gerais de contratos
     * 
     * @param string $userId ID do usuário logado
     * @return array Estatísticas dos contratos
     */
    public function getContractStats(string $userId): array
    {
        try {
            // Total de contratos do usuário
            $totalContracts = $this->contractModel->countByUser($userId);
            
            // Contratos processados
            $processedContracts = $this->contractModel->countByUser($userId, ['status' => 'processed']);
            
            // Contratos pendentes
            $pendingContracts = $this->contractModel->countByUser($userId, ['status' => 'pending']);
            
            // Contratos em processamento
            $processingContracts = $this->contractModel->countByUser($userId, ['status' => 'processing']);
            
            // Contratos com erro
            $errorContracts = $this->contractModel->countByUser($userId, ['status' => 'error']);
            
            // Contratos deste mês
            $monthlyContracts = $this->getMonthlyContracts($userId);
            
            return [
                'total' => $totalContracts,
                'processed' => $processedContracts,
                'pending' => $pendingContracts,
                'processing' => $processingContracts,
                'error' => $errorContracts,
                'monthly' => $monthlyContracts
            ];
            
        } catch (\Exception $e) {
            // Em caso de erro, retornar valores padrão
            return [
                'total' => 0,
                'processed' => 0,
                'pending' => 0,
                'processing' => 0,
                'error' => 0,
                'monthly' => 0
            ];
        }
    }
    
    /**
     * Obter contratos do mês atual
     * 
     * @param string $userId ID do usuário
     * @return int Número de contratos do mês
     */
    private function getMonthlyContracts(string $userId): int
    {
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        
        return $this->contractModel->countByUser($userId, [
            'date_from' => $firstDayOfMonth,
            'date_to' => $lastDayOfMonth . ' 23:59:59'
        ]);
    }
    
    /**
     * Obter contratos recentes do usuário
     * 
     * @param string $userId ID do usuário
     * @param int $limit Limite de registros
     * @return array Lista de contratos recentes
     */
    public function getRecentContracts(string $userId, int $limit = 4): array
    {
        try {
            // Usar a consulta SQL específica fornecida
            $sql = "SELECT c.id, c.status, c.original_filename, c.created_at 
                    FROM contracts c 
                    WHERE c.user_id = :user_id 
                    ORDER BY c.created_at DESC 
                    LIMIT :limit";
            
            $contracts = $this->contractModel->executeQuery($sql, [
                'user_id' => $userId,
                'limit' => $limit
            ]);
            
            $formattedContracts = [];
            foreach ($contracts as $contract) {
                $formattedContracts[] = [
                    'id' => $contract['id'],
                    'uuid' => $contract['id'], // Usando ID como UUID
                    'status' => $contract['status'],
                    'original_filename' => $contract['original_filename'],
                    'created_at' => $contract['created_at']
                ];
            }
            
            return $formattedContracts;
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Obter dados para gráfico de status
     * 
     * @param string $userId ID do usuário
     * @return array Dados para o gráfico
     */
    public function getStatusChartData(string $userId): array
    {
        try {
            $stats = $this->getContractStats($userId);
            
            return [
                'labels' => ['Processados', 'Pendentes', 'Processando', 'Erro'],
                'data' => [
                    $stats['processed'],
                    $stats['pending'],
                    $stats['processing'],
                    $stats['error']
                ],
                'colors' => ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
            ];
            
        } catch (\Exception $e) {
            return [
                'labels' => ['Processados', 'Pendentes', 'Processando', 'Erro'],
                'data' => [0, 0, 0, 0],
                'colors' => ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
            ];
        }
    }
    
    /**
     * Obter contratos agrupados por status
     * 
     * @param string $userId ID do usuário
     * @return array Contratos agrupados por status
     */
    public function getContractsByStatus(string $userId): array
    {
        try {
            $contracts = $this->contractModel->getByUser($userId);
            
            // Agrupar contratos por status
            $groupedContracts = [
                'processed' => [],
                'pending' => [],
                'processing' => [],
                'error' => []
            ];
            
            foreach ($contracts as $contract) {
                $status = $contract['status'];
                if (isset($groupedContracts[$status])) {
                    $groupedContracts[$status][] = [
                        'id' => $contract['id'],
                        'uuid' => $contract['id'], // Usando ID como UUID
                        'filename' => $contract['original_filename'],
                        'status' => $contract['status'],
                        'created_at' => $contract['created_at'],
                        'analyzed_at' => $contract['analyzed_at']
                    ];
                }
            }
            
            return $groupedContracts;
            
        } catch (\Exception $e) {
            return [
                'processed' => [],
                'pending' => [],
                'processing' => [],
                'error' => []
            ];
        }
    }
}
