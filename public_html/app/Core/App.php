<?php
/**
 * Classe principal da aplicação ROSS
 * Inicializa o sistema e gerencia rotas
 */

require_once __DIR__ . '/Router.php';

class App
{
    /**
     * @var Router Instância do roteador
     */
    private $router;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        $this->router = new Router();
    }
    
    /**
     * Inicializa a aplicação
     */
    public function run(): void
    {
        // Resolver rota atual
        $targetFile = $this->router->resolve();
        
        if ($targetFile === null) {
            $this->show404();
            return;
        }
        
        // Verificar se arquivo existe
        if (!file_exists($targetFile)) {
            $this->show404();
            return;
        }
        
        // Incluir arquivo
        require_once $targetFile;
    }
    
    /**
     * Exibe página 404
     */
    private function show404(): void
    {
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
                    <p class="text-muted mb-4">A página que você está procurando não existe ou foi movida.</p>
                    <a href="/login" class="btn btn-primary">Voltar ao Login</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Obtém instância do roteador
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }
}

