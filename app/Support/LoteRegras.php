<?php

namespace App\Support;

/**
 * Regras de negócio do cadastro de Lote, isoladas do Controller/FormRequest
 * de propósito: tanto a validação do back-end quanto os <select> da view
 * (via JSON) leem daqui, então nunca ficam dessincronizados. Para adicionar
 * uma espécie ou finalidade nova no futuro, basta editar as constantes
 * abaixo — nenhuma outra parte do sistema precisa mudar.
 */
class LoteRegras
{
    /**
     * Finalidades possíveis por espécie. Cada tipo de animal só aceita as
     * finalidades que fazem sentido pra ele — nem toda espécie é explorada
     * pra corte, leite, ovos etc.
     */
    public const FINALIDADES_POR_ANIMAL = [
        'Bovino' => ['Corte', 'Leite', 'Reprodução', 'Tratamento'],
        'Suíno' => ['Corte', 'Reprodução', 'Tratamento'],
        'Equino' => ['Reprodução', 'Trabalho', 'Esporte', 'Tratamento'],
        'Avicultura' => ['Corte', 'Ovos', 'Reprodução', 'Tratamento'],
        'Ovino' => ['Corte', 'Leite', 'Reprodução', 'Tratamento'],
        'Caprino' => ['Corte', 'Leite', 'Reprodução', 'Tratamento'],
    ];

    /**
     * "Tratamento" é a finalidade usada para lotes de animais doentes/em
     * quarentena — isolados do restante do rebanho até se recuperarem.
     * Não é exclusiva de espécie nem de sexo (doença não escolhe), por isso
     * já entra em todas as listas acima ao invés de ganhar uma regra própria.
     */
    public const FINALIDADE_TRATAMENTO = 'Tratamento';

    /**
     * Finalidades que só existem por causa de uma função biológica
     * exclusiva da fêmea (produzir leite ou pôr ovos) — nenhum macho, em
     * espécie nenhuma, pode ter uma dessas duas.
     */
    public const FINALIDADES_SO_FEMEA = ['Leite', 'Ovos'];

    public const SEXOS = ['Macho', 'Fêmea', 'Misto'];

    /**
     * Ordenha e parto são funções biológicas exclusivas da fêmea — só
     * fazem sentido em lotes que têm pelo menos uma fêmea, ou seja, sexo
     * "Fêmea" (só fêmeas) ou "Misto" (macho e fêmea juntos). Um lote
     * "Macho" nunca pode registrar nenhum dos dois.
     */
    public const SEXOS_COM_FEMEA = ['Fêmea', 'Misto'];

    public const IDADES_MEDIAS = ['0-6 meses', '6-12 meses', '1-2 anos', '2-3 anos', 'Acima de 3 anos'];

    public const STATUS = ['Ativo', 'Inativo', 'Encerrado'];

    /**
     * Doenças mais comuns por espécie, usadas no registro de doença do
     * lote (tela de Lotes). A UI sempre acrescenta a opção "Outra" no
     * final pra cobrir casos fora dessa lista — quando escolhida, quem
     * preenche digita o nome livremente.
     */
    public const DOENCAS_POR_ANIMAL = [
        'Bovino' => [
            'Febre Aftosa', 'Mastite', 'Brucelose', 'Verminose', 'Pneumonia',
            'Diarreia Neonatal', 'Tuberculose', 'Carbúnculo', 'Leptospirose',
            'Babesiose (Tristeza Parasitária)', 'Dermatofitose (Frieira)', 'Timpanismo',
        ],
        'Suíno' => [
            'Peste Suína Clássica', 'Erisipela', 'Verminose', 'Pneumonia',
            'Diarreia', 'Circovirose', 'Sarna', 'Rinite Atrófica', 'Parvovirose Suína',
        ],
        'Equino' => [
            'Anemia Infecciosa Equina', 'Cólica', 'Verminose', 'Tétano',
            'Influenza Equina', 'Garrotilho', 'Encefalomielite Equina', 'Laminite',
        ],
        'Avicultura' => [
            'Doença de Newcastle', 'Gripe Aviária', 'Coccidiose', 'Bronquite Infecciosa',
            'Salmonelose', 'Doença de Marek', 'Micoplasmose', 'Varíola Aviária',
        ],
        'Ovino' => [
            'Ectima Contagioso', 'Verminose', 'Linfadenite Caseosa', 'Febre Aftosa',
            'Mastite', 'Timpanismo', 'Sarna',
        ],
        'Caprino' => [
            'Linfadenite Caseosa', 'Verminose', 'Mastite', 'Ectima Contagioso',
            'Febre Aftosa', 'Artrite-Encefalite Caprina', 'Sarna',
        ],
    ];

    /** Valor especial do <select> de doenças — sinaliza que o nome real
     *  veio digitado num campo de texto à parte, não da lista fixa. */
    public const DOENCA_OUTRA = 'Outra';

