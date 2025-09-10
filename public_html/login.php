<?php
/**
 * Página de Login
 * Sistema de autenticação de usuários
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

$page_title = "Login - Sistema de Análise Contratual";
include 'partials/header.php';
?>

<main class="login-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.svg" 
                                 alt="Logo" 
                                 class="mb-3" 
                                 style="height: 60px;">
                            <h3 class="fw-bold text-primary">Entrar</h3>
                            <p class="text-muted">Acesse sua conta para continuar</p>
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

                        <form action="partials/auth/login_process.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>
                                    E-mail
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="invalid-feedback">
                                    Por favor, insira um e-mail válido.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>
                                    Senha
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Por favor, insira sua senha.
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="remember" 
                                       name="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar de mim
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Entrar
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="password_recovery.php" class="text-decoration-none">
                                    <i class="bi bi-question-circle me-1"></i>
                                    Esqueci minha senha
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        Não tem uma conta? 
                        <a href="register.php" class="text-decoration-none fw-semibold">
                            Cadastre-se aqui
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

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
</script>

<?php include 'partials/footer.php'; ?>
