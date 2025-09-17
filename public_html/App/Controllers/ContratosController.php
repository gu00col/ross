<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class ContratosController extends Action
{
    public function index()
    {
        $this->validaAutenticacao();

        $contractModel = Container::getModel('Contract');
        $userId = $_SESSION['id'];

        // 1. Lógica de Paginação
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Itens por página
        $total_contracts = $contractModel->getContractCountByUser($userId);
        $total_pages = ceil($total_contracts / $limit);
        $offset = ($page - 1) * $limit;

        // 2. Buscar contratos da página atual
        $this->view->contracts = $contractModel->getContractsByUser($userId, $limit, $offset);

        // 3. Passar dados da paginação para a view
        $this->view->current_page = $page;
        $this->view->total_pages = $total_pages;

        // Passa o status do upload para a view (se houver)
        $this->view->upload_status = $_GET['upload'] ?? '';

        $this->view->active_page = 'contratos';
        $this->render('index', 'base');
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}
