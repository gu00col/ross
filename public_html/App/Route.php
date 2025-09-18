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
        $routes['home'] = array(
            'route' => 'home',
            'controller' => 'HomeController',
            'action' => 'index'
        );
        $routes['login'] = array(
            'route' => 'login',
            'controller' => 'IndexController',
            'action' => 'autenticar'
        );
        $routes['contratos'] = array(
            'route' => 'contratos',
            'controller' => 'ContratosController',
            'action' => 'index'
        );
        $routes['minha_conta'] = array(
            'route' => 'minha_conta',
            'controller' => 'MinhaContaController',
            'action' => 'index'
        );
        $routes['change_password'] = array(
            'route' => 'change_password',
            'controller' => 'MinhaContaController',
            'action' => 'changePassword'
        );
        $routes['upload_contract'] = array(
            'route' => 'upload_contract',
            'controller' => 'UploadController',
            'action' => 'process'
        );
        $routes['contrato'] = array(
            'route' => 'contrato',
            'controller' => 'ContratoController',
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