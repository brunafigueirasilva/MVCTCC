<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cadastro extends Model
{
    use HasFactory, Notifiable;

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
    ];

    /**
     * Senha e demais dados sensíveis nunca devem vazar em toArray()/toJson()
     * (ex.: quando o cadastro é passado para uma view ou logado por engano).
     */
    protected $hidden = [
        'senha',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'senha_alterada_em' => 'datetime',
            'email_alterado_em' => 'datetime',
        ];
    }

    public function fazendas()
    {
        return $this->hasMany(Fazenda::class, 'cadastro_id');
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