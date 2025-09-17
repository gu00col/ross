<?php

namespace App\Controllers;

use MF\Controller\Action;

class ContratosController extends Action
{
    public function index()
    {
        $this->view->active_page = 'contratos';
        $this->render('index', 'base');
    }
}