    /**
     * Causas de óbito — a mesma lista serve pra qualquer espécie (não
     * depende do tipo de animal). "Doença" é tratada de forma especial no
     * controller: se o lote for de isolamento, pede quantos morreram de
     * cada doença ativa ali, em vez de só uma quantidade solta.
     */
    public const CAUSAS_MORTE = ['Doença', 'Corte', 'Acidente', 'Predador', 'Causa natural'];

    /** Valor especial do <select> de causa de morte — mesmo padrão do
     *  DOENCA_OUTRA, mas pro campo de causa. */
    public const CAUSA_MORTE_OUTRA = 'Outra';

    /**
     * Faixa de peso médio (kg) por espécie — pensada pro peso médio de UM
     * animal do lote, não do lote inteiro. Só um teto de sanidade dos
     * dados (não é um estudo zootécnico), fácil de recalibrar aqui.
     */
    public const FAIXA_PESO_POR_ANIMAL = [
        'Bovino' => ['min' => 15, 'max' => 1200],
        'Suíno' => ['min' => 1, 'max' => 400],
        'Equino' => ['min' => 20, 'max' => 1000],
        'Avicultura' => ['min' => 0.05, 'max' => 8],
        'Ovino' => ['min' => 2, 'max' => 150],
        'Caprino' => ['min' => 2, 'max' => 120],
    ];

    /** @return string[] */
    public static function tiposAnimais(): array
    {
        return array_keys(self::FINALIDADES_POR_ANIMAL);
    }

    /** Todas as finalidades possíveis, de qualquer espécie, sem repetir. */
    public static function todasFinalidades(): array
    {
        $todas = array_merge(...array_values(self::FINALIDADES_POR_ANIMAL));

        return array_values(array_unique($todas));
    }

    /**
     * Finalidades realmente válidas pro par tipo de animal + sexo. Parte da
     * lista da espécie e, se o sexo for exclusivamente macho, remove as
     * finalidades "só de fêmea" (leite/ovos) — um lote "Misto" mantém essas
     * opções, já que pode conter fêmeas.
     */
    public static function finalidadesPermitidas(?string $tipoAnimal, ?string $sexo): array
    {
        $permitidas = self::FINALIDADES_POR_ANIMAL[$tipoAnimal] ?? [];

        if ($sexo === 'Macho') {
            $permitidas = array_values(array_diff($permitidas, self::FINALIDADES_SO_FEMEA));
        }

        return $permitidas;
    }

    /** Doenças cadastradas pra espécie — vazio se a espécie não tiver uma
     *  lista própria (a opção "Outra" continua disponível mesmo assim). */
    public static function doencasPorAnimal(?string $tipoAnimal): array
    {
        return self::DOENCAS_POR_ANIMAL[$tipoAnimal] ?? [];
    }

    /** Todas as doenças cadastradas, de qualquer espécie, sem repetir —
     *  usado só pra distinguir "doença conhecida do sistema" de "outra". */
    public static function todasDoencas(): array
    {
        $todas = array_merge(...array_values(self::DOENCAS_POR_ANIMAL));

        return array_values(array_unique($todas));
    }

    /**
     * Só espécies que têm "Leite" como finalidade possível produzem leite de
     * verdade — hoje Bovino, Ovino e Caprino. Suíno, Equino e Avicultura
     * (galinha) nunca têm ordenha, e a lista se mantém sincronizada
     * automaticamente com FINALIDADES_POR_ANIMAL (não duplica a regra).
     */
    public static function tipoTemOrdenha(?string $tipoAnimal): bool
    {
        return in_array('Leite', self::FINALIDADES_POR_ANIMAL[$tipoAnimal] ?? [], true);
    }

    /** Todas as espécies que produzem leite (ver tipoTemOrdenha()). */
    public static function tiposComOrdenha(): array
    {
        return array_values(array_filter(
            self::tiposAnimais(),
            fn ($tipo) => self::tipoTemOrdenha($tipo)
        ));
    }

    /** Faixa de peso (['min' => ..., 'max' => ...]) da espécie, se definida. */
    public static function faixaPeso(?string $tipoAnimal): ?array
    {
        return self::FAIXA_PESO_POR_ANIMAL[$tipoAnimal] ?? null;
    }

    /** Se não houver faixa definida pra espécie, não bloqueia (evita travar
     *  o cadastro por uma espécie nova que ainda não foi calibrada). */
    public static function pesoValido(?string $tipoAnimal, mixed $peso): bool
    {
        $faixa = self::faixaPeso($tipoAnimal);

        if (!$faixa || !is_numeric($peso)) {
            return true;
        }

        return $peso >= $faixa['min'] && $peso <= $faixa['max'];
    }
}
