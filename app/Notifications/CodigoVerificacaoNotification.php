<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail com o código de confirmação de 6 dígitos, usado tanto em
 * "esqueci minha senha" quanto na troca de senha a partir do perfil.
 * Enviada de forma síncrona (sem ShouldQueue), mesmo padrão de
 * PerfilAtualizadoNotification — este projeto não roda worker de fila.
 */
class CodigoVerificacaoNotification extends Notification
{
    public function __construct(
        private readonly string $codigo,
        private readonly string $motivo,
        private readonly int $validoPorMinutos = 15,
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
        return (new MailMessage)
            ->subject('Seu código de confirmação — ' . $this->motivo)
            ->greeting('Olá!')
            ->line('Use o código abaixo para confirmar: ' . $this->motivo . '.')
            ->line(new \Illuminate\Support\HtmlString(
                '<div style="font-size:28px;font-weight:bold;letter-spacing:6px;text-align:center;margin:16px 0;">' . $this->codigo . '</div>'
            ))
            ->line('Esse código vale por ' . $this->validoPorMinutos . ' minutos e só pode ser usado uma vez.')
            ->line('Se você não solicitou isso, ignore este e-mail — nenhuma alteração será feita na sua conta.');
    }
}
