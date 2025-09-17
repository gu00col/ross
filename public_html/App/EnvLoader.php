<?php

namespace App;

/**
 * Classe para carregar variáveis de ambiente do arquivo .env
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class EnvLoader
{
    private static array $variables = [];
    private static bool $loaded = false;

    /**
     * Carrega as variáveis do arquivo .env
     * 
     * @param string $envPath Caminho para o arquivo .env
     * @return void
     */
    public static function load(string $envPath = './.env'): void
    {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($envPath)) {
            throw new \Exception("Arquivo .env não encontrado em: {$envPath}");
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignora comentários e linhas vazias
            if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
                continue;
            }

            // Verifica se a linha contém um sinal de igual
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // Remove aspas se existirem
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                self::$variables[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Obtém uma variável de ambiente
     * 
     * @param string $key Chave da variável
     * @param mixed $default Valor padrão caso a chave não exista
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables[$key] ?? $default;
    }

    /**
     * Verifica se uma variável existe
     * 
     * @param string $key Chave da variável
     * @return bool
     */
    public static function has(string $key): bool
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]);
    }

    /**
     * Retorna todas as variáveis carregadas
     * 
     * @return array
     */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }
}
