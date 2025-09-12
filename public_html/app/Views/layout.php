<?php
/**
 * Layout principal do sistema
 * Frame onde a sidebar fica fixa e as páginas são carregadas
 * Sistema ROSS - Analista Jurídico
 */

// Verificar se usuário está logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Obter título da página atual
$page_title = $page_title ?? 'ROSS - Analista Jurídico';
$page_subtitle = $page_subtitle ?? 'Sistema de análise jurídica';

/**
 * Função para detectar o controller atual baseado na URL
 */
function getControllerName() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);
    
    // Mapear rotas para controllers (rotas exatas)
    $routeMap = [
        '/home' => 'dashboard',
        '/dashboard' => 'dashboard', 
        '/contracts' => 'contracts',
        '/profile' => 'profile',
        '/settings' => 'settings'
    ];
    
    // Verificar se a rota existe no mapeamento exato
    if (isset($routeMap[$path])) {
        return $routeMap[$path];
    }
    
    // Verificar rotas com parâmetros
    $segments = explode('/', trim($path, '/'));
    $firstSegment = $segments[0] ?? '';
    
    // Mapear rotas com parâmetros
    $paramRouteMap = [
        'contract' => 'contract',  // /contract/123 -> contract
        'user' => 'user',          // /user/456 -> user
        'report' => 'report'       // /report/789 -> report
    ];
    
    if (isset($paramRouteMap[$firstSegment])) {
        return $paramRouteMap[$firstSegment];
    }
    
    // Fallback
    return $firstSegment ?: 'dashboard';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS customizado do layout -->
    <link href="/assets/css/layout.css" rel="stylesheet">
    
    <!-- CSS específico da página (se existir) -->
    <?php if (isset($page_css)): ?>
        <?php foreach ($page_css as $css): ?>
            <link href="<?php echo htmlspecialchars($css); ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php else: ?>
        <!-- CSS automático baseado no controller -->
        <?php
        $controller_name = getControllerName();
        $css_file = "/assets/css/{$controller_name}.css";
        $css_exists = file_exists("/var/www/html{$css_file}");
        
        if ($css_exists):
        ?>
            <link href="<?php echo $css_file; ?>" rel="stylesheet">
        <?php endif; ?>
        
    <?php endif; ?>
</head>
<body class="bg-beige">
    <!-- Layout principal com Bootstrap Grid -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0">
                <?php include 'partials/sidebar.php'; ?>
            </div>
            
            <!-- Conteúdo principal -->
            <div class="col-md-10 p-0">
                <main class="main-content">
                    <!-- Header da página -->
                    <div class="page-header">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
                                </div>
                                <div class="col-auto">
                                    <!-- Breadcrumb -->
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">

                                            <?php if (isset($breadcrumbs)): ?>
                                                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                                    <?php if (isset($breadcrumb['url'])): ?>
                                                        <li class="breadcrumb-item">
                                                            <a href="<?php echo htmlspecialchars($breadcrumb['url']); ?>">
                                                                <?php echo htmlspecialchars($breadcrumb['title']); ?>
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li class="breadcrumb-item active" aria-current="page">
                                                            <?php echo htmlspecialchars($breadcrumb['title']); ?>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conteúdo da página -->
                    <div class="page-content p-0">
                        <div class="container-fluid">
                            <?php echo $content; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    
    <!-- Botão de menu mobile (overlay) -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    
    <!-- JavaScript do layout -->
    <script src="/assets/js/layout.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <!-- JavaScript específico da página (se existir) -->
    <?php if (isset($page_js)): ?>
        <?php foreach ($page_js as $js): ?>
            <script src="<?php echo htmlspecialchars($js); ?>"></script>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- JavaScript automático baseado no controller -->
        <?php
        $controller_name = getControllerName();
        $js_file = "/assets/js/{$controller_name}.js";
        $js_exists = file_exists("/var/www/html{$js_file}");
        
        // Não carregar JavaScript para contract (renderização é feita em PHP)
        if ($js_exists && $controller_name !== 'contract'):
        ?>
            <script src="<?php echo $js_file; ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
