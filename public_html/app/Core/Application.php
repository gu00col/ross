<?php
/**
 * Classe Principal da Aplicação
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Application
{
    private static $instance = null;
    private $config = [];
    private $services = [];

    private function __construct()
    {
        $this->loadConfig();
        $this->registerServices();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Carregar configurações
     */
    private function loadConfig()
    {
        $this->config = require CONFIG_PATH . '/config.php';
    }

    /**
     * Registrar serviços
     */
    private function registerServices()
    {
        // Registrar serviços básicos
        $this->services['config'] = $this->config;
        $this->services['database'] = new Database();
        $this->services['redis'] = new Redis();
        $this->services['session'] = new Session();
        $this->services['cache'] = new Cache();
        $this->services['logger'] = new Logger();
    }

    /**
     * Obter serviço
     */
    public function get($service)
    {
        return $this->services[$service] ?? null;
    }

    /**
     * Obter configuração
     */
    public function config($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Executar aplicação
     */
    public function run()
    {
        try {
            // Iniciar sessão
            $this->get('session')->start();

            // Processar requisição
            $this->handleRequest();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Processar requisição
     */
    private function handleRequest()
    {
        $router = new Router();
        $router->dispatch();
    }

    /**
     * Tratar erros
     */
    private function handleError(\Exception $e)
    {
        if (APP_DEBUG) {
            echo '<h1>Erro na Aplicação</h1>';
            echo '<p><strong>Mensagem:</strong> ' . $e->getMessage() . '</p>';
            echo '<p><strong>Arquivo:</strong> ' . $e->getFile() . '</p>';
            echo '<p><strong>Linha:</strong> ' . $e->getLine() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        } else {
            echo '<h1>Erro Interno do Servidor</h1>';
            echo '<p>Ocorreu um erro inesperado. Tente novamente mais tarde.</p>';
        }

        // Log do erro
        $this->get('logger')->error('Application Error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
