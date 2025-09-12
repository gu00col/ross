<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;
    
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer(): void
    {
        try {
            // Configurações do servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $this->mailer->Port = $_ENV['MAIL_PORT'] ?? 587;
            
            // Configurações do remetente
            $this->mailer->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@ross.com.br',
                $_ENV['MAIL_FROM_NAME'] ?? 'ROSS Analista Jurídico'
            );
            
            // Configurações de charset
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->isHTML(true);
            
        } catch (Exception $e) {
            error_log("Erro na configuração do PHPMailer: " . $e->getMessage());
        }
    }
    
    public function sendPasswordRecovery(string $email, string $name, string $token): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            
            $this->mailer->Subject = 'Recuperação de Senha - ROSS';
            $this->mailer->Body = $this->getPasswordRecoveryTemplate($name, $token);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de recuperação: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendWelcomeEmail(string $email, string $name): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            
            $this->mailer->Subject = 'Bem-vindo ao ROSS - Analista Jurídico';
            $this->mailer->Body = $this->getWelcomeTemplate($name);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de boas-vindas: " . $e->getMessage());
            return false;
        }
    }
    
    public function sendContractAnalysisNotification(string $email, string $name, string $contractName): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);
            
            $this->mailer->Subject = 'Análise de Contrato Concluída - ROSS';
            $this->mailer->Body = $this->getContractAnalysisTemplate($name, $contractName);
            
            return $this->mailer->send();
            
        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de análise: " . $e->getMessage());
            return false;
        }
    }
    
    private function getPasswordRecoveryTemplate(string $name, string $token): string
    {
        $resetUrl = $_ENV['APP_URL'] . "/reset-password?token={$token}";
        
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Recuperação de Senha - ROSS</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0D2149; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; background: #0D2149; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ROSS Analista Jurídico</h1>
                </div>
                <div class='content'>
                    <h2>Recuperação de Senha</h2>
                    <p>Olá, {$name}!</p>
                    <p>Você solicitou a recuperação de senha para sua conta no ROSS.</p>
                    <p>Clique no botão abaixo para redefinir sua senha:</p>
                    <a href='{$resetUrl}' class='button'>Redefinir Senha</a>
                    <p>Se você não solicitou esta recuperação, ignore este e-mail.</p>
                    <p>Este link expira em 1 hora.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 ROSS Analista Jurídico. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getWelcomeTemplate(string $name): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Bem-vindo ao ROSS</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0D2149; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; background: #0D2149; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ROSS Analista Jurídico</h1>
                </div>
                <div class='content'>
                    <h2>Bem-vindo ao ROSS!</h2>
                    <p>Olá, {$name}!</p>
                    <p>Seja bem-vindo ao ROSS - Analista Jurídico, a plataforma mais avançada para análise contratual automatizada.</p>
                    <p>Com o ROSS, você pode:</p>
                    <ul>
                        <li>Analisar contratos automaticamente</li>
                        <li>Identificar riscos e brechas</li>
                        <li>Gerar relatórios detalhados</li>
                        <li>Gerenciar sua biblioteca de contratos</li>
                    </ul>
                    <a href='" . $_ENV['APP_URL'] . "/login' class='button'>Acessar Plataforma</a>
                </div>
                <div class='footer'>
                    <p>© 2024 ROSS Analista Jurídico. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function getContractAnalysisTemplate(string $name, string $contractName): string
    {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Análise Concluída - ROSS</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0D2149; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .button { display: inline-block; background: #0D2149; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>ROSS Analista Jurídico</h1>
                </div>
                <div class='content'>
                    <h2>Análise Concluída!</h2>
                    <p>Olá, {$name}!</p>
                    <p>A análise do contrato <strong>{$contractName}</strong> foi concluída com sucesso.</p>
                    <p>Você pode acessar o relatório completo na plataforma.</p>
                    <a href='" . $_ENV['APP_URL'] . "/contratos' class='button'>Ver Análise</a>
                </div>
                <div class='footer'>
                    <p>© 2024 ROSS Analista Jurídico. Todos os direitos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}

