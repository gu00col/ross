<?php

namespace App;

use MF\Init\Bootstrap;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Route extends Bootstrap
{

    public function __construct($projectName) {
        parent::__construct($projectName);
    }

    protected function initRoutes()
    {
        //echo 'Iniciando initRoute <br>';
        // Landing page em '/'
        $routes['index'] = array(
            'route' => '/',
            'controller' => 'LandingController',
            'action' => 'index'
        );

		$routes['home'] = array(
			'route' => '/home',
			'controller' => 'HomeController',
            'action' => 'index'
        );
        // Página de login (GET)
        $routes['login'] = array(
            'route' => '/login',
            'controller' => 'IndexController',
            'action' => 'index'
        );
        // Autenticação (POST)
        $routes['login_post'] = array(
            'route' => '/login_post',
            'controller' => 'IndexController',
            'action' => 'autenticar'
        );
        $routes['sair'] = array(
            'route' => '/sair',
            'controller' => 'IndexController',
            'action' => 'sair'
        );
        $routes['contratos'] = array(
            'route' => '/contratos',
            'controller' => 'ContratosController',
            'action' => 'index'
        );
        $routes['contrato'] = array(
            'route' => '/contrato',
            'controller' => 'ContratoController',
            'action' => 'index'
        );
        $routes['minha_conta'] = array(
            'route' => '/minha_conta',
            'controller' => 'MinhaContaController',
            'action' => 'index'
        );
        $routes['change_password'] = array(
            'route' => '/change_password',
            'controller' => 'MinhaContaController',
            'action' => 'changePassword'
        );
        $routes['upload_contract'] = array(
            'route' => '/upload_contract',
            'controller' => 'UploadController',
            'action' => 'process'
        );
        $routes['delete_contract'] = array(
            'route' => '/delete_contract',
            'controller' => 'DeleteController',
            'action' => 'process'
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