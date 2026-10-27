<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envio de mensagem pelo WhatsApp Cloud API (Meta). Só funciona com
 * WHATSAPP_TOKEN e WHATSAPP_PHONE_ID configurados no .env (ver
 * config/services.php) — sem eles, enviar() devolve false sem erro.
 */
class WhatsAppService
{
    public function configurado(): bool
    {
        return filled(config('services.whatsapp.token'))
            && filled(config('services.whatsapp.phone_id'));
    }

    /** Converte "(19) 99001-9586" em "5519990019586" (formato E.164 sem +). */
    public function normalizarTelefone(?string $telefone): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $telefone);

        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            $digitos = '55' . $digitos;
        }

        return strlen($digitos) >= 12 ? $digitos : null;
    }

    /** Envia o template configurado com o primeiro nome da pessoa. */
    public function enviarTemplate(string $telefone, string $primeiroNome): bool
    {
        $numero = $this->normalizarTelefone($telefone);

        if (! $this->configurado() || ! $numero) {
            return false;
        }

        try {
            $resposta = Http::withToken(config('services.whatsapp.token'))
                ->timeout(10)
                ->post('https://graph.facebook.com/v21.0/' . config('services.whatsapp.phone_id') . '/messages', [
                    'messaging_product' => 'whatsapp',
                    'to' => $numero,
                    'type' => 'template',
                    'template' => [
                        'name' => config('services.whatsapp.template'),
                        'language' => ['code' => config('services.whatsapp.idioma')],
                        'components' => [[
                            'type' => 'body',
                            'parameters' => [['type' => 'text', 'text' => $primeiroNome]],
                        ]],
                    ],
                ]);

            if ($resposta->failed()) {
                Log::warning('WhatsApp: falha ao enviar mensagem', ['status' => $resposta->status(), 'body' => $resposta->json()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp: erro ao enviar mensagem', ['erro' => $e->getMessage()]);
            return false;
        }
    }
}
