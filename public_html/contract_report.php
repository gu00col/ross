<?php
/**
 * Relatório do Contrato
 * Página para visualizar relatório completo de um contrato
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Verificar se foi fornecido ID do contrato
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /contracts');
    exit;
}

$contract_id = $_GET['id'];
$page_title = "Relatório do Contrato - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div>
                <h1 class="h2">Relatório do Contrato</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/home">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/contracts">Contratos</a></li>
                        <li class="breadcrumb-item"><a href="/contract/<?php echo htmlspecialchars($contract_id); ?>">Detalhes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Relatório</li>
                    </ol>
                </nav>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printReport()">
                        <i class="bi bi-printer me-1"></i>
                        Imprimir
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportPDF()">
                        <i class="bi bi-file-pdf me-1"></i>
                        Exportar PDF
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" onclick="shareReport()">
                    <i class="bi bi-share me-1"></i>
                    Compartilhar
                </button>
            </div>
        </div>

        <!-- Informações do Contrato -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Informações do Contrato
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nome do Arquivo:</strong><br>
                                <span id="contractFilename">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Data de Análise:</strong><br>
                                <span id="contractAnalysisDate">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                <span id="contractStatus" class="badge">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>ID do Contrato:</strong><br>
                                <code><?php echo htmlspecialchars($contract_id); ?></code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Executivo -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-graph-up me-2"></i>
                            Resumo Executivo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="border-end">
                                    <h3 class="text-primary" id="totalPoints">0</h3>
                                    <small class="text-muted">Pontos Analisados</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h3 class="text-warning" id="riskPoints">0</h3>
                                <small class="text-muted">Riscos Identificados</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h3 class="text-info" id="gapPoints">0</h3>
                                <small class="text-muted">Brechas Encontradas</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h3 class="text-success" id="recommendations">0</h3>
                                <small class="text-muted">Recomendações</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise Detalhada -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-search me-2"></i>
                            Análise Detalhada
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Dados Essenciais -->
                        <div class="mb-5">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                1. Dados Essenciais
                            </h5>
                            <div id="dadosEssenciais">
                                <div class="text-center py-4">
                                    <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Carregando dados essenciais...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Riscos e Cláusulas -->
                        <div class="mb-5">
                            <h5 class="text-warning mb-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                2. Riscos e Cláusulas Perigosas
                            </h5>
                            <div id="riscosClausulas">
                                <div class="text-center py-4">
                                    <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Carregando análise de riscos...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Brechas e Inconsistências -->
                        <div class="mb-5">
                            <h5 class="text-info mb-3">
                                <i class="bi bi-search me-2"></i>
                                3. Brechas e Inconsistências
                            </h5>
                            <div id="brechasInconsistencias">
                                <div class="text-center py-4">
                                    <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Carregando análise de brechas...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Parecer Final -->
                        <div class="mb-5">
                            <h5 class="text-success mb-3">
                                <i class="bi bi-file-text me-2"></i>
                                4. Parecer Final e Recomendações
                            </h5>
                            <div id="parecerFinal">
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

        <!-- Anexos -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-paperclip me-2"></i>
                            Anexos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <i class="bi bi-file-pdf fs-1 text-danger me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Contrato Original</h6>
                                        <small class="text-muted">Arquivo PDF do contrato</small>
                                        <br>
                                        <a href="#" id="originalContractLink" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-download me-1"></i>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <i class="bi bi-file-text fs-1 text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Relatório Completo</h6>
                                        <small class="text-muted">Este relatório em PDF</small>
                                        <br>
                                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="exportPDF()">
                                            <i class="bi bi-download me-1"></i>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript removido para evitar interferência com modais -->

<?php include 'partials/footer.php'; ?>
