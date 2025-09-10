<?php
/**
 * Detalhes do Contrato
 * Página para visualizar detalhes de um contrato específico
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar se foi fornecido ID do contrato
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: contracts.php');
    exit;
}

$contract_id = $_GET['id'];
$page_title = "Detalhes do Contrato - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div>
                <h1 class="h2">Detalhes do Contrato</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="contracts.php">Contratos</a></li>
                        <li class="breadcrumb-item active" aria-current="page" id="contractBreadcrumb">Carregando...</li>
                    </ol>
                </nav>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="downloadContract()">
                        <i class="bi bi-download me-1"></i>
                        Download
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="shareContract()">
                        <i class="bi bi-share me-1"></i>
                        Compartilhar
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="viewFullReport()">
                    <i class="bi bi-file-text me-1"></i>
                    Relatório Completo
                </button>
            </div>
        </div>

        <!-- Informações do Contrato -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-info-circle me-2"></i>
                            Informações do Contrato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Nome do Arquivo:</dt>
                                    <dd class="col-sm-8" id="contractFilename">-</dd>
                                    
                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8" id="contractStatus">-</dd>
                                    
                                    <dt class="col-sm-4">Data de Upload:</dt>
                                    <dd class="col-sm-8" id="contractUploadDate">-</dd>
                                    
                                    <dt class="col-sm-4">Data de Análise:</dt>
                                    <dd class="col-sm-8" id="contractAnalysisDate">-</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Tamanho:</dt>
                                    <dd class="col-sm-8" id="contractSize">-</dd>
                                    
                                    <dt class="col-sm-4">Páginas:</dt>
                                    <dd class="col-sm-8" id="contractPages">-</dd>
                                    
                                    <dt class="col-sm-4">ID do Contrato:</dt>
                                    <dd class="col-sm-8"><code id="contractId"><?php echo htmlspecialchars($contract_id); ?></code></dd>
                                    
                                    <dt class="col-sm-4">Link de Armazenamento:</dt>
                                    <dd class="col-sm-8">
                                        <a href="#" id="contractStorageLink" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>
                                            Ver no Google Drive
                                        </a>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-graph-up me-2"></i>
                            Estatísticas da Análise
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-primary" id="totalAnalysisPoints">0</h4>
                                    <small class="text-muted">Pontos de Análise</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-warning" id="riskPoints">0</h4>
                                <small class="text-muted">Riscos Identificados</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info" id="gapPoints">0</h4>
                                <small class="text-muted">Brechas Encontradas</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success" id="recommendations">0</h4>
                                <small class="text-muted">Recomendações</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise do Contrato -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-search me-2"></i>
                            Análise do Contrato
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Tabs de Análise -->
                        <ul class="nav nav-tabs" id="analysisTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Dados Essenciais
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="riscos-tab" data-bs-toggle="tab" data-bs-target="#riscos" type="button" role="tab">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Riscos e Cláusulas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="brechas-tab" data-bs-toggle="tab" data-bs-target="#brechas" type="button" role="tab">
                                    <i class="bi bi-search me-1"></i>
                                    Brechas e Inconsistências
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="parecer-tab" data-bs-toggle="tab" data-bs-target="#parecer" type="button" role="tab">
                                    <i class="bi bi-file-text me-1"></i>
                                    Parecer Final
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="analysisTabContent">
                            <!-- Dados Essenciais -->
                            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                                <div class="mt-3" id="dadosContent">
                                    <div class="text-center py-4">
                                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Carregando dados essenciais...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Riscos e Cláusulas -->
                            <div class="tab-pane fade" id="riscos" role="tabpanel">
                                <div class="mt-3" id="riscosContent">
                                    <div class="text-center py-4">
                                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Carregando análise de riscos...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Brechas e Inconsistências -->
                            <div class="tab-pane fade" id="brechas" role="tabpanel">
                                <div class="mt-3" id="brechasContent">
                                    <div class="text-center py-4">
                                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Carregando análise de brechas...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Parecer Final -->
                            <div class="tab-pane fade" id="parecer" role="tabpanel">
                                <div class="mt-3" id="parecerContent">
                                    <div class="text-center py-4">
                                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Carregando parecer final...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Texto Original do Contrato -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-file-text me-2"></i>
                            Texto Original do Contrato
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleRawText()">
                            <i class="bi bi-eye me-1"></i>
                            <span id="toggleTextBtn">Mostrar</span>
                        </button>
                    </div>
                    <div class="card-body" id="rawTextContainer" style="display: none;">
                        <div class="bg-light p-3 rounded">
                            <pre id="rawTextContent" class="mb-0" style="white-space: pre-wrap; font-size: 0.9rem;">Carregando texto original...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const contractId = '<?php echo htmlspecialchars($contract_id); ?>';
</script>
<script src="assets/js/contract.js"></script>

<?php include 'partials/footer.php'; ?>
