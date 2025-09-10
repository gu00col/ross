<?php
/**
 * Dashboard Principal
 * Página inicial após login
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

$page_title = "Dashboard - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header do Dashboard -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download me-1"></i>
                        Exportar
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-plus-circle me-1"></i>
                    Novo Contrato
                </button>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total de Contratos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-contracts">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Processados
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="processed-contracts">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pendentes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-contracts">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Este Mês
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthly-contracts">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    Carregando...
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-month fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Tabelas -->
        <div class="row">
            <!-- Gráfico de Contratos por Status -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Contratos por Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="contractsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contratos Recentes -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Contratos Recentes</h6>
                    </div>
                    <div class="card-body">
                        <div id="recent-contracts">
                            <div class="text-center py-3">
                                <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Carregando contratos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Contratos -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Todos os Contratos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="contractsTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nome do Arquivo</th>
                                        <th>Status</th>
                                        <th>Data de Upload</th>
                                        <th>Data de Análise</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="contractsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <i class="bi bi-hourglass-split me-2"></i>
                                            Carregando dados...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-upload me-2"></i>
                    Upload de Novo Contrato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="contractFile" class="form-label">Selecionar Arquivo PDF</label>
                        <input type="file" class="form-control" id="contractFile" name="contract" accept=".pdf" required>
                        <div class="form-text">Apenas arquivos PDF são aceitos. Tamanho máximo: 10MB</div>
                    </div>
                    <div class="mb-3">
                        <label for="contractDescription" class="form-label">Descrição (Opcional)</label>
                        <textarea class="form-control" id="contractDescription" name="description" rows="3" 
                                  placeholder="Adicione uma descrição ou observações sobre este contrato..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>
                        Enviar para Análise
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/dashboard.js"></script>

<?php include 'partials/footer.php'; ?>
