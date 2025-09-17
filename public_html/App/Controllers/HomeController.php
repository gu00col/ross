<?php

namespace App\Controllers;

use MF\Controller\Action;

class HomeController extends Action
{
    public function index()
    {
        $this->validaAutenticacao();
        $this->view->active_page = 'home';
        $this->render('index', 'base');
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}

