<?php
/**
 * Controller para APIs
 * Sistema ROSS - Analista Jurídico
 */

namespace App\Controllers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use App\Services\AuthService;
use App\Services\StatsService;

class ApiController
{
    private AuthService $authService;
    private StatsService $statsService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->statsService = new StatsService();
    }
    
    /**
     * Endpoint para buscar estatísticas de contratos
     */
    public function getContractStats(): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new JsonResponse(['error' => 'Não autorizado'], 401);
        }
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                return new JsonResponse(['error' => 'ID do usuário não encontrado'], 400);
            }
            
            $stats = $this->statsService->getContractStats($userId);
            $recentContracts = $this->statsService->getRecentContracts($userId, 5);
            $chartData = $this->statsService->getStatusChartData($userId);
            
            return new JsonResponse([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_contracts' => $recentContracts,
                    'chart_data' => $chartData
                ]
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erro ao buscar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Endpoint para buscar contratos recentes
     */
    public function getRecentContracts(): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new JsonResponse(['error' => 'Não autorizado'], 401);
        }
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $limit = $_GET['limit'] ?? 5;
            
            if (!$userId) {
                return new JsonResponse(['error' => 'ID do usuário não encontrado'], 400);
            }
            
            $contracts = $this->statsService->getRecentContracts($userId, (int)$limit);
            
            return new JsonResponse([
                'success' => true,
                'data' => $contracts
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erro ao buscar contratos: ' . $e->getMessage()
            ], 500);
        }
    }
}
