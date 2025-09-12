<?php
/**
 * Lista de Contratos
 * Página para visualizar todos os contratos processados
 * Este arquivo é incluído pelo ContractsController
 */

// Carregar sistema de mensagens flash
require_once 'vendor/autoload.php';
$flashService = new \App\Services\FlashMessageService();
?>

<main class="main-content contracts-page">
    <div class="container-fluid">
        <!-- Mensagens flash -->
        <?php echo $flashService->render(); ?>
        
        <!-- Header -->
        <div class="d-flex justify-content-end flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contractUploadModal">
    <i class="fas fa-plus-circle me-2"></i>Novo Contrato
</button>




        </div>

        <!-- Filtros -->
        <div class="card shadow mb-4 filters-card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">Todos os Status</option>
                            <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendente</option>
                            <option value="processed" <?php echo ($filters['status'] ?? '') === 'processed' ? 'selected' : ''; ?>>Processado</option>
                            <option value="error" <?php echo ($filters['status'] ?? '') === 'error' ? 'selected' : ''; ?>>Erro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFrom" class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" id="dateFrom" name="date_from" value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="dateTo" class="form-label">Data Final</label>
                        <input type="date" class="form-control" id="dateTo" name="date_to" value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="searchTerm" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="searchTerm" name="search" placeholder="Nome do arquivo..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-filter">
                            <i class="fas fa-search me-1"></i>
                            Filtrar
                        </button>
                        <a href="/contracts" class="btn btn-outline-secondary btn-clear">
                            <i class="fas fa-times-circle me-1"></i>
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Contratos -->
        <div class="card shadow mb-4 contracts-table-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Lista de Contratos</h6>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Total: <?php echo $totalContracts; ?> contratos</span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome do Arquivo</th>
                                <th>Status</th>
                                <th>Data de Upload</th>
                                <th>Data de Análise</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contracts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 empty-state">
                                        <i class="fas fa-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Nenhum contrato encontrado</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contracts as $contract): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($contract['original_filename']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $statusConfig = [
                                                'pending' => ['class' => 'warning', 'text' => 'Pendente'],
                                                'processing' => ['class' => 'info', 'text' => 'Processando'],
                                                'processed' => ['class' => 'success', 'text' => 'Processado'],
                                                'error' => ['class' => 'danger', 'text' => 'Erro']
                                            ];
                                            $status = $statusConfig[$contract['status']] ?? ['class' => 'secondary', 'text' => $contract['status']];
                                            ?>
                                            <span class="badge bg-<?php echo $status['class']; ?>">
                                                <?php echo $status['text']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($contract['created_at'])); ?></td>
                                        <td>
                                            <?php echo $contract['analyzed_at'] ? date('d/m/Y H:i', strtotime($contract['analyzed_at'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <a href="/contract/<?php echo htmlspecialchars($contract['id']); ?>" 
                                               class="btn btn-icon" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-icon disabled" 
                                                    title="Reprocessar"
                                                    data-contract-id="<?php echo htmlspecialchars($contract['id']); ?>">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-icon disabled" 
                                                    title="Excluir"
                                                    data-contract-id="<?php echo htmlspecialchars($contract['id']); ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Navegação de contratos" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Página anterior -->
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage - 1])); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Páginas -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Página seguinte -->
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $currentPage + 1])); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="contractUploadModal" tabindex="-1" aria-labelledby="contractUploadModalLabel" aria-hidden="true">
    
    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contractUploadModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload de Novo Contrato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form id="contractUploadForm" action="/api/contracts/upload" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">

                    <div class="mb-3">
                        <label for="contractUploadFile" class="form-label">Selecionar Arquivo PDF <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="contractUploadFile" name="contrato" accept=".pdf" required>
                        <div class="form-text">Apenas arquivos no formato PDF são aceitos.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Enviar para Análise
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>

<!-- Modal de Upload -->
