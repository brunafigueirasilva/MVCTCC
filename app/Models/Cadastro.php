<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AutenticavelPadrao;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Conta do sistema (o usuário logado). Implementa o contrato de usuário do
 * Laravel porque é ela que o Sanctum devolve em $request->user() na API:
 * sem isso, qualquer recurso que pergunte "quem é o usuário" (limite de
 * requisições, logs, políticas) quebrava com 500 (getAuthIdentifier).
 */
class Cadastro extends Model implements Authenticatable
{
    use AutenticavelPadrao, HasApiTokens, HasFactory, Notifiable;

    /** A senha fica na coluna "senha" (não "password"). */
    public function getAuthPasswordName()
    {
        return 'senha';
    }

    /** Sem "lembrar de mim" por token: a tabela não tem remember_token. */
    public function getRememberTokenName()
    {
        return '';
    }

    protected $table = 'cadastros';

    protected $fillable = [
        'responsavel',
        'cpf',
        'data_nascimento',
        'email',
        'senha',
        'telefone',
        'foto',
        'senha_alterada_em',
        'email_alterado_em',
        'email_verificado_em',
    ];

    /**
     * Senha e demais dados sensíveis nunca devem vazar em toArray()/toJson()
     * (ex.: quando o cadastro é passado para uma view ou logado por engano).
     */
    protected $hidden = [
        'senha',
    ];

    /**
     * "foto_url" vai junto no JSON (API do app) — URL pronta para exibir,
     * seja de uma foto enviada pelo perfil ou da foto da conta Google.
     */
    protected $appends = [
        'foto_url',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'senha_alterada_em' => 'datetime',
            'email_alterado_em' => 'datetime',
            'email_verificado_em' => 'datetime',
        ];
    }

    public function fazendas()
    {
        return $this->hasMany(Fazenda::class, 'cadastro_id');
    }

    /**
     * "foto" guarda um de dois formatos: o caminho de uma foto enviada pelo
     * perfil (disco "public", ex.: "perfil/abc.jpg") ou a URL da foto da
     * conta Google (login via Google). Esta é a URL pronta para <img>.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }

        return self::fotoEhExterna($this->foto) ? $this->foto : asset('storage/' . $this->foto);
    }

    public static function fotoEhExterna(?string $foto): bool
    {
        return (bool) $foto && preg_match('#^https?://#i', $foto) === 1;
    }

    /**
     * Login via Google: usa a foto da conta Google enquanto o usuário não
     * tiver enviado uma foto própria pelo perfil (essa nunca é trocada).
     * Também atualiza se o Google mudou a URL da foto.
     */
    public function usarFotoDoGoogle(?string $urlFoto): void
    {
        if (!$urlFoto || strlen($urlFoto) > 255 || !self::fotoEhExterna($urlFoto)) {
            return;
        }

        $temFotoPropria = $this->foto && !self::fotoEhExterna($this->foto);
        if ($temFotoPropria || $this->foto === $urlFoto) {
            return;
        }

        $this->forceFill(['foto' => $urlFoto])->save();
    }

    /** true assim que o e-mail ATUAL foi confirmado por código — vira null de novo se o e-mail mudar (ver CadastroController::atualizarPerfil()). */
    public function getEmailVerificadoAttribute(): bool
    {
        return $this->email_verificado_em !== null;
    }

    /** Idade calculada a partir da data de nascimento, usada para validar maioridade. */
    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
    }

    /** CPF formatado (000.000.000-00) para exibição, mantendo só dígitos no banco. */
    public function getCpfFormatadoAttribute(): ?string
    {
        if (!$this->cpf) {
            return null;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    /** Telefone formatado ((00) 00000-0000 ou (00) 0000-0000) para exibição. */
    public function getTelefoneFormatadoAttribute(): ?string
    {
        if (!$this->telefone) {
            return null;
        }

        if (strlen($this->telefone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $this->telefone);
        }

        if (strlen($this->telefone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $this->telefone);
        }

        return $this->telefone;
    }
}