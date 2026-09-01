<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Alerta de segurança enviado sempre que o perfil do administrador é
 * alterado. Enviada de forma síncrona (sem ShouldQueue) porque este
 * projeto não roda um worker de fila em produção/demonstração — o
 * usuário precisa ver o aviso registrado imediatamente (MAIL_MAILER=log
 * grava o e-mail em storage/logs/laravel.log em ambiente de desenvolvimento).
 */
class PerfilAtualizadoNotification extends Notification
{
    /**
     * @param array<int, string> $camposAlterados Rótulos legíveis dos campos que mudaram.
     */
    public function __construct(
        private readonly array $camposAlterados,
        private readonly string $dataHora,
        private readonly string $ip,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mensagem = (new MailMessage)
            ->subject('Alerta de segurança: seu perfil foi atualizado')
            ->greeting('Olá!')
            ->line('Detectamos uma alteração no perfil da sua conta no AgriFarm.')
            ->line('**Data/hora:** ' . $this->dataHora)
            ->line('**Endereço IP:** ' . $this->ip)
            ->line('**Dados alterados:** ' . implode(', ', $this->camposAlterados));

        if (array_intersect(['Senha', 'E-mail', 'CPF'], $this->camposAlterados) !== []) {
            $mensagem->line('⚠️ Esta alteração envolve uma informação sensível da sua conta.')
                ->line('Se você não reconhece esta ação, redefina sua senha imediatamente e entre em contato com o suporte.');
        }

        return $mensagem->line('Se foi você quem fez esta alteração, nenhuma ação adicional é necessária.');
    }
}
