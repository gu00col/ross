<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

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
        
        // Calcular o nível de risco
        $risk_level = 'Baixo';
        if (count($groupedAnalysis[2] ?? []) > 10) {
            $risk_level = 'Alto';
        } elseif (count($groupedAnalysis[2] ?? []) > 5) {
            $risk_level = 'Médio';
        }
        $executiveSummary['risk_level'] = $risk_level;

        $this->view->executiveSummary = $executiveSummary;
        $this->view->generalInfo = $groupedAnalysis[1] ?? [];


        $this->view->sectionTitles = [
            0 => [
                'title' => 'Resumo Executivo',
                'description' => 'Dados fundamentais, principais pontos de atenção e nível de risco do contrato.',
                'class' => 'text-primary',
                'icon' => 'bi-clipboard-data'
            ],
            2 => [
                'title' => 'Pontos de Atenção (Cláusulas Leoninas)',
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
                'title' => 'Parecer Final e Recomendações',
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
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}
