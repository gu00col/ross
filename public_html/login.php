<?php
/**
 * Página de Login
 * Sistema de autenticação de usuários - ROSS Analista Jurídico
 */

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar se usuário já está logado
if (isset($_SESSION['user_id'])) {
    header('Location: /home');
    exit;
}

// Carregar sistema de mensagens flash
require_once 'vendor/autoload.php';
$flashService = new \App\Services\FlashMessageService();
$adminSetupService = new \App\Services\AdminSetupService();

$page_title = "Login - ROSS Analista Jurídico";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS customizado -->
    <link href="assets/css/login.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="container-fluid vh-100 p-0">
        <div class="row g-0 h-100">
            <!-- Painel esquerdo - Logo e branding -->
            <div class="col-lg-8 col-md-6 login-left-panel d-flex align-items-center justify-content-center">
                <div class="text-center">
                <img src="assets/images/logo-ross.png" 
                             alt="Logo ROSS" 
                             class="logo-image mb-4">
              
                    <h1 class="brand-text">ROSS</h1>
                </div>
            </div>

            <!-- Painel direito - Formulário de login -->
            <div class="col-lg-4 col-md-6 login-right-panel d-flex align-items-center justify-content-center">
                <div class="container-fluid px-4">
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-10 col-md-8 col-lg-10">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <h2 class="login-title">ROSS</h2>
                                <p class="login-subtitle">Analista Jurídico</p>
                            </div>

                            <!-- Instruções -->
                            <div class="text-center mb-4">
                                <p class="instruction-text mb-1">Digite suas credenciais</p>
                                <p class="instruction-text">para entrar</p>
                            </div>

                            <!-- Mensagens flash -->
                            <?php echo $flashService->render(); ?>

                            <!-- Informações do administrador (primeira vez) -->
                            <?php if ($adminSetupService->needsInitialSetup()): ?>
                                <?php $adminInfo = $adminSetupService->getDefaultAdminInfo(); ?>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Configuração Inicial
                                    </h6>
                                    <p class="mb-2">Use as credenciais do administrador para fazer o primeiro login:</p>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>E-mail:</strong><br>
                                            <code><?php echo htmlspecialchars($adminInfo['email']); ?></code>
                                        </div>
                                        <div class="col-6">
                                            <strong>Nome:</strong><br>
                                            <code><?php echo htmlspecialchars($adminInfo['nome']); ?></code>
                                        </div>
                                    </div>
                                    <hr>
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        A senha está configurada no arquivo .env
                                    </small>
                                </div>
                            <?php endif; ?>

                            <!-- Formulário de login -->
                            <form class="needs-validation" action="/login" method="POST" novalidate>
                                <div class="form-floating mb-3">
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Digite seu e-mail"
                                           required
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <label for="email">E-mail</label>
                                    <div class="invalid-feedback">
                                        Por favor, insira um e-mail válido.
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Digite sua senha"
                                           required>
                                    <label for="password">Senha</label>
                                    <div class="invalid-feedback">
                                        Por favor, insira sua senha.
                                    </div>
                                </div>

                                <div class="text-end mb-3">
                                    <a href="password_recovery.php" class="text-decoration-none forgot-link">Esqueci minha senha</a>
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Entrar
                                    </button>
                                </div>
                            </form>

                            <!-- Footer -->
                            <div class="text-center">
                                <p class="footer-text mb-0">
                                    <span class="footer-version">ROSS Analista Jurídico V 0.1</span><br>
                                    <span class="footer-author">Criado por Luis Oliveira</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript customizado -->
    <script src="assets/js/login.js"></script>
</body>
</html>