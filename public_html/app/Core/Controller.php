<?php
/**
 * Controller Base
 * Sistema de Análise Contratual
 */

namespace App\Core;

abstract class Controller
{
    protected $app;
    protected $db;
    protected $redis;
    protected $session;
    protected $cache;
    protected $logger;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->db = $this->app->get('database');
        $this->redis = $this->app->get('redis');
        $this->session = $this->app->get('session');
        $this->cache = $this->app->get('cache');
        $this->logger = $this->app->get('logger');
    }

    /**
     * Renderizar view
     */
    protected function view($view, $data = [])
    {
        $viewFile = APP_PATH . '/Views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View {$view} não encontrada");
        }
        
        // Extrair variáveis para a view
        extract($data);
        
        // Incluir view
        include $viewFile;
    }

    /**
     * Renderizar view com layout
     */
    protected function viewWithLayout($view, $data = [], $layout = 'main')
    {
        $data['content'] = $this->getViewContent($view, $data);
        $this->view("layouts/{$layout}", $data);
    }

    /**
     * Obter conteúdo da view
     */
    private function getViewContent($view, $data = [])
    {
        ob_start();
        $this->view($view, $data);
        return ob_get_clean();
    }

    /**
     * Retornar JSON
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirecionar
     */
    protected function redirect($url, $status = 302)
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    /**
     * Retornar erro 404
     */
    protected function notFound($message = 'Página não encontrada')
    {
        http_response_code(404);
        $this->view('errors/404', ['message' => $message]);
        exit;
    }

    /**
     * Retornar erro 500
     */
    protected function serverError($message = 'Erro interno do servidor')
    {
        http_response_code(500);
        $this->view('errors/500', ['message' => $message]);
        exit;
    }

    /**
     * Verificar se é requisição AJAX
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obter dados da requisição
     */
    protected function getRequestData()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
        
        return $_POST;
    }

    /**
     * Validar dados
     */
    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleParts = explode('|', $rule);
            
            foreach ($ruleParts as $rulePart) {
                $this->validateField($field, $value, $rulePart, $errors);
            }
        }
        
        return $errors;
    }

    /**
     * Validar campo individual
     */
    private function validateField($field, $value, $rule, &$errors)
    {
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule, 2);
        }
        
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $errors[$field][] = "O campo {$field} é obrigatório";
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "O campo {$field} deve ser um e-mail válido";
                }
                break;
            case 'min':
                if (strlen($value) < $param) {
                    $errors[$field][] = "O campo {$field} deve ter pelo menos {$param} caracteres";
                }
                break;
            case 'max':
                if (strlen($value) > $param) {
                    $errors[$field][] = "O campo {$field} deve ter no máximo {$param} caracteres";
                }
                break;
        }
    }

    /**
     * Verificar autenticação
     */
    protected function requireAuth()
    {
        if (!$this->session->has('user_id')) {
            $this->redirect('/login');
        }
    }

    /**
     * Obter usuário logado
     */
    protected function getCurrentUser()
    {
        if (!$this->session->has('user_id')) {
            return null;
        }
        
        return [
            'id' => $this->session->get('user_id'),
            'name' => $this->session->get('user_name'),
            'email' => $this->session->get('user_email'),
        ];
    }
}
