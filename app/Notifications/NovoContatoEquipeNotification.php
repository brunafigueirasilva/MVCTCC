<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Cópia de cada solicitação do formulário "Entre em Contato" para a equipe
 * AgriFarm (CONTATO_EMAIL_EQUIPE), pra que ninguém precise olhar o log.
 */
class NovoContatoEquipeNotification extends Notification
{
    public function __construct(private readonly array $dados)
    {
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
        $assunto = ContatoRecebidoNotification::ASSUNTOS[$this->dados['subject']] ?? 'Contato';

        return (new MailMessage)
            ->subject('Novo contato pelo site: ' . $assunto)
            ->replyTo($this->dados['email'], $this->dados['name'])
            ->greeting('Nova solicitação recebida')
            ->line('**Nome:** ' . $this->dados['name'])
            ->line('**E-mail:** ' . $this->dados['email'])
            ->line('**Telefone/WhatsApp:** ' . ($this->dados['phone'] ?: '—'))
            ->line('**Empresa/Propriedade:** ' . ($this->dados['company'] ?: '—'))
            ->line('**Assunto:** ' . $assunto)
            ->line('**Mensagem:** ' . $this->dados['message']);
    }
}
