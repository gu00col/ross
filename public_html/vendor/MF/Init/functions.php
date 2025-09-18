<?php
/**
 * Funções auxiliares do sistema de roteamento
 * 
 * @author Luis Gustavo Barbosa de Oliveira
 * @version 1.0.0
 */

/**
 * Função para obter o caminho dinâmico baseado no nome do projeto
 * 
 * @param string $projectName Nome do projeto
 * @return string Caminho dinâmico
 */
function getDynamicPath($projectName)
{
    // Obtém o caminho da aplicação
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Remove a barra inicial, se existir, para evitar um elemento vazio no array
    if (substr($path, 0, 1) === '/') {
        $path = substr($path, 1);
    }

    // Divide o caminho em um array usando o caractere '/'
    $pathArray = explode('/', $path);
    // Encontra a posição da pasta do projeto
    $projectKey = array_search($projectName, $pathArray);

    // Se a pasta do projeto for encontrada, cria o novo caminho a partir dela
    if ($projectKey !== false) {
        $dynamicPathArray = array_slice($pathArray, $projectKey + 1);
        $dynamicPath = '/' . implode('/', $dynamicPathArray);
    } else {
        // Se a pasta do projeto não for encontrada, retorna o caminho original
        $dynamicPath = $path;
    }
    
    // Se o caminho estiver vazio, retorna '/'
    if (empty($dynamicPath)) {
        $dynamicPath = '/';
    }
    
    //logMessage('$dynamicPath: '.$dynamicPath);
    return $dynamicPath;
}
