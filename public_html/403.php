<?php
/**
 * Página 403 - Acesso negado
 * Sistema ROSS - Analista Jurídico
 */

$page_title = "Acesso negado - ROSS Analista Jurídico";
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
    
    <style>
        :root {
            --ross-blue: #0D2149;
            --ross-light-blue: #1a3a7a;
            --ross-beige: #F9EBE0;
            --ross-dark-beige: #f2e0c7;
            --ross-text-dark: #1e293b;
            --ross-text-muted: #64748b;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--ross-blue);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .error-container {
            text-align: center;
            color: var(--ross-beige);
            max-width: 600px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        


        .error-container {
            text-align: center;
            color: var(--ross-beige);
            max-width: 600px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: var(--ross-beige);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 3px;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--ross-beige);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .error-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--ross-dark-beige);
            opacity: 0.9;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-custom {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid var(--ross-beige);
            color: var(--ross-blue);
            background: var(--ross-beige);
            font-size: 1rem;
        }
        
        .btn-custom:hover {
            background: var(--ross-dark-beige);
            border-color: var(--ross-dark-beige);
            color: var(--ross-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Negado</h1>
        <p class="error-message">
            Você não tem permissão para acessar este recurso.
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-custom">
                <i class="fas fa-home me-2"></i>
                Página Inicial
            </a>
            <a href="/login" class="btn btn-custom">
                <i class="fas fa-sign-in-alt me-2"></i>
                Fazer Login
            </a>
        </div>
    </div>
</body>
</html>
