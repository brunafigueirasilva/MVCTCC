<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Confirmação enviada para quem preencheu o formulário "Entre em Contato"
 * da landing page. Quando o assunto é "Solicitar Demonstração", o e-mail
 * explica como a demonstração funciona e os próximos passos. Síncrona
 * (sem ShouldQueue), mesmo padrão das outras notificações do projeto.
 */
class ContatoRecebidoNotification extends Notification
{
    public const ASSUNTOS = [
        'demo' => 'Solicitar Demonstração do Sistema',
        'pricing' => 'Informações sobre Planos e Preços',
        'partnership' => 'Proposta de Parceria Comercial',
        'support' => 'Suporte Técnico',
        'investor' => 'Interesse em Investimento',
        'other' => 'Outro Assunto',
    ];

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
        $primeiroNome = strtok($this->dados['name'], ' ');
        $assunto = self::ASSUNTOS[$this->dados['subject']] ?? 'Contato';

        if ($this->dados['subject'] === 'demo') {
            return (new MailMessage)
                ->subject('AgriFarm — Recebemos seu pedido de demonstração')
                ->greeting('Olá, ' . $primeiroNome . '!')
                ->line('Recebemos sua solicitação de demonstração do AgriFarm' . (! empty($this->dados['company']) ? ' para ' . $this->dados['company'] : '') . '. Obrigado pelo interesse!')
                ->line('**Como funciona a demonstração:**')
                ->line('• Nossa equipe entrará em contato em até 1 dia útil para agendar o melhor dia e horário.')
                ->line('• A apresentação é online, dura cerca de 30 minutos e é gratuita.')
                ->line('• Mostramos na prática o controle de lotes, cochos, estoque de ração, plano alimentar e relatórios.')
                ->line('• No final, você pode tirar todas as dúvidas sobre a implantação na sua propriedade.')
                ->line('**Sua mensagem:** ' . $this->dados['message'])
                ->line('Se precisar falar com a gente antes disso, é só responder este e-mail.')
                ->salutation('Equipe AgriFarm');
        }

        return (new MailMessage)
            ->subject('AgriFarm — Recebemos sua mensagem')
            ->greeting('Olá, ' . $primeiroNome . '!')
            ->line('Recebemos sua mensagem sobre **' . $assunto . '** e nossa equipe vai retornar em breve.')
            ->line('**Sua mensagem:** ' . $this->dados['message'])
            ->line('Se precisar complementar alguma informação, é só responder este e-mail.')
            ->salutation('Equipe AgriFarm');
    }
}
