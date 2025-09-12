<?php
/**
 * Minha Conta
 * Gerenciamento de dados do usuário
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

$page_title = "Minha Conta - Sistema de Análise Contratual";
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<main class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Minha Conta</h1>
        </div>

        <div class="row">
            <!-- Informações Pessoais -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-person me-2"></i>
                            Informações Pessoais
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="personalInfoForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" 
                                           value="<?php echo htmlspecialchars($_SESSION['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Sobrenome</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" 
                                           value="<?php echo htmlspecialchars($_SESSION['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="company" class="form-label">Empresa</label>
                                <input type="text" class="form-control" id="company" name="company" 
                                       value="<?php echo htmlspecialchars($_SESSION['company'] ?? ''); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Salvar Alterações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Alterar Senha -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-shield-lock me-2"></i>
                            Alterar Senha
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="passwordForm">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                <div class="form-text">
                                    A senha deve ter pelo menos 8 caracteres, incluindo letras e números.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-1"></i>
                                Alterar Senha
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar de Informações -->
            <div class="col-lg-4">
                <!-- Avatar e Status -->
                <div class="card shadow mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="assets/images/default-avatar.png" 
                                 alt="Avatar" 
                                 class="rounded-circle" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Usuário'); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                        <span class="badge bg-success">Conta Ativa</span>
                    </div>
                </div>

                <!-- Estatísticas da Conta -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-graph-up me-2"></i>
                            Estatísticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary" id="totalUploads">0</h4>
                                    <small class="text-muted">Contratos Enviados</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success" id="totalProcessed">0</h4>
                                <small class="text-muted">Processados</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Notificação -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-bell me-2"></i>
                            Notificações
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="notificationsForm">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" checked>
                                <label class="form-check-label" for="emailNotifications">
                                    E-mail quando análise for concluída
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="weeklyReport" name="weekly_report">
                                <label class="form-check-label" for="weeklyReport">
                                    Relatório semanal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="systemUpdates" name="system_updates" checked>
                                <label class="form-check-label" for="systemUpdates">
                                    Atualizações do sistema
                                </label>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-3">
                                <i class="bi bi-check me-1"></i>
                                Salvar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/my_account.js"></script>

<?php include 'partials/footer.php'; ?>
