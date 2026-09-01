<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'cadastro_id',
        'nome',
        'mensagem',
        'nota',
        'nota_facilidade',
        'nota_visual',
        'nota_desempenho',
        'nota_organizacao',
        'nota_funcionalidades',
        'nota_acessibilidade',
    ];

    /**
     * Nome de exibição: quem avaliou logado usa o nome do cadastro; quem
     * avaliou como visitante usa o que digitou no campo "nome" (opcional).
     */
    public function nomeExibicao(): string
    {
        return $this->nome
            ?: optional($this->cadastro)->responsavel
            ?: 'Visitante';
    }

    public const CATEGORIAS = [
        'nota_facilidade' => 'Facilidade de uso',
        'nota_visual' => 'Visual do sistema',
        'nota_desempenho' => 'Desempenho',
        'nota_organizacao' => 'Organização das informações',
        'nota_funcionalidades' => 'Funcionalidades',
        'nota_acessibilidade' => 'Acessibilidade',
    ];

    public function sentimento(): ?string
    {
        if (!$this->nota) {
            return null;
        }

        return match (true) {
            $this->nota >= 4 => 'positivo',
            $this->nota == 3 => 'neutro',
            default => 'negativo',
        };
    }

    public function cadastro()
    {
        return $this->belongsTo(Cadastro::class);
    }
}
