<?php
/**
 * Sistema de roteamento alternativo
 * Usado quando .htaccess não funciona
 */

// Obter a rota solicitada
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = ltrim($path, '/');

// Mapeamento de rotas
$routes = [
    'login' => 'login.php',
    'home' => 'home.php',
    'dashboard' => 'dashboard.php',
    'contratos' => 'contracts.php',
    'perfil' => 'profile.php',
    'configuracoes' => 'settings.php',
    'recuperar-senha' => 'password_recovery.php',
    'cadastro' => 'register.php',
    'logout' => 'logout.php',
];

// Se for a raiz, redirecionar para login
if (empty($path) || $path === 'index.php') {
    header('Location: login.php');
    exit;
}

// Verificar se a rota existe
if (isset($routes[$path])) {
    $file = $routes[$path];
    
    // Verificar se o arquivo existe
    if (file_exists($file)) {
        require_once $file;
        exit;
    }
}

// Rota não encontrada - mostrar erro 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada - ROSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0D2149 0%, #1a3a7a 100%); min-height: 100vh; }
        .error-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { background: white; border-radius: 15px; padding: 3rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .error-code { font-size: 6rem; font-weight: 900; color: #0D2149; margin-bottom: 1rem; }
        .error-message { font-size: 1.5rem; color: #666; margin-bottom: 2rem; }
        .btn-primary { background: #0D2149; border: none; padding: 12px 30px; border-radius: 8px; }
        .btn-primary:hover { background: #1a3a7a; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1 class="error-message">Página não encontrada</h1>
            <p class="text-muted mb-4">A página que você está procurando não existe.</p>
            <a href="login.php" class="btn btn-primary">Voltar ao Login</a>
        </div>
    </div>
</body>
</html>

