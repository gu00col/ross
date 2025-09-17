<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class HomeController extends Action
{
    public function index()
    {
        $this->validaAutenticacao();

        $contract = Container::getModel('Contract');
        $this->view->latestContracts = $contract->getLatestByUserId($_SESSION['id']);
        $this->view->dashboardStats = $contract->getDashboardStats($_SESSION['id']);

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

