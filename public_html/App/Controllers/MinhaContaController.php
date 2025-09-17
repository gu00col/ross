<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class MinhaContaController extends Action
{
    public function index()
    {
        $this->validaAutenticacao();

        $this->view->active_page = 'minha_conta';
        $this->view->user_nome = $_SESSION['nome'];
        $this->view->user_email = $_SESSION['email'];
        // A data de criação precisaria ser buscada do banco ou armazenada na sessão no login.
        // Por enquanto, vamos deixar um placeholder.
        $this->view->user_created_at = '25/08/2025'; // Placeholder

        // Passa mensagens de status para a view
        $this->view->status = isset($_GET['status']) ? $_GET['status'] : '';

        $this->render('index', 'base');
    }

    public function changePassword() {
        $this->validaAutenticacao();
        
        // 1. Validar se as senhas foram preenchidas e se a nova senha e a confirmação são iguais
        if (empty($_POST['newPassword']) || $_POST['newPassword'] != $_POST['confirmNewPassword']) {
            header('Location: /minha_conta?status=error_new_password');
            exit;
        }

        // 2. Buscar usuário atual no banco para pegar o hash da senha
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();

        // 3. Verificar se a senha atual está correta
        if (!password_verify($_POST['currentPassword'], $currentUser->__get('password'))) {
            header('Location: /minha_conta?status=error_current_password');
            exit;
        }
        
        // 4. Gerar o hash da nova senha
        $newPasswordHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

        // 5. Atualizar no banco de dados
        $user->__set('password', $newPasswordHash);
        if ($user->updatePassword()) {
            // Destruir a sessão após a alteração da senha
            session_destroy();
            header('Location: /?login=password_changed');
        } else {
            header('Location: /minha_conta?status=error_update');
        }
        exit;
    }

    public function validaAutenticacao() {
        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=unauthorized');
            exit;
        }
    }
}
