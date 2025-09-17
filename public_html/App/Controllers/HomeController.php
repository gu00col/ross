<?php

namespace App\Controllers;

use MF\Controller\Action;

class HomeController extends Action
{
    public function index()
    {
        $this->render('index', 'base');
    }
}

