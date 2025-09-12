<?php
/**
 * Página 404 - Página não encontrada
 * Sistema ROSS - Analista Jurídico
 */

$page_title = "Página não encontrada - ROSS Analista Jurídico";
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
        
        .btn-primary-custom {
            background: var(--ross-light-blue);
            border-color: var(--ross-light-blue);
            color: var(--ross-beige);
        }
        
        .btn-primary-custom:hover {
            background: var(--ross-blue);
            border-color: var(--ross-blue);
            color: var(--ross-beige);
        }
        
        .floating-icon {
            position: absolute;
            font-size: 2rem;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-icon:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .floating-icon:nth-child(2) { top: 60%; right: 15%; animation-delay: 2s; }
        .floating-icon:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 4s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .error-details {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .error-details h6 {
            color: var(--ross-beige);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .error-details code {
            background: var(--ross-light-blue);
            color: var(--ross-beige);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- Ícones flutuantes -->
        <i class="fas fa-search floating-icon"></i>
        <i class="fas fa-exclamation-triangle floating-icon"></i>
        <i class="fas fa-question-circle floating-icon"></i>
        
        <!-- Código de erro -->
        <div class="error-code">404</div>
        
        <!-- Título -->
        <h1 class="error-title">Página não encontrada</h1>
        
        <!-- Mensagem -->
        <p class="error-message">
            A página que você está procurando não existe ou foi movida.
        </p>
        
        <!-- Ações -->
        <div class="error-actions">
            <a href="/" class="btn btn-custom btn-primary-custom">
                <i class="fas fa-home me-2"></i>
                Página Inicial
            </a>
            <a href="/login" class="btn btn-custom">
                <i class="fas fa-sign-in-alt me-2"></i>
                Fazer Login
            </a>
            <a href="javascript:history.back()" class="btn btn-custom">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
        
        <!-- Detalhes do erro -->
        <div class="error-details">
            <h6><i class="fas fa-info-circle me-2"></i>Informações Técnicas</h6>
            <p class="mb-2">
                <strong>URL solicitada:</strong> 
                <code><?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A'); ?></code>
            </p>
            <p class="mb-2">
                <strong>Método:</strong> 
                <code><?php echo htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'N/A'); ?></code>
            </p>
            <p class="mb-0">
                <strong>Data/Hora:</strong> 
                <code><?php echo date('d/m/Y H:i:s'); ?></code>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
