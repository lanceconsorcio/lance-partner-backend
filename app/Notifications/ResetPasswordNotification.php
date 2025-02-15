<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = env('APP_ENV') === 'production'
            ? env('FRONTEND_URL', 'https://centraldecontempladas.com.br/')
            : env('LOCAL_FRONTEND_URL', 'http://localhost:3000/');

        $resetUrl = "{$frontendUrl}admin/reset?token={$this->token}";

        return (new MailMessage)
            ->subject('Recuperação de Senha')
            ->markdown('mail.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl
            ])
            ->greeting("Olá, {$notifiable->name}!") // Personalização do nome do usuário
            ->line('Recebemos um pedido para redefinir sua senha. Se você não solicitou isso, ignore este e-mail.')
            ->action('Redefinir Senha', $resetUrl)
            ->line('Se o botão acima não funcionar, copie e cole o link abaixo em seu navegador:')
            ->line($resetUrl)
            ->salutation('Atenciosamente, Sua Equipe.');
    }
}