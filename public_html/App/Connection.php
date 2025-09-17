<?php

namespace App;

use MF\Init\Config;

/**
 * Classe de conexão com banco de dados PostgreSQL
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class Connection extends Config {

    /**
     * Estabelece conexão com o banco de dados PostgreSQL
     * 
     * @return \PDO|null Retorna objeto PDO ou null em caso de erro
     */
    public static function getDb(): ?\PDO {
        try {
            // Instanciar a classe Connection para acessar as propriedades herdadas
            $config = new self();
            
            // DSN específico para PostgreSQL
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s;options='--client_encoding=UTF8'",
                $config->db_type,
                $config->db_url,
                $config->db_port,
                $config->db_name
            );
            
            $conn = new \PDO($dsn, $config->db_userName, $config->db_password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);

            return $conn;

        } catch (\PDOException $e) {
            // Log do erro (em produção, usar sistema de logs)
            error_log("Erro de conexão com PostgreSQL: " . $e->getMessage());
            
            // Em modo de desenvolvimento, exibe o erro
            if (EnvLoader::get('APP_DEBUG', 'true') === 'true') {
                echo "Erro de conexão: " . $e->getMessage();
            }
            
            return null;
        }
    }
}

?>
