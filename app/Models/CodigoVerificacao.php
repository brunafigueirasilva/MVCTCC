<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Código numérico de 6 dígitos usado para confirmar por e-mail (e, no
 * futuro, SMS/WhatsApp) ações sensíveis de senha: "esqueci minha senha" e
 * "trocar senha a partir do perfil". Guardado com hash (nunca em texto
 * puro), expira em alguns minutos e só pode ser usado uma vez.
 */
class CodigoVerificacao extends Model
{
    protected $table = 'codigos_verificacao';

    protected $fillable = [
        'email',
        'codigo_hash',
        'proposito',
        'payload',
        'expira_em',
        'usado_em',
    ];

    protected $casts = [
        'expira_em' => 'datetime',
        'usado_em' => 'datetime',
    ];

    /**
     * Gera um novo código de 6 dígitos para o e-mail/propósito informados e
     * já invalida qualquer código anterior pendente do mesmo par
     * (email, propósito), pra nunca ter mais de um código válido ao mesmo
     * tempo.
     */
    public static function gerar(string $email, string $proposito, ?string $payload = null, int $minutos = 15): string
    {
        self::where('email', $email)
            ->where('proposito', $proposito)
            ->whereNull('usado_em')
            ->delete();

        $codigo = (string) random_int(100000, 999999);

        self::create([
            'email' => $email,
            'codigo_hash' => Hash::make($codigo),
            'proposito' => $proposito,
            'payload' => $payload,
            'expira_em' => now()->addMinutes($minutos),
        ]);

        return $codigo;
    }

    /**
     * Confirma um código digitado pelo usuário. Retorna o registro (ainda
     * não marcado como usado — quem chama decide quando marcar, depois de
     * aplicar a ação) ou null se o código for inválido, expirado ou já
     * usado.
     */
    public static function validar(string $email, string $codigo, string $proposito): ?self
    {
        $registro = self::where('email', $email)
            ->where('proposito', $proposito)
            ->whereNull('usado_em')
            ->where('expira_em', '>=', now())
            ->latest('id')
            ->first();

        if (!$registro || !Hash::check($codigo, $registro->codigo_hash)) {
            return null;
        }

        return $registro;
    }

    public function marcarUsado(): void
    {
        $this->update(['usado_em' => now()]);
    }
}
