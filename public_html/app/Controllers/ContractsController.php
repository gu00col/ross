<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use App\Services\AuthService;
use App\Models\Contract;

class ContractsController
{
    private AuthService $authService;
    private Contract $contractModel;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->contractModel = new Contract();
    }
    
    /**
     * Lista de contratos (rota /contracts)
     */
    public function index(): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new \Laminas\Diactoros\Response\RedirectResponse('/login');
        }
        
        // Obter ID do usuário logado
        $userId = $_SESSION['user_id'] ?? null;
        
        // Parâmetros de paginação
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 30);
        $offset = ($page - 1) * $limit;
        
        // Filtros
        $filters = [
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Buscar contratos com paginação
        $contracts = $this->getContractsWithPagination($userId, $filters, $limit, $offset);
        $totalContracts = $this->getTotalContracts($userId, $filters);
        $totalPages = ceil($totalContracts / $limit);
        
        // Configurar dados da página
        $page_title = "Contratos";
        $page_subtitle = "Lista de contratos processados";
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => '/home'],
            ['title' => 'Contratos', 'url' => null]
        ];
        
        // CSS e JavaScript são carregados automaticamente baseado no controller
        // Nome do controller: 'contracts' -> assets/css/contracts.css e assets/js/contracts.js
        
        // Passar dados para o template
        $contracts = $contracts;
        $totalContracts = $totalContracts;
        $totalPages = $totalPages;
        $currentPage = $page;
        $limit = $limit;
        $filters = $filters;
        
        // Capturar o conteúdo do contracts.php
        ob_start();
        include __DIR__ . '/../../contracts.php';
        $contractsContent = ob_get_clean();
        
        // Extrair apenas o conteúdo principal (sem header/sidebar duplicados)
        $content = $this->extractMainContent($contractsContent);
        
        // Renderizar layout completo
        ob_start();
        include __DIR__ . '/../Views/layout.php';
        $layoutContent = ob_get_clean();
        
        return new HtmlResponse($layoutContent);
    }
    
    /**
     * Busca contratos com paginação
     */
    private function getContractsWithPagination(string $userId, array $filters, int $limit, int $offset): array
    {
        $sql = "SELECT id, original_filename, storage_path, analyzed_at, created_at, status, raw_text, user_id
                FROM contracts 
                WHERE user_id = :user_id";
        
        $params = ['user_id' => $userId];
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND original_filename ILIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->contractModel->executeQuery($sql, $params);
    }
    
    /**
     * Conta total de contratos para paginação
     */
    private function getTotalContracts(string $userId, array $filters): int
    {
        $sql = "SELECT COUNT(*) as total FROM contracts WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        
        // Aplicar filtros
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND original_filename ILIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $result = $this->contractModel->executeQueryOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Extrai apenas o conteúdo principal do contracts.php (sem header/sidebar duplicados)
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
