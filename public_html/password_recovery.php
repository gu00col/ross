<?php
/**
 * Recuperação de Senha
 * Página para solicitar redefinição de senha
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário já está logado
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$page_title = "Recuperar Senha - Sistema de Análise Contratual";
include 'partials/header.php';
?>

<main class="password-recovery-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.svg" 
                                 alt="Logo" 
                                 class="mb-3" 
                                 style="height: 60px;">
                            <h3 class="fw-bold text-primary">Recuperar Senha</h3>
                            <p class="text-muted">Digite seu e-mail para receber instruções de redefinição</p>
                        </div>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <form action="partials/auth/password_recovery_process.php" method="POST" novalidate>
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>
                                    E-mail
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required
                                       placeholder="Digite seu e-mail cadastrado"
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="invalid-feedback">
                                    Por favor, insira um e-mail válido.
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>
                                    Enviar Instruções
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Voltar ao Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        Lembrou da senha? 
                        <a href="login.php" class="text-decoration-none fw-semibold">
                            Fazer Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Confirmação -->
<div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recoveryModalLabel">
                    <i class="bi bi-envelope-check me-2"></i>
                    E-mail Enviado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Instruções Enviadas!</h5>
                <p class="text-muted">
                    Enviamos um e-mail com instruções para redefinir sua senha. 
                    Verifique sua caixa de entrada e a pasta de spam.
                </p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Dica:</strong> O link de redefinição expira em 1 hora por segurança.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check me-1"></i>
                    Entendi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Mostrar modal de confirmação se houver sucesso
<?php if (isset($_SESSION['recovery_sent'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const recoveryModal = new bootstrap.Modal(document.getElementById('recoveryModal'));
    recoveryModal.show();
});
<?php unset($_SESSION['recovery_sent']); ?>
<?php endif; ?>
</script>

<?php include 'partials/footer.php'; ?>
