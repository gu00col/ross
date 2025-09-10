<?php
/**
 * Página de Erro 404
 * Página não encontrada
 */

$page_title = "404 - Página não encontrada";
include 'partials/header.php';
?>

<main class="error-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center justify-content-center">
            <div class="col-md-6 text-center">
                <div class="error-content">
                    <h1 class="display-1 text-primary">404</h1>
                    <h2 class="h3 mb-4">Página não encontrada</h2>
                    <p class="lead text-muted mb-4">
                        A página que você está procurando não existe ou foi movida.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="/" class="btn btn-primary btn-lg">
                            <i class="bi bi-house me-2"></i>
                            Voltar ao Início
                        </a>
                        <a href="/contracts" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Ver Contratos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'partials/footer.php'; ?>
