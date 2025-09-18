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

        // Verifica se o usuário já está logado e redireciona para /home se estiver
        if(isset($_SESSION['id']) && $_SESSION['id'] != '') {
            header('Location: /home');
            exit;
        }

        // $user = Container::getModel('User');
        
        // $users = $user->getActiveUsers(); 

        // $this->view->dados = $users;
        $this->view->login_error = isset($_GET['login']) && $_GET['login'] == 'error' ? true : false;
        $this->view->password_changed = isset($_GET['status']) && $_GET['status'] == 'password_changed' ? true : false;
        $this->view->unauthorized_access = isset($_GET['login']) && $_GET['login'] == 'unauthorized' ? true : false;
		
		$this->render('login', ''); // Renderiza a view sem layout
	}

	public function autenticar() {

        logMessage("Iniciando processo de autenticação para o e-mail: {$_POST['email']}", "INFO");
        logMessage("POST data: " . json_encode($_POST), "INFO");

		$user = Container::getModel('User');

        if (!$user) {
            logMessage("Não foi possível obter o model User. Verifique a conexão com o banco de dados.", "ERROR");
            header('Location: /?login=error');
            return;
        }

		$user->email = $_POST['email'];
		$user->password = $_POST['password'];
        logMessage("Objeto User preparado para busca: email='{$user->email}'", "INFO");

		$retorno = $user->getUserByEmail();

        if ($retorno) {
            logMessage("Retorno do getUserByEmail(): Object(User) [id=>{$retorno->id}, nome=>{$retorno->nome}, email=>{$retorno->email}, active=>{$retorno->active}]", "INFO");
        } else {
            logMessage("Retorno do getUserByEmail(): null", "INFO");
        }

		if($retorno && $retorno->id && $retorno->nome) { // CORRIGIDO: Removido !empty() que não funciona com propriedades privadas via __get
            logMessage("Usuário encontrado e verificado (ID e Nome não vazios).", "INFO");

            $isPasswordCorrect = password_verify($user->password, $retorno->password);
            logMessage("Verificando senha. Hash do DB: '{$retorno->password}'. Resultado do password_verify(): " . ($isPasswordCorrect ? 'true' : 'false'), "INFO");

            if ($isPasswordCorrect && $retorno->active) {
                logMessage("Senha correta e usuário ativo. Autenticação bem-sucedida.", "INFO");

                $_SESSION['id'] = $retorno->id;
                $_SESSION['nome'] = $retorno->nome; // CORRIGIDO: de 'name' para 'nome'
                logMessage("Sessão criada: " . json_encode($_SESSION), "INFO");

                header('Location: /home');

            } else {
                if (!$retorno->active) {
                    logMessage("Tentativa de login falhou: Usuário {$retorno->email} não está ativo.", "ERROR");
                } 
                if (!$isPasswordCorrect) {
                    logMessage("Tentativa de login falhou: Senha incorreta para o usuário {$retorno->email}.", "ERROR");
                }
                header('Location: /?login=error');
            }

		} else {
            logMessage("Tentativa de login falhou: a verificação 'retorno && retorno->id && retorno->nome' falhou. Isso pode acontecer se o usuário não for encontrado ou se as propriedades id/nome estiverem vazias.", "ERROR");
			header('Location: /?login=error');
		}
	}

    public function sair() {
        session_destroy();
        header('Location: /');
    }
}

?>
