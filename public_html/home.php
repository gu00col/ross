<?php
/**
 * Dashboard Principal
 * Página inicial após login
 * Este arquivo é incluído pelo DashboardController
 */
?>

<main class="main-content dashboard-page">
    <div class="container-fluid">
        <!-- Header do Dashboard -->
        <div class="d-flex justify-content-end flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                        <div class="btn-toolbar mb-2 mb-md-0">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="">
                    <i class="fas fa-plus-circle me-1"></i>
                    Novo Contrato
                </button>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-4 shadow h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-uppercase text-primary fw-bold small mb-1">
                                    Total de Contratos
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark" id="total-contracts">
                                    <?php echo $stats['total'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4 shadow h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-uppercase text-success fw-bold small mb-1">
                                    Processados
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark" id="processed-contracts">
                                    <?php echo $stats['processed'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 shadow h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-uppercase text-warning fw-bold small mb-1">
                                    Pendentes
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark" id="pending-contracts">
                                    <?php echo $stats['pending'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4 shadow h-100">
                    <div class="card-body">
                        <div class="row g-0 align-items-center">
                            <div class="col me-2">
                                <div class="text-uppercase text-info fw-bold small mb-1">
                                    Este Mês
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark" id="monthly-contracts">
                                    <?php echo $stats['monthly'] ?? 0; ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Tabelas -->
        <div class="row">
            <!-- Lista de Contratos por Status -->
            <!-- Contratos Recentes -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary">Contratos Recentes</h6>
                    </div>
                    <div class="card-body">
                        <div id="recent-contracts">
                        <?php if (empty($recentContracts)): ?>
    <div class="text-center py-5">
        <i class="fas fa-inbox fs-1 text-muted"></i>
        <p class="text-muted mt-2">Nenhum contrato encontrado</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <!-- <thead class="table-light">
                <tr>
                    <th scope="col">Nome</th>
                    <th scope="col">Status</th>
                </tr>
            </thead> -->
            <tbody>
                <?php foreach ($recentContracts as $contract): ?>
                    <tr>
                        <td>
                            <a href="/contract/<?php echo htmlspecialchars($contract['uuid']); ?>" class="text-decoration-none fw-bold">
                                <?php echo htmlspecialchars($contract['original_filename']); ?>
                            </a>
                        </td>
                        <td>
                            <?php 
                            $statusConfig = [
                                'processed' => ['text' => 'Processado', 'class' => 'bg-success'],
                                'pending' => ['text' => 'Pendente', 'class' => 'bg-warning'],
                                'processing' => ['text' => 'Processando', 'class' => 'bg-info'],
                                'error' => ['text' => 'Erro', 'class' => 'bg-danger']
                            ];
                            $currentStatus = $contract['status'];
                            $displayText = htmlspecialchars($statusConfig[$currentStatus]['text'] ?? $currentStatus);
                            $displayClass = $statusConfig[$currentStatus]['class'] ?? 'bg-secondary';
                            
                            echo "<span class='badge {$displayClass}'>{$displayText}</span>";
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>



<script src="assets/js/dashboard.js"></script>
