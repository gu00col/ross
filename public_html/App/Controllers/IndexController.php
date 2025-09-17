<?php

namespace App\Controllers;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('./logs.php');

use MF\Controller\Action;
use MF\Model\Container;

use App\Models\User;

class IndexController extends Action
{
    
    public function index() {

        // $user = Container::getModel('User');
        
        // $users = $user->getActiveUsers(); 

        // $this->view->dados = $users;
        $this->view->login_error = isset($_GET['login']) && $_GET['login'] == 'erro';
        $this->view->unauthorized_access = isset($_GET['login']) && $_GET['login'] == 'unauthorized';
        $this->view->password_changed = isset($_GET['login']) && $_GET['login'] == 'password_changed';
        $this->render('login','login' );
    }

    public function autenticar() {
        $user = Container::getModel('User');
        $user->__set('email', $_POST['email']);
        
        $user_data = $user->getUserByEmail();

        if ($user_data && $user_data->__get('active') && password_verify($_POST['password'], $user_data->__get('password'))) {
            $_SESSION['id'] = $user_data->__get('id');
            $_SESSION['nome'] = $user_data->__get('nome');
            $_SESSION['email'] = $user_data->__get('email');
            $_SESSION['is_superuser'] = $user_data->__get('is_superuser');
            
            header('Location: /home');
            exit;
        } else {
            header('Location: /?login=erro');
            exit;
        }
    }
    
}

?>
