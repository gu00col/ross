<?php

namespace App\Controllers;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./logs.php');

use MF\Controller\Action;


class NotFoundController extends Action
{
    
    public function index() {
        $this->view->dados = 'alow';
        $this->render('notFound','notFound' );
    }
    
}

?>
