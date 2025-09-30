<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class MinhaContaController extends Action
{
    public function index()
    {
        $this->validaAutenticacao();

        // Buscar dados completos do usuário no banco de dados
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $userData = $user->getUserById();

        if (!$userData) {
            // Se não conseguir buscar o usuário, redireciona para login
            header('Location: /?login=user_not_found');
            exit;
        }

        $this->view->active_page = 'minha_conta';
        $this->view->user_nome = $userData->__get('nome');
        $this->view->user_email = $userData->__get('email');
        $this->view->is_superuser = $userData->__get('is_superuser');
        
        // Formatar data de criação para exibição
        $createdAt = $userData->__get('created_at');
        if ($createdAt) {
            $this->view->user_created_at = date('d/m/Y', strtotime($createdAt));
        } else {
            $this->view->user_created_at = 'Data não disponível';
        }

        // Se for superadmin, carregar lista de usuários
        if ($userData->__get('is_superuser')) {
            $allUsers = $user->getAllUsers();
            $this->view->users = $allUsers;
        }

        // Passa mensagens de status para a view
        $this->view->status = isset($_GET['status']) ? $_GET['status'] : '';
        
        // Preservar parâmetro tab para manter a aba ativa
        $this->view->current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

        $this->render('index', 'base');
    }

    public function changePassword() {
        $this->validaAutenticacao();
        
        // 1. Validar se as senhas foram preenchidas e se a nova senha e a confirmação são iguais
        if (empty($_POST['newPassword']) || $_POST['newPassword'] != $_POST['confirmNewPassword']) {
            $tab = isset($_GET['tab']) ? '&tab=' . $_GET['tab'] : '';
            header('Location: /minha_conta?status=error_new_password' . $tab);
            exit;
        }

        // 2. Buscar usuário atual no banco para pegar o hash da senha
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();

        // 3. Verificar se a senha atual está correta
        if (!password_verify($_POST['currentPassword'], $currentUser->__get('password'))) {
            $tab = isset($_GET['tab']) ? '&tab=' . $_GET['tab'] : '';
            header('Location: /minha_conta?status=error_current_password' . $tab);
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
            $tab = isset($_GET['tab']) ? '&tab=' . $_GET['tab'] : '';
            header('Location: /minha_conta?status=error_update' . $tab);
        }
        exit;
    }

    /**
     * Cria um novo usuário (apenas para superadmin)
     */
    public function createUser() {
        $this->validaAutenticacao();
        
        // Verificar se é superadmin
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();
        
        if (!$currentUser || !$currentUser->__get('is_superuser')) {
            header('Location: /minha_conta?status=unauthorized');
            exit;
        }

        // Validar dados do POST
        if (empty($_POST['nome']) || empty($_POST['email']) || empty($_POST['password'])) {
            header('Location: /minha_conta?status=error_missing_fields&tab=users');
            exit;
        }

        if ($_POST['password'] !== $_POST['confirm_password']) {
            header('Location: /minha_conta?status=error_password_mismatch&tab=users');
            exit;
        }

        // Verificar se email já existe
        if ($user->emailExists($_POST['email'])) {
            header('Location: /minha_conta?status=error_email_exists&tab=users');
            exit;
        }

        // Criar usuário
        $isSuperuser = isset($_POST['is_superuser']) ? (bool)$_POST['is_superuser'] : false;
        
        try {
            $newUserId = $user->createUser(
                $_POST['nome'],
                $_POST['email'],
                $_POST['password'],
                $isSuperuser
            );
            
            if ($newUserId) {
                header('Location: /minha_conta?status=user_created&tab=users');
            } else {
                header('Location: /minha_conta?status=error_user_creation&tab=users');
            }
        } catch (\Exception $e) {
            logMessage("Erro ao criar usuário: " . $e->getMessage(), "ERROR");
            header('Location: /minha_conta?status=error_user_creation&tab=users');
        }
        
        exit;
    }

    /**
     * Atualiza um usuário (apenas para superadmin)
     */
    public function updateUser() {
        $this->validaAutenticacao();
        
        // Verificar se é superadmin
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();
        
        if (!$currentUser || !$currentUser->__get('is_superuser')) {
            header('Location: /minha_conta?status=unauthorized');
            exit;
        }

        if (empty($_POST['user_id']) || empty($_POST['nome']) || empty($_POST['email'])) {
            header('Location: /minha_conta?status=error_missing_fields&tab=users');
            exit;
        }

        // Verificar se email já existe (excluindo o próprio usuário)
        if ($user->emailExists($_POST['email'], $_POST['user_id'])) {
            header('Location: /minha_conta?status=error_email_exists&tab=users');
            exit;
        }

        $isSuperuser = isset($_POST['is_superuser']) ? (bool)$_POST['is_superuser'] : false;
        $isActive = isset($_POST['active']) ? (bool)$_POST['active'] : true;
        $password = !empty($_POST['password']) ? $_POST['password'] : null;

        try {
            $rowsAffected = $user->updateUser(
                $_POST['user_id'],
                $_POST['nome'],
                $_POST['email'],
                $password,
                $isActive,
                $isSuperuser
            );
            
            if ($rowsAffected > 0) {
                header('Location: /minha_conta?status=user_updated&tab=users');
            } else {
                header('Location: /minha_conta?status=error_user_update&tab=users');
            }
        } catch (\Exception $e) {
            logMessage("Erro ao atualizar usuário: " . $e->getMessage(), "ERROR");
            header('Location: /minha_conta?status=error_user_update&tab=users');
        }
        
        exit;
    }

    /**
     * Desativa um usuário (apenas para superadmin)
     */
    public function deactivateUser() {
        $this->validaAutenticacao();
        
        // Verificar se é superadmin
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();
        
        if (!$currentUser || !$currentUser->__get('is_superuser')) {
            header('Location: /minha_conta?status=unauthorized');
            exit;
        }

        if (empty($_POST['user_id'])) {
            header('Location: /minha_conta?status=error_missing_fields&tab=users');
            exit;
        }

        try {
            $rowsAffected = $user->deactivateUser($_POST['user_id']);
            
            if ($rowsAffected > 0) {
                header('Location: /minha_conta?status=user_deactivated&tab=users');
            } else {
                header('Location: /minha_conta?status=error_user_deactivation&tab=users');
            }
        } catch (\Exception $e) {
            logMessage("Erro ao desativar usuário: " . $e->getMessage(), "ERROR");
            header('Location: /minha_conta?status=error_user_deactivation&tab=users');
        }
        
        exit;
    }

    /**
     * Exclui um usuário permanentemente (apenas para superadmin)
     */
    public function deleteUser() {
        $this->validaAutenticacao();
        
        // Verificar se é superadmin
        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);
        $currentUser = $user->getUserById();
        
        if (!$currentUser || !$currentUser->__get('is_superuser')) {
            header('Location: /minha_conta?status=unauthorized');
            exit;
        }

        if (empty($_POST['user_id'])) {
            header('Location: /minha_conta?status=error_missing_fields&tab=users');
            exit;
        }

        // Verificar se não está tentando excluir a si mesmo
        if ($_POST['user_id'] == $_SESSION['id']) {
            header('Location: /minha_conta?status=error_cannot_delete_self&tab=users');
            exit;
        }

        try {
            $rowsAffected = $user->deleteUser($_POST['user_id']);
            
            if ($rowsAffected > 0) {
                header('Location: /minha_conta?status=user_deleted&tab=users');
            } else {
                header('Location: /minha_conta?status=error_user_deletion&tab=users');
            }
        } catch (\Exception $e) {
            logMessage("Erro ao excluir usuário: " . $e->getMessage(), "ERROR");
            header('Location: /minha_conta?status=error_user_deletion&tab=users');
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
