<?php

namespace App;

use MF\Init\Bootstrap;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Route extends Bootstrap
{

    protected function initRoute()
    {
        //echo 'Iniciando initRoute <br>';
        $routes['index'] = array(
            'route' => '/',
            'controller' => 'IndexController',
            'action' => 'index'
        );
        $routes['notFound'] = array(
            'route' => '404',
            'controller' => 'NotFoundController',
            'action' => 'index'
        );
        $this->setRoutes($routes);
    }
}

?>