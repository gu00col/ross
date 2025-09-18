<?php

namespace MF\Model;

use App\Connection;

class Container {

	public static function getModel($model) {

		$class = "\\App\\Models\\".ucfirst($model);
        
        $conn = Connection::getDb();

        if ($conn) {
            logMessage("Conexão com o banco de dados obtida com sucesso via App\\Connection.", "INFO");
            return new $class($conn);
        } else {
            logMessage("Falha ao obter conexão com o banco de dados via App\\Connection.", "ERROR");
            return null; // Retorna nulo para indicar falha
        }
	}
}

?>