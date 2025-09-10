<?php
/**
 * Controller da Página Inicial
 * Sistema de Análise Contratual
 */

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    /**
     * Página inicial
     */
    public function index()
    {
        $data = [
            'title' => 'Sistema de Análise Contratual',
            'description' => 'Sistema automatizado de análise de contratos utilizando inteligência artificial',
            'features' => [
                [
                    'icon' => 'bi-file-earmark-text',
                    'title' => 'Upload de Contratos',
                    'description' => 'Envie contratos em PDF e receba análise completa em minutos'
                ],
                [
                    'icon' => 'bi-robot',
                    'title' => 'IA Avançada',
                    'description' => 'Análise inteligente com Google Gemini para identificar riscos e cláusulas'
                ],
                [
                    'icon' => 'bi-graph-up',
                    'title' => 'Relatórios Detalhados',
                    'description' => 'Relatórios completos com recomendações e pontos de atenção'
                ]
            ]
        ];

        $this->viewWithLayout('home/index', $data);
    }
}
