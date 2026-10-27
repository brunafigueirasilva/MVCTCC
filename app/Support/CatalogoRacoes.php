<?php

namespace App\Support;

use App\Models\Lote;
use App\Models\Racao;
use Illuminate\Support\Collection;

/**
 * Catálogo de TIPOS de ração ("modelos") mostrado em cards na tela Tipos de
 * Rações — web e app leem esta mesma lista (o app via
 * GET /api/racoes/catalogo). Cada modelo define o perfil do animal
 * (tipo, sexo, finalidade, faixa etária) e os nutrientes de referência; o
 * botão "Adicionar" cadastra uma ração com esse perfil pela rota normal de
 * cadastro, que valida tudo de novo.
 *
 * Perfis seguem as regras do Lote (LoteRegras): só finalidades que a espécie
 * tem e nada de leite/ovos para machos — ver tests/Unit/CatalogoRacoesTest.
 * Sexo/finalidade/idade nulos = ração de uso geral naquele quesito (ver
 * CompatibilidadeRacao).
 *
 * Valores nutricionais são referências médias de mercado para orientar o
 * produtor; ele pode ajustá-los depois em "Editar".
 */
class CatalogoRacoes
{
    public const MODELOS = [
        // ---------------------------------------------------------- Bovinos
        [
            'chave' => 'bovino-leite', 'nome' => 'Ração Bovino Leite',
            'tipo' => 'Bovino', 'sexo' => 'Fêmea', 'finalidade' => 'Leite', 'idade' => null,
            'descricao' => 'Para vacas em lactação: proteína alta para sustentar a produção de leite.',
            'proteina' => 22, 'energia' => 1.75, 'fibra' => 16,
        ],
        [
            'chave' => 'bovino-corte', 'nome' => 'Ração Bovino Corte',
            'tipo' => 'Bovino', 'sexo' => null, 'finalidade' => 'Corte', 'idade' => null,
            'descricao' => 'Engorda e terminação: energia elevada para ganho de peso rápido.',
            'proteina' => 14, 'energia' => 2.9, 'fibra' => 12,
        ],
        [
            'chave' => 'bovino-reproducao', 'nome' => 'Ração Bovino Reprodução',
            'tipo' => 'Bovino', 'sexo' => null, 'finalidade' => 'Reprodução', 'idade' => null,
            'descricao' => 'Matrizes e touros: equilíbrio de minerais e vitaminas para a fertilidade.',
            'proteina' => 16, 'energia' => 2.5, 'fibra' => 18,
        ],
        [
            'chave' => 'bovino-bezerros', 'nome' => 'Ração Bezerros Inicial',
            'tipo' => 'Bovino', 'sexo' => null, 'finalidade' => null, 'idade' => '0-6 meses',
            'descricao' => 'Fase inicial (0 a 6 meses): fácil digestão para o desenvolvimento do rúmen.',
            'proteina' => 20, 'energia' => 3.0, 'fibra' => 8,
        ],

        // ----------------------------------------------------------- Suínos
        [
            'chave' => 'suino-terminacao', 'nome' => 'Ração Suíno Terminação',
            'tipo' => 'Suíno', 'sexo' => null, 'finalidade' => 'Corte', 'idade' => null,
            'descricao' => 'Crescimento e terminação: energia e aminoácidos para carcaça magra.',
            'proteina' => 16, 'energia' => 3.25, 'fibra' => 4,
        ],
        [
            'chave' => 'suino-matrizes', 'nome' => 'Ração Suíno Matrizes',
            'tipo' => 'Suíno', 'sexo' => null, 'finalidade' => 'Reprodução', 'idade' => null,
            'descricao' => 'Porcas e cachaços: fibra e minerais para gestação e lactação saudáveis.',
            'proteina' => 15, 'energia' => 3.1, 'fibra' => 7,
        ],
        [
            'chave' => 'suino-leitoes', 'nome' => 'Ração Leitões Pré-inicial',
            'tipo' => 'Suíno', 'sexo' => null, 'finalidade' => null, 'idade' => '0-6 meses',
            'descricao' => 'Leitões em creche: alta palatabilidade e proteína de boa digestão.',
            'proteina' => 21, 'energia' => 3.4, 'fibra' => 3,
        ],

        // --------------------------------------------------------------- Aves
        [
            'chave' => 'aves-postura', 'nome' => 'Ração Aves de Postura',
            'tipo' => 'Avicultura', 'sexo' => 'Fêmea', 'finalidade' => 'Ovos', 'idade' => null,
            'descricao' => 'Poedeiras: cálcio reforçado para casca firme e postura constante.',
            'proteina' => 16, 'energia' => 2.75, 'fibra' => 4,
        ],
        [
            'chave' => 'aves-corte', 'nome' => 'Ração Frango de Corte',
            'tipo' => 'Avicultura', 'sexo' => null, 'finalidade' => 'Corte', 'idade' => null,
            'descricao' => 'Frangos de corte: proteína e energia altas para engorda rápida.',
            'proteina' => 21, 'energia' => 3.05, 'fibra' => 3,
        ],
        [
            'chave' => 'aves-matrizes', 'nome' => 'Ração Aves Matrizes',
            'tipo' => 'Avicultura', 'sexo' => null, 'finalidade' => 'Reprodução', 'idade' => null,
            'descricao' => 'Matrizes e galos reprodutores: fertilidade e boa eclosão dos ovos.',
            'proteina' => 15, 'energia' => 2.8, 'fibra' => 5,
        ],

        // ------------------------------------------------------------ Equinos
        [
            'chave' => 'equino-trabalho', 'nome' => 'Ração Equino Trabalho',
            'tipo' => 'Equino', 'sexo' => null, 'finalidade' => 'Trabalho', 'idade' => null,
            'descricao' => 'Cavalos de lida: energia de liberação lenta para jornadas longas.',
            'proteina' => 12, 'energia' => 2.6, 'fibra' => 14,
        ],
        [
            'chave' => 'equino-esporte', 'nome' => 'Ração Equino Esporte',
            'tipo' => 'Equino', 'sexo' => null, 'finalidade' => 'Esporte', 'idade' => null,
            'descricao' => 'Alto desempenho: gordura e eletrólitos para treino e competição.',
            'proteina' => 13, 'energia' => 3.0, 'fibra' => 12,
        ],
        [
            'chave' => 'equino-reproducao', 'nome' => 'Ração Equino Reprodução',
            'tipo' => 'Equino', 'sexo' => null, 'finalidade' => 'Reprodução', 'idade' => null,
            'descricao' => 'Éguas gestantes e garanhões: minerais e vitaminas para a reprodução.',
            'proteina' => 14, 'energia' => 2.7, 'fibra' => 15,
        ],

        // ---------------------------------------------------- Ovinos/Caprinos
        [
            'chave' => 'ovino-corte', 'nome' => 'Ração Ovino Cordeiros',
            'tipo' => 'Ovino', 'sexo' => null, 'finalidade' => 'Corte', 'idade' => null,
            'descricao' => 'Cordeiros em engorda: ganho de peso com baixo risco de acidose.',
            'proteina' => 17, 'energia' => 2.8, 'fibra' => 12,
        ],
        [
            'chave' => 'ovino-leite', 'nome' => 'Ração Ovino Leite',
            'tipo' => 'Ovino', 'sexo' => 'Fêmea', 'finalidade' => 'Leite', 'idade' => null,
            'descricao' => 'Ovelhas leiteiras: proteína e energia para a lactação.',
            'proteina' => 18, 'energia' => 2.6, 'fibra' => 15,
        ],
        [
            'chave' => 'caprino-leite', 'nome' => 'Ração Caprino Leite',
            'tipo' => 'Caprino', 'sexo' => 'Fêmea', 'finalidade' => 'Leite', 'idade' => null,
            'descricao' => 'Cabras em lactação: mais leite com teor de sólidos estável.',
            'proteina' => 18, 'energia' => 2.6, 'fibra' => 16,
        ],
        [
            'chave' => 'caprino-corte', 'nome' => 'Ração Caprino Corte',
            'tipo' => 'Caprino', 'sexo' => null, 'finalidade' => 'Corte', 'idade' => null,
            'descricao' => 'Cabritos em terminação: crescimento rápido e carne magra.',
            'proteina' => 16, 'energia' => 2.7, 'fibra' => 14,
        ],
    ];

