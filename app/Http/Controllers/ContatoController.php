<?php

namespace App\Http\Controllers;

use App\Notifications\ContatoRecebidoNotification;
use App\Notifications\NovoContatoEquipeNotification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

/**
 * Formulário "Entre em Contato" da landing page (landing/contato.blade.php):
 * envia a confirmação por e-mail (e por WhatsApp, se configurado) para quem
 * preencheu, e uma cópia para a equipe.
 */
class ContatoController extends Controller
{
    public function enviar(Request $request, WhatsAppService $whatsApp)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', Rule::in(array_keys(ContatoRecebidoNotification::ASSUNTOS))],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $dados['phone'] = $dados['phone'] ?? '';
        $dados['company'] = $dados['company'] ?? '';

        $emailEnviado = true;
        try {
            Notification::route('mail', [$dados['email'] => $dados['name']])
                ->notify(new ContatoRecebidoNotification($dados));
        } catch (\Throwable $e) {
            $emailEnviado = false;
            Log::error('Contato: falha ao enviar e-mail de confirmação', ['erro' => $e->getMessage()]);
        }

        if ($emailEquipe = config('services.contato.email_equipe')) {
            try {
                Notification::route('mail', $emailEquipe)->notify(new NovoContatoEquipeNotification($dados));
            } catch (\Throwable $e) {
                Log::error('Contato: falha ao enviar cópia para a equipe', ['erro' => $e->getMessage()]);
            }
        }

        $whatsAppEnviado = $dados['phone'] !== ''
            && $whatsApp->enviarTemplate($dados['phone'], strtok($dados['name'], ' '));

        if (! $emailEnviado && ! $whatsAppEnviado) {
            return response()->json([
                'ok' => false,
                'mensagem' => 'Não foi possível enviar sua mensagem agora. Tente novamente em alguns minutos.',
            ], 500);
        }

        $canais = $emailEnviado ? 'no seu e-mail' : '';
        if ($whatsAppEnviado) {
            $canais .= ($canais ? ' e ' : '') . 'no seu WhatsApp';
        }

        return response()->json([
            'ok' => true,
            'email' => $emailEnviado,
            'whatsapp' => $whatsAppEnviado,
            'mensagem' => $dados['subject'] === 'demo'
                ? "Pedido de demonstração enviado! Enviamos os detalhes {$canais}."
                : "Mensagem enviada! Enviamos uma confirmação {$canais}.",
        ]);
    }
}
