<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use Parsedown;

class ContratoController extends Action
{
    /**
     * Exibe os detalhes da análise de um contrato.
     *
     * @return void
     */
    public function index()
    {
        $this->validaAutenticacao();
        
        $contractId = $_GET['contrato_id'] ?? null;
        $userId = $_SESSION['id'];

        if (!$contractId) {
            header('Location: /contratos');
            exit;
        }

        $contractModel = Container::getModel('Contract');
        $analysisJson = $contractModel->getContractAnalysisJson($contractId, $userId);

        $groupedAnalysis = [];
        if ($analysisJson) {
            $analysisData = json_decode($analysisJson, true);
            foreach ($analysisData as $item) {
                $groupedAnalysis[$item['section_id']][] = $item;
            }
        }
        
        $this->view->groupedAnalysis = $groupedAnalysis;

        // Criar o Resumo Executivo
        $executiveSummary = [];
        // Pegar os 3 primeiros pontos de atenção
        $attentionPoints = array_slice($groupedAnalysis[2] ?? [], 0, 3);
        $executiveSummary['key_points'] = $attentionPoints;
        
        // Calcular o Nível de Risco com base na pontuação ponderada
        $highRiskCount = count($groupedAnalysis[2] ?? []);
        $mediumRiskCount = count($groupedAnalysis[3] ?? []);

        $totalRiskItems = $highRiskCount + $mediumRiskCount;

        // Pontuação atual: Alto = 3 pts, Médio = 2 pts
        $currentScore = ($highRiskCount * 3) + ($mediumRiskCount * 2);

        // Pontuação máxima possível (se todos os itens fossem de risco alto)
        $maxScore = $totalRiskItems > 0 ? $totalRiskItems * 3 : 0;
        
        // Porcentagem de risco para o gráfico
        $riskPercentage = $maxScore > 0 ? round(($currentScore / $maxScore) * 100) : 0;
        
        $this->view->riskPercentage = $riskPercentage;

        $this->view->executiveSummary = $executiveSummary;

        // Converter conteúdos de Markdown para HTML nas seções 2 e 3 (quando aplicável)
        $sectionsToMarkdown = [1, 2, 3];
        foreach ($sectionsToMarkdown as $sectionId) {
            if (!empty($groupedAnalysis[$sectionId])) {
                $parsedown = new Parsedown();
                $parsedown->setSafeMode(true);
                foreach ($groupedAnalysis[$sectionId] as $index => $item) {
                    $content = (string)($item['content'] ?? '');
                    $groupedAnalysis[$sectionId][$index]['contentHtml'] = $content !== '' ? $parsedown->text($content) : '';
                }
            }
        }
        $this->view->groupedAnalysis = $groupedAnalysis;
        // Atualiza a seção de informações gerais já com Markdown convertido
        $this->view->generalInfo = $groupedAnalysis[1] ?? [];

        // Converter o conteúdo da Seção 4 (Parecer Final) de Markdown para HTML
        if (isset($groupedAnalysis[4][0]['content'])) {
            $parsedown = new Parsedown();
            $this->view->finalReportHtml = $parsedown->text($groupedAnalysis[4][0]['content']);
        }

        $this->view->breadcrumb = [
            ['label' => 'Dashboard', 'link' => '/home'],
            ['label' => 'Meus Contratos', 'link' => '/contratos'],
            ['label' => 'Detalhe da Análise', 'active' => true]
        ];


        $this->view->sectionTitles = [
            0 => [
                'title' => 'Resumo Executivo',
                'description' => 'Dados fundamentais, principais pontos de atenção e nível de risco do contrato.',
                'class' => 'text-primary',
                'icon' => 'bi-clipboard-data'
            ],
            2 => [
                'title' => 'Cláusulas Leoninas',
                'description' => 'Cláusulas desequilibradas que podem favorecer excessivamente uma das partes.',
                'class' => 'text-danger',
                'icon' => 'bi-exclamation-triangle'
            ],
            3 => [
                'title' => 'Inconsistências e Ambiguidades',
                'description' => 'Problemas de redação e contradições internas no contrato.',
                'class' => 'text-warning',
                'icon' => 'bi-exclamation-circle'
            ],
            4 => [
                'title' => 'Parecer Final',
                'description' => 'Conclusão da análise e sugestões para mitigação de riscos.',
                'class' => 'text-primary',
                'icon' => 'bi-lightbulb'
            ]
        ];

        $this->view->active_page = 'contratos';
        $this->render('index', 'base');
    }

    /**
     * Valida se o usuário está autenticado.
     * Redireciona para a página de login caso não esteja.
     *
     * @return void
     */
    public function validaAutenticacao()
    {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /login?login=unauthorized');
            exit;
        }
    }
}
