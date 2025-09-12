<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sistema de Análise Contratual'; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo asset_url('css/app.css'); ?>" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="<?php echo app_url(); ?>">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Sistema de Análise Contratual
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url(); ?>">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url('/contracts'); ?>">Contratos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url('/my-account'); ?>">Minha Conta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url('/settings'); ?>">Configurações</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo app_url('/logout'); ?>">Sair</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema de Análise Contratual</h5>
                    <p class="mb-0">Sistema automatizado de análise de contratos utilizando inteligência artificial.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo asset_url('js/app.js'); ?>"></script>
</body>
</html>
