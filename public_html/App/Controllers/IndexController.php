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
        $this->view->login_error = isset($_GET['login']) && $_GET['login'] == 'error' ? true : false;
        $this->view->password_changed = isset($_GET['status']) && $_GET['status'] == 'password_changed' ? true : false;
        $this->view->unauthorized_access = isset($_GET['login']) && $_GET['login'] == 'unauthorized' ? true : false;
        $this->render('login', 'base_login');
    }

    public function autenticar() {

        logMessage("Iniciando processo de autenticação para o e-mail: {$_POST['email']}", "INFO");

		$user = Container::getModel('User');

        if (!$user) {
            logMessage("Não foi possível obter o model User. Verifique a conexão com o banco de dados.", "ERROR");
            header('Location: /?login=error');
            return;
        }

		$user->email = $_POST['email'];
		$user->password = $_POST['password'];

		$retorno = $user->getUserByEmail();

		if($retorno && !empty($retorno->id) && !empty($retorno->name) ) {
            logMessage("Usuário encontrado: {$retorno->email} (ID: {$retorno->id})", "INFO");

            if (password_verify($user->password, $retorno->password) && $retorno->active) {
                logMessage("Senha verificada com sucesso. Usuário ativo. Autenticado.", "INFO");

                session_start();
                $_SESSION['id'] = $retorno->id;
                $_SESSION['nome'] = $retorno->name;

                header('Location: /home');

            } else {
                if (!$retorno->active) {
                    logMessage("Tentativa de login falhou: Usuário {$retorno->email} não está ativo.", "ERROR");
                } else {
                    logMessage("Tentativa de login falhou: Senha incorreta para o usuário {$retorno->email}.", "ERROR");
                }
                header('Location: /?login=error');
            }

		} else {
            logMessage("Tentativa de login falhou: Nenhum usuário encontrado para o e-mail: {$user->email}", "ERROR");
			header('Location: /?login=error');
		}
	}

    public function sair() {
        session_start();
        session_destroy();
        header('Location: /');
    }
}

?>
