<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Services\AuthService;
use App\Models\Contract;

class ContractController
{
    private AuthService $authService;
    private Contract $contractModel;
    
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->contractModel = new Contract();
    }
    
    /**
     * Exibir detalhes de um contrato específico
     */
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        // Verificar se usuário está logado
        if (!$this->authService->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Obter ID do contrato da URL
        $contractId = $request->getAttribute('id');
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$contractId || !$userId) {
            return new RedirectResponse('/contracts');
        }
        
        // Buscar dados do contrato com análise
        $contractData = $this->getContractAnalysis($contractId, $userId);
        
        // Configurar dados da página
        $page_title = "Detalhes do Contrato";
        $page_subtitle = "Análise completa do contrato";
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => '/home'],
            ['title' => 'Contratos', 'url' => '/contracts'],
            ['title' => 'Detalhes', 'url' => null]
        ];
        
        // Os dados são processados diretamente no contract.php
        
        // Passar dados para o template
        $contractId = $contractId;
        $contractData = $contractData;
        
        // Capturar o conteúdo do contract.php
        ob_start();
        include __DIR__ . '/../../contract.php';
        $contractContent = ob_get_clean();
        
        // Extrair apenas o conteúdo principal (sem header/sidebar duplicados)
        $content = $this->extractMainContent($contractContent);
        
        // Renderizar layout completo
        ob_start();
        include __DIR__ . '/../Views/layout.php';
        $layoutContent = ob_get_clean();
        
        return new HtmlResponse($layoutContent);
    }
    
    /**
     * Buscar análise completa do contrato
     */
    private function getContractAnalysis(string $contractId, string $userId): array
    {
        $sql = "SELECT
            -- Inicia a agregação de todas as linhas resultantes em um único array JSON.
            jsonb_agg(
                -- Para cada linha, cria um objeto JSON com as chaves e valores especificados.
                jsonb_build_object(
                    'section_id', s.id, -- Usa o ID da seção.
                    'display_order', adp.display_order, -- Usa a ordem de exibição do item.
                    'label', adp.label, -- Usa o rótulo do item.
                    'content', adp.content, -- Usa o conteúdo principal do item.
                    'details', adp.details -- Usa o objeto JSON de detalhes.
                )
                -- Garante que o array seja ordenado primeiro pela ordem da seção, depois pela ordem do item.
                ORDER BY s.display_order, adp.display_order
            ) AS analysis_json
        FROM
            -- Começa pela tabela de contratos para filtrar pelo ID do contrato.
            public.contracts c
        -- Junta com os pontos de análise correspondentes ao contrato.
        JOIN
            public.analysis_data_points adp ON c.id = adp.contract_id
        -- Junta com as seções para obter a ordem de exibição correta das seções.
        JOIN
            public.analysis_sections s ON adp.section_id = s.id
        WHERE
            c.id = :contract_id AND c.user_id = :user_id";
        
        $params = [
            'contract_id' => $contractId,
            'user_id' => $userId
        ];
        
        $result = $this->contractModel->executeQueryOne($sql, $params);
        
        return $result ? $result : ['analysis_json' => null];
    }
    
    /**
     * Extrai apenas o conteúdo principal do contract.php (sem header/sidebar duplicados)
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