    /** Ícone Font Awesome por espécie (o app tem o equivalente em Material). */
    public const ICONES = [
        'Bovino' => 'fa-cow',
        'Suíno' => 'fa-piggy-bank',
        'Equino' => 'fa-horse',
        'Avicultura' => 'fa-dove',
        'Ovino' => 'fa-paw',
        'Caprino' => 'fa-paw',
    ];

    public static function todos(): Collection
    {
        return collect(self::MODELOS)->map(fn (array $m) => $m + ['icone' => self::ICONES[$m['tipo']] ?? 'fa-wheat-awn']);
    }

    public static function buscar(string $chave): ?array
    {
        return self::todos()->firstWhere('chave', $chave);
    }

    /** Ração "virtual" (não salva) com o perfil do modelo — para usar a
     *  mesma regra de compatibilidade das rações reais. */
    public static function comoRacao(array $modelo, int $fazendaId): Racao
    {
        return new Racao([
            'fazenda_id' => $fazendaId,
            'tipo' => $modelo['tipo'],
            'sexo' => $modelo['sexo'],
            'finalidade' => $modelo['finalidade'],
            'status' => 'ativa',
        ]);
    }

    /**
     * Catálogo com, para cada modelo, quantos lotes do usuário (na fazenda
     * escolhida, ou em todas) aceitariam essa ração e quantas rações desse
     * mesmo perfil ele já tem.
     *
     * @param  Collection<Lote>  $lotes
     * @param  Collection<Racao>  $racoes
     */
    public static function comResumo(Collection $lotes, Collection $racoes): Collection
    {
        return self::todos()->map(function (array $modelo) use ($lotes, $racoes) {
            $compativeis = $lotes->filter(fn (Lote $l) => CompatibilidadeRacao::compativel(self::comoRacao($modelo, (int) $l->fazenda_id), $l));

            return $modelo + [
                'lotes_compativeis' => $compativeis->count(),
                'lotes' => $compativeis->map(fn (Lote $l) => [
                    'id' => $l->id,
                    'nome_lote' => $l->nome_lote,
                    'fazenda_id' => $l->fazenda_id,
                ])->values()->all(),
                'ja_cadastradas' => $racoes->filter(fn (Racao $r) => $r->tipo === $modelo['tipo']
                    && $r->sexo === $modelo['sexo']
                    && $r->finalidade === $modelo['finalidade']
                    && $r->idade === $modelo['idade'])->count(),
            ];
        });
    }

    /** Dados para POST /racoes a partir do modelo (perfil + nutrientes). */
    public static function camposRacao(array $modelo): array
    {
        return [
            'tipo' => $modelo['tipo'],
            'sexo' => $modelo['sexo'],
            'finalidade' => $modelo['finalidade'],
            'idade' => $modelo['idade'],
            'proteina_percentual' => $modelo['proteina'],
            'energia_mcal_kg' => $modelo['energia'],
            'fibra_percentual' => $modelo['fibra'],
            'observacoes' => $modelo['descricao'],
        ];
    }
}
