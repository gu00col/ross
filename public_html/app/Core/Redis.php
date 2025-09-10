<?php
/**
 * Classe de Conexão com Redis
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Redis
{
    private $connection;
    private $config;

    public function __construct()
    {
        $this->config = config('redis');
        $this->connect();
    }

    /**
     * Conectar ao Redis
     */
    private function connect()
    {
        try {
            $config = $this->config['connections'][$this->config['default']];
            
            $this->connection = new \Redis();
            $this->connection->connect($config['host'], $config['port'], $config['timeout']);
            
            if ($config['password']) {
                $this->connection->auth($config['password']);
            }
            
            $this->connection->select($config['database']);
            
        } catch (\Exception $e) {
            throw new \Exception('Erro ao conectar com o Redis: ' . $e->getMessage());
        }
    }

    /**
     * Obter conexão
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Definir valor
     */
    public function set($key, $value, $ttl = null)
    {
        $key = $this->config['cache']['prefix'] . $key;
        
        if ($ttl) {
            return $this->connection->setex($key, $ttl, $value);
        }
        
        return $this->connection->set($key, $value);
    }

    /**
     * Obter valor
     */
    public function get($key)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->get($key);
    }

    /**
     * Deletar chave
     */
    public function delete($key)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->del($key);
    }

    /**
     * Verificar se chave existe
     */
    public function exists($key)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->exists($key);
    }

    /**
     * Definir expiração
     */
    public function expire($key, $ttl)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->expire($key, $ttl);
    }

    /**
     * Obter TTL
     */
    public function ttl($key)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->ttl($key);
    }

    /**
     * Incrementar valor
     */
    public function increment($key, $value = 1)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->incrBy($key, $value);
    }

    /**
     * Decrementar valor
     */
    public function decrement($key, $value = 1)
    {
        $key = $this->config['cache']['prefix'] . $key;
        return $this->connection->decrBy($key, $value);
    }

    /**
     * Listar chaves
     */
    public function keys($pattern = '*')
    {
        $pattern = $this->config['cache']['prefix'] . $pattern;
        return $this->connection->keys($pattern);
    }

    /**
     * Limpar cache
     */
    public function flush()
    {
        return $this->connection->flushDB();
    }

    /**
     * Obter informações do servidor
     */
    public function info()
    {
        return $this->connection->info();
    }
}
