<?php

namespace App\Controllers;

use MF\Controller\Action;

class LandingController extends Action
{
    public function index()
    {
        // Landing page temporária em branco (sem layout complexo)
        $this->render('index', '');
    }
}


