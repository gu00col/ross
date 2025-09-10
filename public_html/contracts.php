<?php
/**
 * Lista de Contratos
 * Página para visualizar todos os contratos processados
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

$page_title = "Contratos - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Contratos</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportContracts()">
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

        <!-- Filtros -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form id="filtersForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">Todos os Status</option>
                            <option value="pending">Pendente</option>
                            <option value="processing">Processando</option>
                            <option value="processed">Processado</option>
                            <option value="error">Erro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFrom" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="dateFrom" name="date_from">
                    </div>
                    <div class="col-md-3">
                        <label for="dateTo" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="dateTo" name="date_to">
                    </div>
                    <div class="col-md-3">
                        <label for="searchTerm" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="searchTerm" name="search" placeholder="Nome do arquivo...">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-1"></i>
                            Limpar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Contratos -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Contratos</h6>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3" id="contractsCount">Carregando...</span>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="viewMode" id="tableView" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="tableView">
                            <i class="bi bi-table"></i>
                        </label>
                        <input type="radio" class="btn-check" name="viewMode" id="cardView" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="cardView">
                            <i class="bi bi-grid"></i>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabela -->
                <div id="tableViewContent">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="contractsTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Arquivo</th>
                                    <th>Status</th>
                                    <th>Data de Upload</th>
                                    <th>Data de Análise</th>
                                    <th>Tamanho</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="contractsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Carregando contratos...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Cards -->
                <div id="cardViewContent" style="display: none;">
                    <div class="row" id="contractsCards">
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Carregando contratos...</p>
                        </div>
                    </div>
                </div>

                <!-- Paginação -->
                <nav aria-label="Navegação de contratos" class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Paginação será inserida via JavaScript -->
                    </ul>
                </nav>
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

<!-- Modal de Detalhes -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Detalhes do Contrato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Conteúdo será inserido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Fechar
                </button>
                <button type="button" class="btn btn-primary" onclick="viewFullReport()">
                    <i class="bi bi-file-text me-1"></i>
                    Ver Relatório Completo
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/contracts.js"></script>

<?php include 'partials/footer.php'; ?>
