<?php
/**
 * Classe de Gerenciamento de Sessão
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Session
{
    private $redis;
    private $config;

    public function __construct()
    {
        $this->config = config('session');
        $this->redis = new Redis();
    }

    /**
     * Iniciar sessão
     */
    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar parâmetros da sessão
            ini_set('session.cookie_name', $this->config['cookie']);
            ini_set('session.cookie_path', $this->config['path']);
            ini_set('session.cookie_domain', $this->config['domain']);
            ini_set('session.cookie_secure', $this->config['secure']);
            ini_set('session.cookie_httponly', $this->config['http_only']);
            ini_set('session.cookie_samesite', $this->config['same_site']);
            
            // Configurar tempo de vida
            ini_set('session.gc_maxlifetime', $this->config['lifetime'] * 60);
            
            session_start();
        }
    }

    /**
     * Definir valor na sessão
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Obter valor da sessão
     */
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verificar se chave existe
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remover chave da sessão
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Limpar toda a sessão
     */
    public function clear()
    {
        $_SESSION = [];
    }

    /**
     * Destruir sessão
     */
    public function destroy()
    {
        session_destroy();
    }

    /**
     * Regenerar ID da sessão
     */
    public function regenerate()
    {
        session_regenerate_id(true);
    }

    /**
     * Obter ID da sessão
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Definir ID da sessão
     */
    public function setId($id)
    {
        session_id($id);
    }

    /**
     * Obter todos os dados da sessão
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * Flash message (usar uma vez)
     */
    public function flash($key, $value = null)
    {
        if ($value === null) {
            $value = $this->get($key);
            $this->remove($key);
            return $value;
        }
        
        $this->set($key, $value);
    }

    /**
     * Definir flash message
     */
    public function setFlash($key, $value)
    {
        $this->set('_flash.' . $key, $value);
    }

    /**
     * Obter flash message
     */
    public function getFlash($key, $default = null)
    {
        return $this->get('_flash.' . $key, $default);
    }

    /**
     * Verificar se flash message existe
     */
    public function hasFlash($key)
    {
        return $this->has('_flash.' . $key);
    }
}
