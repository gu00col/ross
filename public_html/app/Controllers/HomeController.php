<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class HomeController
{
    public function index()
    {
        // Verificar se usuário está logado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            // Se estiver logado, redirecionar para home
            return new RedirectResponse('/home');
        }
        
        // Se não estiver logado, carregar landing page
        ob_start();
        include __DIR__ . '/../../landing.php';
        $content = ob_get_clean();
        
        return new HtmlResponse($content);
    }
}