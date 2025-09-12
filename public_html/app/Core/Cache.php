<?php
/**
 * Classe de Gerenciamento de Cache
 * Sistema de Análise Contratual
 */

namespace App\Core;

class Cache
{
    private $redis;
    private $config;

    public function __construct()
    {
        $this->config = config('cache');
        $this->redis = new Redis();
    }

    /**
     * Obter valor do cache
     */
    public function get($key, $default = null)
    {
        $value = $this->redis->get($key);
        
        if ($value === false) {
            return $default;
        }
        
        return unserialize($value);
    }

    /**
     * Definir valor no cache
     */
    public function set($key, $value, $ttl = null)
    {
        $ttl = $ttl ?? $this->config['ttl'];
        $serialized = serialize($value);
        
        return $this->redis->set($key, $serialized, $ttl);
    }

    /**
     * Verificar se chave existe
     */
    public function has($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * Deletar chave do cache
     */
    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * Obter ou definir valor
     */
    public function remember($key, $callback, $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Incrementar valor
     */
    public function increment($key, $value = 1)
    {
        return $this->redis->increment($key, $value);
    }

    /**
     * Decrementar valor
     */
    public function decrement($key, $value = 1)
    {
        return $this->redis->decrement($key, $value);
    }

    /**
     * Definir expiração
     */
    public function expire($key, $ttl)
    {
        return $this->redis->expire($key, $ttl);
    }

    /**
     * Obter TTL
     */
    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

    /**
     * Limpar todo o cache
     */
    public function flush()
    {
        return $this->redis->flush();
    }

    /**
     * Obter múltiplas chaves
     */
    public function many($keys)
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        
        return $results;
    }

    /**
     * Definir múltiplas chaves
     */
    public function put($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        
        return true;
    }

    /**
     * Deletar múltiplas chaves
     */
    public function forget($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        
        return true;
    }
}
