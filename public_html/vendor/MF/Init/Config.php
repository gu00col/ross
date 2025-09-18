<?php

namespace MF\Init;

use App\EnvLoader;

/**
 * Classe de configuração que carrega dados do arquivo .env
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */
class Config {
    public string $db_type;
    public string $db_url;
    public string $db_port;
    public string $db_userName;
    public string $db_password;
    public string $db_name;

    public function __construct() {
        // Carrega as variáveis do arquivo .env
        EnvLoader::load();
        
        // Configurações do banco de dados a partir do .env
        $this->db_type = 'pgsql'; // PostgreSQL usa 'pgsql' como driver PDO
        $this->db_url = EnvLoader::get('DB_HOST', 'localhost'); 
        $this->db_port = EnvLoader::get('DB_PORT', '5432'); 
        $this->db_userName = EnvLoader::get('DB_USERNAME', 'postgres');
        $this->db_password = EnvLoader::get('DB_PASSWORD', ''); 
        $this->db_name = EnvLoader::get('DB_DATABASE', 'ross');  
    }
}

?>
