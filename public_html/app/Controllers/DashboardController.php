<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use App\Services\AuthService;
use App\Services\StatsService;

class DashboardController
{
    private AuthService $authService;
    private StatsService $statsService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->statsService = new StatsService();
    }
    
    /**
     * Página inicial do sistema (rota /home)
     */
    public function home(): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new \Laminas\Diactoros\Response\RedirectResponse('/login');
        }
        
        // Obter ID do usuário logado
        $userId = $_SESSION['user_id'] ?? null;
        
        // Buscar estatísticas dos contratos
        $stats = $this->statsService->getContractStats($userId);
        $recentContracts = $this->statsService->getRecentContracts($userId, 6);
        $chartData = $this->statsService->getStatusChartData($userId);
        
        // Buscar contratos por status para o usuário logado
        $contractsByStatus = $this->statsService->getContractsByStatus($userId);
        
        // Configurar dados da página
        $page_title = "Dashboard";
        $page_subtitle = "Bem-vindo ao sistema ROSS";
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => null]
        ];
        
        // Variáveis para o layout
        $user_name = $_SESSION['user_name'] ?? 'Usuário';
        $user_email = $_SESSION['user_email'] ?? '';
        $current_time = date('d/m/Y H:i');
        $total_contracts = $stats['total'] ?? 0;
        
        // Não incluir JavaScript - dados são processados no PHP
        
        // Passar dados para o template home.php
        $contractsByStatus = $contractsByStatus;
        $recentContracts = $recentContracts;
        $stats = $stats;
        $chartData = $chartData;
        
        // Capturar o conteúdo do home.php
        ob_start();
        include __DIR__ . '/../../home.php';
        $homeContent = ob_get_clean();
        
        // Extrair apenas o conteúdo principal (sem header/sidebar duplicados)
        $content = $this->extractMainContent($homeContent);
        
        // Renderizar layout completo
        ob_start();
        include __DIR__ . '/../Views/layout.php';
        $layoutContent = ob_get_clean();
        
        return new HtmlResponse($layoutContent);
    }
    
    /**
     * Extrai apenas o conteúdo principal do home.php (sem header/sidebar duplicados)
     */
    private function extractMainContent(string $htmlContent): string
    {
        // Encontrar o conteúdo entre <main class="main-content"> e </main>
        if (preg_match('/<main[^>]*class="main-content[^"]*"[^>]*>(.*?)<\/main>/s', $htmlContent, $matches)) {
            return $matches[1];
        }
        
        // Se não encontrar, retornar o conteúdo original
        return $htmlContent;
    }
    
}