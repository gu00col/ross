<?php

namespace App\Controllers;

use MF\Controller\Action;

class MinhaContaController extends Action
{
    public function index()
    {
        $this->view->active_page = 'minha_conta';
        $this->render('index', 'base');
    }
}
