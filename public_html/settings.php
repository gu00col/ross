<?php
/**
 * Configurações
 * Página de configurações do sistema
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

$page_title = "Configurações - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Configurações</h1>
        </div>

        <div class="row">
            <!-- Configurações Gerais -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-gear me-2"></i>
                            Configurações Gerais
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="generalSettingsForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="systemName" class="form-label">Nome do Sistema</label>
                                    <input type="text" class="form-control" id="systemName" name="system_name" 
                                           value="Sistema de Análise Contratual">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Fuso Horário</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        <option value="America/Sao_Paulo" selected>América/São_Paulo</option>
                                        <option value="America/New_York">América/Nova_York</option>
                                        <option value="Europe/London">Europa/Londres</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="language" class="form-label">Idioma</label>
                                <select class="form-select" id="language" name="language">
                                    <option value="pt-BR" selected>Português (Brasil)</option>
                                    <option value="en-US">English (US)</option>
                                    <option value="es-ES">Español</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Salvar Configurações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Configurações de Notificação -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-bell me-2"></i>
                            Notificações
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="notificationSettingsForm">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" checked>
                                <label class="form-check-label" for="emailNotifications">
                                    Receber notificações por e-mail
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="analysisComplete" name="analysis_complete" checked>
                                <label class="form-check-label" for="analysisComplete">
                                    Notificar quando análise for concluída
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="weeklyReport" name="weekly_report">
                                <label class="form-check-label" for="weeklyReport">
                                    Relatório semanal de atividades
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="systemUpdates" name="system_updates" checked>
                                <label class="form-check-label" for="systemUpdates">
                                    Atualizações do sistema
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Salvar Notificações
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar de Informações -->
            <div class="col-lg-4">
                <!-- Status do Sistema -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-activity me-2"></i>
                            Status do Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h4 class="text-success" id="systemStatus">Online</h4>
                                    <small class="text-muted">Sistema</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h4 class="text-info" id="databaseStatus">Conectado</h4>
                                <small class="text-muted">Banco de Dados</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning" id="redisStatus">Ativo</h4>
                                <small class="text-muted">Cache</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary" id="n8nStatus">Funcionando</h4>
                                <small class="text-muted">N8N</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações da Versão -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-info-circle me-2"></i>
                            Informações
                        </h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Versão:</dt>
                            <dd class="col-sm-7">1.0.0</dd>
                            
                            <dt class="col-sm-5">PHP:</dt>
                            <dd class="col-sm-7"><?php echo PHP_VERSION; ?></dd>
                            
                            <dt class="col-sm-5">Servidor:</dt>
                            <dd class="col-sm-7"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></dd>
                            
                            <dt class="col-sm-5">Última Atualização:</dt>
                            <dd class="col-sm-7"><?php echo date('d/m/Y'); ?></dd>
                        </dl>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-lightning me-2"></i>
                            Ações Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="clearCache()">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Limpar Cache
                            </button>
                            <button class="btn btn-outline-warning" onclick="exportSettings()">
                                <i class="bi bi-download me-1"></i>
                                Exportar Configurações
                            </button>
                            <button class="btn btn-outline-info" onclick="viewLogs()">
                                <i class="bi bi-file-text me-1"></i>
                                Ver Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/settings.js"></script>

<?php include 'partials/footer.php'; ?>
