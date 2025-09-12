<?php
/**
 * Landing Page - Sistema de Análise Contratual
 * Página inicial sem autenticação
 */

$page_title = "Sistema de Análise Contratual";
include 'partials/header.php';
?>

<main class="landing-page">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold text-primary mb-4">
                        Análise Contratual Inteligente
                    </h1>
                    <p class="lead mb-4">
                        Sistema automatizado de análise de contratos utilizando inteligência artificial 
                        para identificar riscos, cláusulas perigosas e fornecer recomendações jurídicas.
                    </p>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="/login" class="btn btn-primary btn-lg px-4 me-md-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Entrar
                        </a>
                        <a href="#features" class="btn btn-outline-primary btn-lg px-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Saiba Mais
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <img src="assets/images/hero-contract-analysis.svg" 
                         alt="Análise de Contratos" 
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Seção de Funcionalidades -->
<section id="features" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="h1 mb-4">Funcionalidades Principais</h2>
                <p class="lead text-muted">
                    Nossa plataforma oferece análise completa e automatizada de contratos
                </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-3 mb-3">
                            <i class="bi bi-file-earmark-text fs-2"></i>
                        </div>
                        <h5 class="card-title">Upload de Contratos</h5>
                        <p class="card-text text-muted">
                            Envie contratos em PDF e receba análise completa em minutos
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success bg-gradient text-white rounded-3 mb-3">
                            <i class="bi bi-robot fs-2"></i>
                        </div>
                        <h5 class="card-title">IA Avançada</h5>
                        <p class="card-text text-muted">
                            Análise inteligente com Google Gemini para identificar riscos e cláusulas
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-warning bg-gradient text-white rounded-3 mb-3">
                            <i class="bi bi-graph-up fs-2"></i>
                        </div>
                        <h5 class="card-title">Relatórios Detalhados</h5>
                        <p class="card-text text-muted">
                            Relatórios completos com recomendações e pontos de atenção
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção de Benefícios -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="h1 mb-4">Por que escolher nossa plataforma?</h2>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Análise Automatizada</h6>
                                <p class="text-muted mb-0">Processamento rápido e eficiente de contratos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Segurança Total</h6>
                                <p class="text-muted mb-0">Seus documentos são protegidos e confidenciais</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Relatórios Profissionais</h6>
                                <p class="text-muted mb-0">Documentação completa para tomada de decisão</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/benefits-analysis.svg" 
                     alt="Benefícios da Análise" 
                     class="img-fluid">
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
