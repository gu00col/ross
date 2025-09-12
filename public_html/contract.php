<?php
/**
 * Detalhes do Contrato
 * Página para visualizar detalhes de um contrato específico
 * Este arquivo é incluído pelo ContractController
 */

// Preparar dados para renderização
$analysisData = [];
if (isset($contractData['analysis_json'])) {
    // Se analysis_json é uma string JSON, decodificar
    if (is_string($contractData['analysis_json'])) {
        $decoded = json_decode($contractData['analysis_json'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $analysisData = $decoded;
        }
    }
    // Se analysis_json já é um array
    elseif (is_array($contractData['analysis_json'])) {
        $analysisData = $contractData['analysis_json'];
    }
}

// Separar dados por seção
$basicInfo = array_filter($analysisData, function($item) {
    return isset($item['section_id']) && $item['section_id'] == 1;
});

$leonineClauses = array_filter($analysisData, function($item) {
    return isset($item['section_id']) && $item['section_id'] == 2;
});

$inconsistencies = array_filter($analysisData, function($item) {
    return isset($item['section_id']) && $item['section_id'] == 3;
});

$recommendations = array_filter($analysisData, function($item) {
    return isset($item['section_id']) && $item['section_id'] == 4;
});

// Calcular estatísticas
$attentionClausesCount = count($leonineClauses);
$inconsistenciesCount = count($inconsistencies);

// Contar recomendações (linhas que começam com número)
$recommendationsCount = 0;
foreach ($recommendations as $item) {
    if (isset($item['content'])) {
        $lines = explode("\n", $item['content']);
        foreach ($lines as $line) {
            if (preg_match('/^\s*\d+\.\s/', $line)) {
                $recommendationsCount++;
            }
        }
    }
}

/**
 * Função para formatar conteúdo
 */
function formatContent($content) {
    if (!$content) return '';
    
    // Converter quebras de linha em <br>
    $formatted = str_replace("\n", '<br>', $content);
    
    // Destacar texto em negrito
    $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
    
    // Destacar texto em itálico
    $formatted = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formatted);
    
    // Destacar cláusulas específicas
    $formatted = preg_replace('/(Cláusula \d+[\.\d]*)/', '<span class="badge bg-light text-dark">$1</span>', $formatted);
    
    return $formatted;
}

/**
 * Função para formatar conteúdo de recomendações
 */
function formatRecommendationContent($content) {
    if (!$content) return '';
    
    // Dividir por quebras de linha duplas
    $sections = preg_split('/\n\s*\n/', $content);
    
    $html = '';
    foreach ($sections as $section) {
        $section = trim($section);
        if (!$section) continue;
        
        // Verificar se é uma lista numerada
        if (preg_match('/^\d+\./', $section)) {
            $listItems = preg_split('/\n(?=\d+\.)/', $section);
            $html .= '<ol class="list-group list-group-numbered mb-3">';
            foreach ($listItems as $item) {
                $item = trim($item);
                if ($item) {
                    $cleanItem = preg_replace('/^\d+\.\s*/', '', $item);
                    $html .= '<li class="list-group-item">' . formatContent($cleanItem) . '</li>';
                }
            }
            $html .= '</ol>';
        } else {
            $html .= '<p class="mb-3">' . formatContent($section) . '</p>';
        }
    }
    
    return $html;
}

/**
 * Função para criar seção de detalhes
 */
function createDetailsSection($details) {
    if (!is_array($details) || empty($details)) return '';
    
    $html = '<div class="mt-3 p-3 bg-light rounded">';
    $html .= '<h6 class="text-primary mb-3 fw-bold">Detalhes Adicionais</h6>';
    
    foreach ($details as $key => $value) {
        if ($value && trim($value)) {
            $html .= '<div class="mb-3">';
            $html .= '<h6 class="text-uppercase fw-bold text-muted small">' . htmlspecialchars($key) . '</h6>';
            $html .= '<p class="mb-0 small">' . formatContent($value) . '</p>';
            $html .= '</div>';
        }
    }
    
    $html .= '</div>';
    return $html;
}
?>

<div class="container-fluid py-4">
    <!-- Hero Section Simplificado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body bg-primary text-dark rounded">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                          
                            <p class="card-text h2 text-light">Resumo dos pontos identificados na análise jurídica</p>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="bg-beige bg-opacity-20 rounded p-1">
                                        <h3 class="mb-1 text-dark"><?php echo $attentionClausesCount; ?></h3>
                                        <small class="text-dark-50">Pontos de Atenção</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-beige bg-opacity-20 rounded p-1">
                                        <h3 class="mb-1 text-dark"><?php echo $inconsistenciesCount; ?></h3>
                                        <small class="text-dark-50">Inconsistências</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-beige bg-opacity-20 rounded p-1">
                                        <h3 class="mb-1 text-dark"><?php echo $recommendationsCount; ?></h3>
                                        <small class="text-dark-50">Recomendações</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($analysisData)): ?>
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                    <h5 class="mt-3">Nenhuma análise encontrada</h5>
                    <p class="text-muted">Este contrato ainda não possui dados de análise ou não foi encontrado.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Informações Básicas -->
        <?php if (!empty($basicInfo)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-primary mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Informações Básicas
                </h4>
                <div class="row">
                    <?php foreach ($basicInfo as $item): ?>
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">Básico</span>
                                        <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo formatContent($item['content']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pontos de Atenção -->
        <?php if (!empty($leonineClauses)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Pontos de Atenção
                </h4>
                <div class="row">
                    <?php foreach ($leonineClauses as $item): ?>
                        <?php $hasDetails = isset($item['details']) && is_array($item['details']) && !empty($item['details']); ?>
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-white text-danger me-2">Atenção</span>
                                        <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo formatContent($item['content']); ?></p>
                                    <?php if ($hasDetails): ?>
                                        <?php echo createDetailsSection($item['details']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Inconsistências -->
        <?php if (!empty($inconsistencies)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-warning mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Inconsistências e Ambiguidades
                </h4>
                <div class="row">
                    <?php foreach ($inconsistencies as $item): ?>
                        <?php $hasDetails = isset($item['details']) && is_array($item['details']) && !empty($item['details']); ?>
                        <div class="col-lg-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-dark text-white me-2">Inconsistência</span>
                                        <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo formatContent($item['content']); ?></p>
                                    <?php if ($hasDetails): ?>
                                        <?php echo createDetailsSection($item['details']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recomendações -->
        <?php if (!empty($recommendations)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="text-success mb-3">
                    <i class="bi bi-lightbulb me-2"></i>
                    Recomendações e Parecer Final
                </h4>
                <div class="row">
                    <?php foreach ($recommendations as $item): ?>
                        <div class="col-12 mb-5">
                            <div class="card mb-5">
                                <div class="card-header bg-success text-dark">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-beige text-success me-2">Recomendação</span>
                                        <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="recommendation-content">
                                        <?php echo formatRecommendationContent($item['content']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
